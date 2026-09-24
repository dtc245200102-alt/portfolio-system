<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

try {
    if ($path === '/healthz') {
        db()->query('SELECT 1');
        header('Content-Type: application/json; charset=utf-8');
        echo '{"status":"ok"}';
        exit;
    }

    if ($path === '/contact' && $method === 'POST') {
        verify_csrf();
        if (trim((string) ($_POST['website'] ?? '')) !== '') {
            set_flash('success', 'Cảm ơn bạn đã gửi lời nhắn.');
            redirect('/#contact');
        }

        $name = request_text('name', 120);
        $email = request_text('email', 190);
        $message = request_text('message', 5000);
        if ($name === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            set_flash('error', 'Vui lòng kiểm tra họ tên, email và nội dung tin nhắn.');
            redirect('/#contact');
        }

        $statement = db()->prepare('INSERT INTO contact_messages (name, email, message) VALUES (:name, :email, :message)');
        $statement->execute(['name' => $name, 'email' => $email, 'message' => $message]);
        app_log('info', 'contact_message_received');
        set_flash('success', 'Đã gửi tin nhắn. Cảm ơn bạn đã liên hệ!');
        redirect('/#contact');
    }

    if ($path === '/admin/login' && $method === 'POST') {
        verify_csrf();
        $now = time();
        $attempts = array_values(array_filter(
            $_SESSION['login_attempts'] ?? [],
            static fn ($timestamp): bool => is_int($timestamp) && $timestamp > $now - 900
        ));
        $_SESSION['login_attempts'] = $attempts;

        if (count($attempts) >= 5) {
            set_flash('error', 'Bạn đã thử đăng nhập quá nhiều lần. Vui lòng đợi 15 phút.');
            redirect('/admin');
        }

        $username = request_text('username', 100);
        $password = (string) ($_POST['password'] ?? '');
        $configuredUsername = getenv('ADMIN_USERNAME') ?: 'admin';
        $configuredPassword = read_secret('ADMIN_PASSWORD_FILE');
        if (hash_equals($configuredUsername, $username) && hash_equals($configuredPassword, $password)) {
            session_regenerate_id(true);
            $_SESSION['is_admin'] = true;
            $_SESSION['login_attempts'] = [];
            unset($_SESSION['csrf_token']);
            app_log('info', 'admin_login_succeeded');
            redirect('/admin');
        }

        $_SESSION['login_attempts'][] = $now;
        app_log('warning', 'admin_login_failed');
        set_flash('error', 'Tên đăng nhập hoặc mật khẩu chưa chính xác.');
        redirect('/admin');
    }

    if (str_starts_with($path, '/admin') && $method === 'POST') {
        require_admin();
        verify_csrf();

        if ($path === '/admin/logout') {
            $_SESSION = [];
            session_regenerate_id(true);
            set_flash('success', 'Bạn đã đăng xuất.');
            redirect('/admin');
        }

        if ($path === '/admin/profile') {
            $name = request_text('display_name', 120);
            $studentCode = request_text('student_code', 40);
            $role = request_text('role_title', 180);
            $tagline = request_text('tagline', 240);
            $about = request_text('about_text', 5000);
            $email = request_text('email', 190);
            $github = trim((string) ($_POST['github_url'] ?? ''));
            if ($name === '' || $studentCode === '' || $role === '' || $tagline === '' || $about === '') {
                set_flash('error', 'Họ tên, mã sinh viên, vai trò, câu giới thiệu và phần giới thiệu không được để trống.');
                redirect('/admin');
            }
            if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                set_flash('error', 'Địa chỉ email chưa đúng định dạng.');
                redirect('/admin');
            }
            $githubUrl = normalize_url($github);
            if ($github !== '' && $githubUrl === null) {
                set_flash('error', 'Liên kết GitHub phải bắt đầu bằng http:// hoặc https://.');
                redirect('/admin');
            }

            $statement = db()->prepare('UPDATE profile SET display_name = :name, student_code = :student_code, role_title = :role, tagline = :tagline, about_text = :about, email = :email, github_url = :github WHERE id = 1');
            $statement->execute(['name' => $name, 'student_code' => $studentCode, 'role' => $role, 'tagline' => $tagline, 'about' => $about, 'email' => $email, 'github' => $githubUrl ?? '']);
            set_flash('success', 'Đã cập nhật hồ sơ.');
            redirect('/admin');
        }

        if ($path === '/admin/skills') {
            $name = request_text('name', 100);
            $level = filter_var($_POST['level'] ?? null, FILTER_VALIDATE_INT);
            if ($name === '' || $level === false || $level < 0 || $level > 100) {
                set_flash('error', 'Nhập tên kỹ năng và mức độ từ 0 đến 100.');
                redirect('/admin');
            }
            $nextOrder = (int) db()->query('SELECT COALESCE(MAX(sort_order), 0) + 1 FROM skills')->fetchColumn();
            $statement = db()->prepare('INSERT INTO skills (name, level, sort_order) VALUES (:name, :level, :sort_order)');
            $statement->execute(['name' => $name, 'level' => $level, 'sort_order' => $nextOrder]);
            set_flash('success', 'Đã thêm kỹ năng.');
            redirect('/admin');
        }

        if ($path === '/admin/skills/delete') {
            $statement = db()->prepare('DELETE FROM skills WHERE id = :id');
            $statement->execute(['id' => filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT) ?: 0]);
            set_flash('success', 'Đã xóa kỹ năng.');
            redirect('/admin');
        }

        if ($path === '/admin/projects') {
            $title = request_text('title', 150);
            $description = request_text('description', 5000);
            $stack = request_text('tech_stack', 240);
            $url = trim((string) ($_POST['project_url'] ?? ''));
            $projectUrl = normalize_url($url);
            if ($title === '' || $description === '' || ($url !== '' && $projectUrl === null)) {
                set_flash('error', 'Nhập tên, mô tả dự án và dùng liên kết http:// hoặc https:// nếu có.');
                redirect('/admin');
            }
            $nextOrder = (int) db()->query('SELECT COALESCE(MAX(sort_order), 0) + 1 FROM projects')->fetchColumn();
            $statement = db()->prepare('INSERT INTO projects (title, description, tech_stack, project_url, sort_order) VALUES (:title, :description, :stack, :url, :sort_order)');
            $statement->execute(['title' => $title, 'description' => $description, 'stack' => $stack, 'url' => $projectUrl ?? '', 'sort_order' => $nextOrder]);
            set_flash('success', 'Đã thêm dự án.');
            redirect('/admin');
        }

        if ($path === '/admin/projects/delete') {
            $statement = db()->prepare('DELETE FROM projects WHERE id = :id');
            $statement->execute(['id' => filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT) ?: 0]);
            set_flash('success', 'Đã xóa dự án.');
            redirect('/admin');
        }

        if ($path === '/admin/messages/delete') {
            $statement = db()->prepare('DELETE FROM contact_messages WHERE id = :id');
            $statement->execute(['id' => filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT) ?: 0]);
            set_flash('success', 'Đã xóa tin nhắn.');
            redirect('/admin');
        }

        if ($path === '/admin/messages/clear') {
            db()->exec('DELETE FROM contact_messages');
            set_flash('success', 'Đã xóa tất cả tin nhắn.');
            redirect('/admin');
        }

        http_response_code(404);
        exit('Không tìm thấy chức năng quản trị.');
    }

    if ($path === '/admin' || $path === '/admin/') {
        if (!is_admin()) {
            $flash = take_flash();
            require APP_ROOT . '/views/admin/login.php';
            exit;
        }

        $profile = profile();
        $skills = db()->query('SELECT id, name, level FROM skills ORDER BY sort_order, id')->fetchAll();
        $projects = db()->query('SELECT id, title, description, tech_stack, project_url FROM projects ORDER BY sort_order, id')->fetchAll();
        $messages = db()->query('SELECT id, name, email, message, created_at FROM contact_messages ORDER BY created_at DESC, id DESC')->fetchAll();
        $flash = take_flash();
        require APP_ROOT . '/views/admin/dashboard.php';
        exit;
    }

    if ($path === '/' || $path === '') {
        $profile = profile();
        $skills = db()->query('SELECT id, name, level FROM skills ORDER BY sort_order, id')->fetchAll();
        $projects = db()->query('SELECT id, title, description, tech_stack, project_url FROM projects ORDER BY sort_order, id')->fetchAll();
        $flash = take_flash();
        require APP_ROOT . '/views/home.php';
        exit;
    }

    http_response_code(404);
    require APP_ROOT . '/views/not-found.php';
} catch (Throwable $error) {
    app_log('error', 'request_failed', ['path' => $path, 'type' => get_class($error)]);
    http_response_code(500);
    require APP_ROOT . '/views/error.php';
}
