<!doctype html>
<html lang="vi" class="dark">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Đăng nhập quản trị · Portfolio</title><link rel="stylesheet" href="/assets/css/site.css"></head>
<body class="admin-body">
    <main class="admin-login-wrap">
        <a class="brand" href="/">← <span>Về trang chủ</span></a>
        <section class="admin-panel login-panel">
            <p class="eyebrow">Khu vực quản trị</p><h1>Đăng nhập</h1><p class="muted">Quản lý hồ sơ, kỹ năng, dự án và tin nhắn liên hệ.</p>
            <?php if ($flash): ?><div class="flash flash-<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></div><?php endif; ?>
            <form action="/admin/login" method="post" class="admin-form">
                <?= csrf_field() ?>
                <label for="username">Tên đăng nhập</label><input id="username" name="username" autocomplete="username" autocapitalize="off" spellcheck="false" required>
                <label for="password">Mật khẩu</label><input id="password" type="password" name="password" autocomplete="current-password" required>
                <button class="button" type="submit">Đăng nhập <span aria-hidden="true">→</span></button>
            </form>
        </section>
    </main>
</body>
</html>
