<?php
declare(strict_types=1);

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function is_admin(): bool
{
    return ($_SESSION['is_admin'] ?? false) === true;
}

function redirect(string $path): never
{
    header('Location: ' . $path, true, 303);
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $posted = $_POST['csrf_token'] ?? '';
    $known = $_SESSION['csrf_token'] ?? '';
    if (!is_string($posted) || !is_string($known) || $known === '' || !hash_equals($known, $posted)) {
        http_response_code(400);
        exit('Yêu cầu không hợp lệ. Hãy tải lại trang và thử lại.');
    }
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function take_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return is_array($flash) ? $flash : null;
}

function require_admin(): void
{
    if (!is_admin()) {
        set_flash('error', 'Vui lòng đăng nhập để tiếp tục.');
        redirect('/admin');
    }
}

function app_log(string $level, string $event, array $context = []): void
{
    $record = [
        'time' => gmdate(DATE_ATOM),
        'level' => $level,
        'event' => $event,
        'context' => $context,
    ];
    $line = json_encode($record, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    error_log(($line === false ? '{"level":"error","event":"log_encode_failed"}' : $line) . PHP_EOL,
        3,
        '/var/log/portfolio/app/application.log'
    );
}

function normalize_url(string $value): ?string
{
    $value = trim($value);
    if ($value === '') {
        return null;
    }

    if (!filter_var($value, FILTER_VALIDATE_URL)) {
        return null;
    }

    $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));
    return in_array($scheme, ['http', 'https'], true) ? $value : null;
}

function request_text(string $key, int $maxLength): string
{
    $value = trim((string) ($_POST[$key] ?? ''));
    return mb_substr($value, 0, $maxLength, 'UTF-8');
}

function profile(): array
{
    $result = db()->query('SELECT * FROM profile WHERE id = 1')->fetch();
    return $result ?: [
        'display_name' => 'Nguyễn Văn Khánh',
        'student_code' => 'DTC245200102',
        'school_name' => '',
        'education_details' => '',
        'role_title' => 'Sinh viên',
        'tagline' => 'Portfolio cá nhân và các dự án thực hành của tôi.',
        'about_text' => 'Tôi là Nguyễn Văn Khánh, sinh viên mã số DTC245200102.',
        'email' => '',
        'github_url' => '',
        'avatar_path' => '',
    ];
}
