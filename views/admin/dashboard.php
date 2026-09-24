<!doctype html>
<html lang="vi" class="dark">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Quản trị nội dung · Portfolio</title><link rel="stylesheet" href="/assets/css/site.css"></head>
<body class="admin-body">
    <header class="admin-header"><a class="brand" href="/"><span class="brand-mark">H</span><span>Hồ sơ cá nhân</span><span class="admin-label">Quản trị</span></a><div class="nav-actions"><a class="button button-quiet button-small" href="/" target="_blank" rel="noopener noreferrer">Xem website ↗</a><form action="/admin/logout" method="post"><?= csrf_field() ?><button class="button button-small" type="submit">Đăng xuất</button></form></div></header>
    <main class="admin-main">
        <div class="admin-title-row"><div><p class="eyebrow">Bảng điều khiển</p><h1>Quản lý nội dung</h1><p class="muted">Thay đổi tại đây sẽ được lưu vào MySQL và hiển thị trên portfolio.</p></div></div>
        <?php if ($flash): ?><div class="flash flash-<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></div><?php endif; ?>

        <section class="admin-panel"><div class="panel-heading"><div><p class="eyebrow">01 / Hồ sơ</p><h2>Thông tin cá nhân</h2></div></div>
            <div class="avatar-manager">
                <?php if (!empty($profile['avatar_path'])): ?><img class="avatar-preview" src="<?= e($profile['avatar_path']) ?>" alt="Ảnh đại diện hiện tại"><?php else: ?><div class="avatar-preview avatar-placeholder" aria-hidden="true"><?= e(mb_strtoupper(mb_substr((string) $profile['display_name'], 0, 1, 'UTF-8'), 'UTF-8')) ?></div><?php endif; ?>
                <form action="/admin/avatar" method="post" enctype="multipart/form-data" class="admin-form avatar-form">
                    <?= csrf_field() ?>
                    <label for="avatar-upload">Ảnh đại diện (JPG, PNG hoặc WebP; tối đa 2 MB)</label>
                    <input id="avatar-upload" name="avatar" type="file" accept="image/jpeg,image/png,image/webp" required>
                    <div class="avatar-actions"><button class="button button-small" type="submit">Cập nhật ảnh</button><?php if (!empty($profile['avatar_path'])): ?><button class="button button-danger button-small" type="submit" name="remove_avatar" value="1" formnovalidate>Xóa ảnh</button><?php endif; ?></div>
                </form>
            </div>
            <form action="/admin/profile" method="post" class="admin-form form-grid">
                <?= csrf_field() ?>
                <div><label for="display_name">Họ và tên</label><input id="display_name" name="display_name" maxlength="120" value="<?= e($profile['display_name']) ?>" required></div>
                <div><label for="student_code">Mã sinh viên</label><input id="student_code" name="student_code" maxlength="40" value="<?= e($profile['student_code']) ?>" required></div>
                <div class="field-wide"><label for="school_name">Tên trường</label><input id="school_name" name="school_name" maxlength="180" value="<?= e($profile['school_name'] ?? '') ?>" placeholder="Nhập tên trường của bạn"></div>
                <div class="field-wide"><label for="education_details">Những nội dung đã học tại trường</label><textarea id="education_details" name="education_details" rows="4" maxlength="5000" placeholder="Ví dụ: các môn học, kiến thức chuyên ngành, kỹ năng và hoạt động thực hành..."><?= e($profile['education_details'] ?? '') ?></textarea></div>
                <div><label for="role_title">Vai trò / ngành học</label><input id="role_title" name="role_title" maxlength="180" value="<?= e($profile['role_title']) ?>" required></div>
                <div class="field-wide"><label for="tagline">Câu giới thiệu ngắn</label><input id="tagline" name="tagline" maxlength="240" value="<?= e($profile['tagline']) ?>" required></div>
                <div class="field-wide"><label for="about_text">Giới thiệu bản thân</label><textarea id="about_text" name="about_text" rows="5" maxlength="5000" required><?= e($profile['about_text']) ?></textarea></div>
                <div><label for="email">Email hiển thị</label><input id="email" name="email" type="email" maxlength="190" value="<?= e($profile['email']) ?>"></div>
                <div><label for="github_url">Liên kết GitHub</label><input id="github_url" name="github_url" type="url" maxlength="500" value="<?= e($profile['github_url']) ?>" placeholder="https://github.com/ten-cua-ban"></div>
                <div class="field-wide"><button class="button" type="submit">Lưu hồ sơ</button></div>
            </form>
        </section>

        <section class="admin-panel"><div class="panel-heading"><div><p class="eyebrow">02 / Giới thiệu</p><h2>Các mục nổi bật</h2><p class="muted">Sửa, thêm hoặc xóa những thẻ hiển thị trong phần Giới thiệu. Số thứ tự trên trang sẽ tự cập nhật.</p></div></div>
            <div class="facts-admin-list">
                <?php foreach ($facts as $index => $fact): ?>
                    <div class="fact-admin-row">
                        <form action="/admin/facts/update" method="post" class="admin-form fact-edit-form"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $fact['id'] ?>"><div><label for="fact-<?= (int) $fact['id'] ?>">Mục <?= e(sprintf('%02d', $index + 1)) ?></label><input id="fact-<?= (int) $fact['id'] ?>" name="content" maxlength="180" value="<?= e($fact['content']) ?>" required></div><button class="button button-quiet button-small" type="submit">Lưu</button></form>
                        <form action="/admin/facts/delete" method="post" class="fact-delete-form" onsubmit="return confirm('Bạn có chắc muốn xóa mục giới thiệu này?')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $fact['id'] ?>"><button class="button button-danger button-small" type="submit">Xóa</button></form>
                    </div>
                <?php endforeach; ?>
                <?php if (!$facts): ?><p class="muted">Chưa có mục nào. Hãy thêm mục giới thiệu bên dưới.</p><?php endif; ?>
            </div>
            <form action="/admin/facts" method="post" class="admin-form inline-form facts-add-form"><?= csrf_field() ?><div><label for="new-fact">Nội dung mục mới</label><input id="new-fact" name="content" maxlength="180" placeholder="Ví dụ: Thích học hỏi điều mới" required></div><button class="button" type="submit">Thêm mục</button></form>
        </section>

        <section class="admin-panel"><div class="panel-heading"><div><p class="eyebrow">03 / Năng lực</p><h2>Kỹ năng</h2></div></div>
            <div class="admin-list"><?php foreach ($skills as $skill): ?><div class="admin-list-row"><span><strong><?= e($skill['name']) ?></strong><small><?= (int) $skill['level'] ?>% thành thạo</small></span><form action="/admin/skills/delete" method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $skill['id'] ?>"><button class="button button-danger button-small" type="submit">Xóa</button></form></div><?php endforeach; ?><?php if (!$skills): ?><p class="muted">Chưa có kỹ năng nào.</p><?php endif; ?></div>
            <form action="/admin/skills" method="post" class="admin-form inline-form"><?= csrf_field() ?><div><label for="skill-name">Tên kỹ năng</label><input id="skill-name" name="name" maxlength="100" required></div><div><label for="skill-level">Mức độ (0–100)</label><input id="skill-level" type="number" name="level" min="0" max="100" value="50" required></div><button class="button" type="submit">Thêm kỹ năng</button></form>
        </section>

        <section class="admin-panel"><div class="panel-heading"><div><p class="eyebrow">04 / Sản phẩm</p><h2>Dự án</h2></div></div>
            <div class="admin-list"><?php foreach ($projects as $project): ?><div class="admin-list-row"><span><strong><?= e($project['title']) ?></strong><small><?= e($project['tech_stack']) ?></small></span><form action="/admin/projects/delete" method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $project['id'] ?>"><button class="button button-danger button-small" type="submit">Xóa</button></form></div><?php endforeach; ?><?php if (!$projects): ?><p class="muted">Chưa có dự án nào.</p><?php endif; ?></div>
            <form action="/admin/projects" method="post" class="admin-form form-grid"><?= csrf_field() ?><div><label for="project-title">Tên dự án</label><input id="project-title" name="title" maxlength="150" required></div><div><label for="project-stack">Công nghệ</label><input id="project-stack" name="tech_stack" maxlength="240" placeholder="PHP · MySQL · Docker"></div><div class="field-wide"><label for="project-description">Mô tả</label><textarea id="project-description" name="description" rows="3" maxlength="5000" required></textarea></div><div class="field-wide"><label for="project-url">Liên kết dự án (không bắt buộc)</label><input id="project-url" name="project_url" type="url" maxlength="500" placeholder="https://..."></div><div class="field-wide"><button class="button" type="submit">Thêm dự án</button></div></form>
        </section>

        <section class="admin-panel"><div class="panel-heading"><div><p class="eyebrow">05 / Hộp thư</p><h2>Tin nhắn liên hệ <span class="count-badge"><?= count($messages) ?></span></h2></div></div>
            <?php if ($messages): ?><div class="messages-list"><?php foreach ($messages as $message): ?><article class="message-card"><div class="message-top"><div><h3><?= e($message['name']) ?></h3><a href="mailto:<?= e($message['email']) ?>"><?= e($message['email']) ?></a></div><time datetime="<?= e($message['created_at']) ?>"><?= e(date('d/m/Y H:i', strtotime($message['created_at']))) ?></time></div><p><?= nl2br(e($message['message'])) ?></p><form action="/admin/messages/delete" method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $message['id'] ?>"><button class="text-button" type="submit">Xóa tin nhắn</button></form></article><?php endforeach; ?></div><form action="/admin/messages/clear" method="post" class="clear-messages-form"><?= csrf_field() ?><button class="button button-danger button-small" type="submit">Xóa tất cả tin nhắn</button></form><?php else: ?><p class="empty-state">Chưa có tin nhắn nào. Tin nhắn từ biểu mẫu liên hệ sẽ xuất hiện ở đây.</p><?php endif; ?>
        </section>
        <p class="admin-footer-note">Trang này dùng phiên đăng nhập, token chống CSRF và kết nối MySQL qua tài khoản ứng dụng giới hạn quyền.</p>
    </main>
</body>
</html>
