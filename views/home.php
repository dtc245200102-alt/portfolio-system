<?php
$avatarPath = (string) ($profile['avatar_path'] ?? '');
?>
<!doctype html>
<html lang="vi" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= e($profile['tagline']) ?>">
    <title><?= e($profile['display_name']) ?> · Hồ sơ cá nhân</title>
    <link rel="stylesheet" href="/assets/css/site.css">
    <script src="/assets/js/site.js" defer></script>
</head>
<body class="portfolio-body">
    <a class="skip-link" href="#main">Đi đến nội dung</a>
    <header class="site-header">
        <nav class="nav-shell" aria-label="Điều hướng chính">
            <a class="brand" href="/#home" aria-label="Hồ sơ cá nhân, trang chủ"><span class="brand-mark">H</span><span>Hồ sơ cá nhân</span></a>
            <div class="nav-links">
                <a href="#home">Home</a>
                <a href="#about">Giới thiệu</a>
                <a href="#education">Học vấn</a>
                <a href="#skills">Kỹ năng</a>
                <a href="#projects">Dự án</a>
                <a href="#contact">Liên hệ</a>
            </div>
            <div class="nav-actions">
                <button class="icon-button theme-toggle" id="theme-toggle" type="button" aria-label="Chuyển giao diện sáng tối" aria-pressed="true">
                    <svg class="theme-glyph theme-glyph-sun" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"/></svg>
                    <svg class="theme-glyph theme-glyph-moon" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.4 15.3A8.7 8.7 0 0 1 8.7 3.6 8.8 8.8 0 1 0 20.4 15.3Z"/></svg>
                </button>
                <a class="button button-small" href="/admin">Quản trị</a>
            </div>
        </nav>
    </header>

    <main id="main">
        <?php if ($flash): ?>
            <div class="flash flash-<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></div>
        <?php endif; ?>

        <section class="hero section-wrap" id="home">
            <div class="hero-copy">
                <h1>Xin chào, tôi là<br><span class="gradient-text"><?= e($profile['display_name']) ?></span></h1>
                <h2><?= e($profile['role_title']) ?></h2>
                <p class="hero-intro"><?= e($profile['tagline']) ?></p>
                <div class="hero-actions">
                    <a class="button" href="#contact">Trao đổi với tôi <span aria-hidden="true">↗</span></a>
                    <?php if (!empty($profile['github_url'])): ?>
                        <a class="button button-quiet" href="<?= e($profile['github_url']) ?>" target="_blank" rel="noopener noreferrer">GitHub <span aria-hidden="true">↗</span></a>
                    <?php endif; ?>
                </div>
                <div class="hero-meta"><span class="status-dot"></span> Sẵn sàng kết nối và học hỏi</div>
            </div>
            <div class="hero-art" aria-label="Thẻ giới thiệu <?= e($profile['display_name']) ?>">
                <div class="orbit orbit-one"></div><div class="orbit orbit-two"></div>
                <div class="lanyard" aria-hidden="true">
                    <svg class="lanyard-loop" viewBox="0 0 120 190" focusable="false">
                        <defs><linearGradient id="lanyard-webbing" x1="0" x2="1" y1="0" y2="0"><stop offset="0" stop-color="#080d17"/><stop offset=".48" stop-color="#283244"/><stop offset="1" stop-color="#0a0f19"/></linearGradient></defs>
                        <path class="lanyard-shadow" d="M60 -18 C58 35 63 82 60 150"/>
                        <path class="lanyard-ribbon" d="M60 -18 C58 35 63 82 60 150"/>
                        <path class="lanyard-stitch" d="M54 -12 C53 38 58 83 55 143 M66 -12 C65 38 70 83 67 143"/>
                        <path class="lanyard-center-seam" d="M60 -12 C58 38 63 83 60 143"/>
                    </svg>
                    <span class="lanyard-clip"></span>
                </div>
                <div class="profile-card" data-tilt aria-label="Ảnh hồ sơ">
                    <?php if ($avatarPath !== ''): ?><img class="profile-card-image" src="<?= e($avatarPath) ?>" alt="Ảnh hồ sơ"><?php else: ?><div class="profile-card-placeholder" aria-hidden="true"></div><?php endif; ?>
                </div>
            </div>
        </section>

        <section class="content-section" id="about">
            <div class="section-wrap split-section">
                <div><p class="eyebrow">01 / Giới thiệu</p><h2 class="section-title">Một chút về tôi</h2></div>
                <div class="about-copy"><p><?= nl2br(e($profile['about_text'])) ?></p>
                    <?php if ($facts): ?><div class="about-facts"><?php foreach ($facts as $index => $fact): ?><article><strong><?= e(sprintf('%02d', $index + 1)) ?></strong><span><?= e($fact['content']) ?></span></article><?php endforeach; ?></div><?php endif; ?>
                </div>
            </div>
        </section>

        <section class="content-section education-section" id="education">
            <div class="section-wrap">
                <p class="eyebrow">02 / Học vấn</p><h2 class="section-title">Hành trình học tập</h2>
                <?php if (trim((string) ($profile['school_name'] ?? '')) !== '' || trim((string) ($profile['education_details'] ?? '')) !== ''): ?>
                    <article class="education-card"><div class="education-icon" aria-hidden="true">H</div><div><p class="education-label">TRƯỜNG HỌC</p><h3><?= e($profile['school_name'] ?: 'Tên trường chưa cập nhật') ?></h3><p><?= nl2br(e($profile['education_details'] ?: 'Nội dung học tập sẽ được cập nhật.')) ?></p></div></article>
                <?php else: ?><p class="empty-state">Thông tin học vấn sẽ xuất hiện tại đây sau khi bạn cập nhật trong trang quản trị.</p><?php endif; ?>
            </div>
        </section>

        <section class="content-section section-tint" id="skills">
            <div class="section-wrap">
                <p class="eyebrow">03 / Năng lực</p><h2 class="section-title">Kỹ năng đang phát triển</h2>
                <?php if ($skills): ?>
                    <div class="skills-grid">
                        <?php foreach ($skills as $skill): ?>
                            <article class="skill-card"><div class="skill-heading"><h3><?= e($skill['name']) ?></h3><span><?= (int) $skill['level'] ?>%</span></div><progress max="100" value="<?= (int) $skill['level'] ?>" aria-label="<?= e($skill['name']) ?>: <?= (int) $skill['level'] ?> phần trăm"></progress></article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?><p class="empty-state">Kỹ năng sẽ được cập nhật sớm.</p><?php endif; ?>
            </div>
        </section>

        <section class="content-section" id="projects">
            <div class="section-wrap">
                <p class="eyebrow">04 / Sản phẩm</p><h2 class="section-title">Một số dự án</h2>
                <?php if ($projects): ?>
                    <div class="projects-grid">
                        <?php foreach ($projects as $index => $project): ?>
                            <article class="project-card"><div class="project-index">0<?= $index + 1 ?></div><h3><?= e($project['title']) ?></h3><p><?= e($project['description']) ?></p><?php if ($project['tech_stack']): ?><div class="tech-list"><?= e($project['tech_stack']) ?></div><?php endif; ?><?php if ($project['project_url']): ?><a class="text-link" href="<?= e($project['project_url']) ?>" target="_blank" rel="noopener noreferrer">Xem dự án <span aria-hidden="true">↗</span></a><?php endif; ?></article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?><p class="empty-state">Dự án sẽ được cập nhật sớm.</p><?php endif; ?>
            </div>
        </section>

        <section class="content-section contact-section" id="contact">
            <div class="section-wrap contact-layout">
                <div><p class="eyebrow">05 / Kết nối</p><h2 class="section-title">Bạn có ý tưởng?<br>Cùng trao đổi nhé</h2><p class="contact-note">Gửi lời nhắn, thông tin sẽ được lưu an toàn trong hệ thống để tôi phản hồi sau.</p><?php if ($profile['email']): ?><a class="text-link" href="mailto:<?= e($profile['email']) ?>"><?= e($profile['email']) ?> <span aria-hidden="true">↗</span></a><?php endif; ?></div>
                <form class="contact-form" action="/contact" method="post">
                    <?= csrf_field() ?>
                    <div class="honeypot" aria-hidden="true"><label>Website<input name="website" tabindex="-1" autocomplete="off"></label></div>
                    <label for="contact-name">Họ và tên</label><input id="contact-name" name="name" maxlength="120" autocomplete="name" placeholder="Nguyễn Văn A" required>
                    <label for="contact-email">Email</label><input id="contact-email" name="email" type="email" maxlength="190" autocomplete="email" placeholder="ban@example.com" required>
                    <label for="contact-message">Lời nhắn</label><textarea id="contact-message" name="message" rows="5" maxlength="5000" placeholder="Bạn muốn trao đổi về điều gì?" required></textarea>
                    <button class="button" type="submit">Gửi lời nhắn <span aria-hidden="true">→</span></button>
                </form>
            </div>
        </section>
    </main>

    <footer class="site-footer"><div class="section-wrap footer-inner"><a class="brand" href="/#home"><span class="brand-mark">H</span><span>Hồ sơ cá nhân</span></a><span>Được xây dựng bằng PHP, MySQL và Docker Compose.</span><a href="/admin">Trang quản trị</a></div></footer>
</body>
</html>
