<!doctype html>
<html lang="vi" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập quản trị · Portfolio</title>
    <link rel="stylesheet" href="/assets/css/site.css">
    <script src="/assets/js/site.js" defer></script>
</head>
<body class="admin-body admin-login-body">
    <main class="admin-login-wrap">
        <div class="login-topbar">
            <a class="brand" href="/" aria-label="Hồ sơ cá nhân, trang chủ">
                <span class="brand-mark">H</span><span>Hồ sơ cá nhân</span>
            </a>
            <div class="login-top-actions">
                <button class="icon-button theme-toggle" id="theme-toggle" type="button" aria-label="Chuyển giao diện sáng tối" aria-pressed="true">
                    <svg class="theme-glyph theme-glyph-sun" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42 1.42"/></svg>
                    <svg class="theme-glyph theme-glyph-moon" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.4 15.3A8.7 8.7 0 0 1 8.7 3.6 8.8 8.8 0 1 0 20.4 15.3Z"/></svg>
                </button>
                <a class="login-home-link" href="/"><span aria-hidden="true">←</span> Về trang chủ</a>
            </div>
        </div>

        <section class="admin-panel login-panel" aria-labelledby="login-title">
            <div class="login-showcase">
                <div class="login-showcase-main">
                    <div class="login-emblem" aria-hidden="true">
                        <svg viewBox="0 0 48 48"><path d="M24 5 39 11v11c0 10-6.4 16.5-15 21-8.6-4.5-15-11-15-21V11L24 5Z"/><path d="m17 24 5 5 10-11"/></svg>
                    </div>
                    <p class="eyebrow">Không gian của bạn</p>
                    <h2>Quản lý<br><span class="gradient-text">portfolio</span></h2>
                    <p class="login-showcase-copy">Cập nhật hồ sơ, chia sẻ hành trình học tập và giữ mọi dự án của bạn luôn mới.</p>
                </div>
                <div class="login-feature-list" aria-label="Nội dung quản lý">
                    <span><i aria-hidden="true">01</i> Hồ sơ cá nhân</span>
                    <span><i aria-hidden="true">02</i> Học vấn và kỹ năng</span>
                    <span><i aria-hidden="true">03</i> Dự án và liên hệ</span>
                </div>
                <p class="login-showcase-foot"><span class="login-live-dot"></span> Góc làm việc dành riêng cho bạn</p>
            </div>

            <div class="login-form-side">
                <p class="eyebrow">Khu vực quản trị</p>
                <h1 id="login-title">Đăng nhập</h1>
                <p class="muted login-description">Nhập tài khoản của bạn để tiếp tục.</p>
                <?php if ($flash): ?><div class="flash flash-<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></div><?php endif; ?>
                <form action="/admin/login" method="post" class="admin-form login-form">
                    <?= csrf_field() ?>
                    <label for="username">Tên đăng nhập</label>
                    <input id="username" name="username" autocomplete="username" autocapitalize="off" spellcheck="false" required>
                    <label for="password">Mật khẩu</label>
                    <input id="password" type="password" name="password" autocomplete="current-password" required>
                    <button class="button login-submit" type="submit">Đăng nhập <span aria-hidden="true">→</span></button>
                </form>
                <p class="login-form-foot"><span aria-hidden="true">✦</span> Sau khi đăng nhập, bạn có thể cập nhật hồ sơ và nội dung hiển thị.</p>
            </div>
        </section>
    </main>
</body>
</html>
