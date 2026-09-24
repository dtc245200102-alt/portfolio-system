<?php
$initial = mb_strtoupper(mb_substr((string) $profile['display_name'], 0, 1, 'UTF-8'), 'UTF-8');
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
<body>
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
                <button class="icon-button" id="theme-toggle" type="button" aria-label="Chuyển giao diện sáng tối"><span aria-hidden="true">☼</span></button>
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
                    <svg class="lanyard-loop" viewBox="0 0 260 230" focusable="false">
                        <defs><linearGradient id="lanyard-color" x1="0" x2="1" y1="0" y2="1"><stop offset="0" stop-color="var(--accent)"/><stop offset="1" stop-color="var(--accent-2)"/></linearGradient></defs>
                        <path class="lanyard-shadow" d="M22 -16 C22 82 59 137 130 153 C201 137 238 82 238 -16"/>
                        <path class="lanyard-ribbon" d="M22 -16 C22 82 59 137 130 153 C201 137 238 82 238 -16"/>
                    </svg>
                    <span class="lanyard-clip"></span>
                </div>
                <div class="profile-card" data-tilt>
                    <div class="profile-card-top"><span class="card-spark" aria-hidden="true">✳</span></div>
                    <div class="avatar"><?php if ($avatarPath !== ''): ?><img src="<?= e($avatarPath) ?>" alt="Ảnh đại diện của <?= e($profile['display_name']) ?>"><?php else: ?><span aria-hidden="true"><?= e($initial) ?></span><?php endif; ?></div>
                    <p class="card-name"><?= e($profile['display_name']) ?></p>
                    <p class="card-role"><?= e($profile['role_title']) ?></p>
                    <div class="card-divider"></div>
                    <div class="card-detail card-detail-id"><span>MÃ SINH VIÊN</span><strong><?= e($profile['student_code']) ?></strong></div>
                </div>
            </div>
        </section>

        <section class="content-section" id="about">
            <div class="section-wrap split-section">
                <div><p class="eyebrow">01 / Giới thiệu</p><h2 class="section-title">Một chút về tôi<span class="brand-dot">.</span></h2></div>
                <div class="about-copy"><p><?= nl2br(e($profile['about_text'])) ?></p>
                    <?php if ($facts): ?><div class="about-facts"><?php foreach ($facts as $index => $fact): ?><article><strong><?= e(sprintf('%02d', $index + 1)) ?></strong><span><?= e($fact['content']) ?></span></article><?php endforeach; ?></div><?php endif; ?>
                </div>
            </div>
        </section>

        <section class="content-section education-section" id="education">
            <div class="section-wrap">
                <p class="eyebrow">02 / Học vấn</p><h2 class="section-title">Hành trình học tập<span class="brand-dot">.</span></h2>
                <?php if (trim((string) ($profile['school_name'] ?? '')) !== '' || trim((string) ($profile['education_details'] ?? '')) !== ''): ?>
                    <article class="education-card"><div class="education-icon" aria-hidden="true">H</div><div><p class="education-label">TRƯỜNG HỌC</p><h3><?= e($profile['school_name'] ?: 'Tên trường chưa cập nhật') ?></h3><p><?= nl2br(e($profile['education_details'] ?: 'Nội dung học tập sẽ được cập nhật.')) ?></p></div></article>
                <?php else: ?><p class="empty-state">Thông tin học vấn sẽ xuất hiện tại đây sau khi bạn cập nhật trong trang quản trị.</p><?php endif; ?>
            </div>
        </section>

        <section class="content-section section-tint" id="skills">
            <div class="section-wrap">
                <p class="eyebrow">03 / Năng lực</p><h2 class="section-title">Kỹ năng đang phát triển<span class="brand-dot">.</span></h2>
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
                <p class="eyebrow">04 / Sản phẩm</p><h2 class="section-title">Một số dự án<span class="brand-dot">.</span></h2>
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
                <div><p class="eyebrow">05 / Kết nối</p><h2 class="section-title">Bạn có ý tưởng?<br>Cùng trao đổi nhé<span class="brand-dot">.</span></h2><p class="contact-note">Gửi lời nhắn, thông tin sẽ được lưu an toàn trong hệ thống để tôi phản hồi sau.</p><?php if ($profile['email']): ?><a class="text-link" href="mailto:<?= e($profile['email']) ?>"><?= e($profile['email']) ?> <span aria-hidden="true">↗</span></a><?php endif; ?></div>
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
