<?php
$initial = mb_strtoupper(mb_substr((string) $profile['display_name'], 0, 1, 'UTF-8'), 'UTF-8');
?>
<!doctype html>
<html lang="vi" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= e($profile['tagline']) ?>">
    <title><?= e($profile['display_name']) ?> · Portfolio cá nhân</title>
    <link rel="stylesheet" href="/assets/css/site.css">
    <script src="/assets/js/site.js" defer></script>
</head>
<body>
    <a class="skip-link" href="#main">Đi đến nội dung</a>
    <header class="site-header">
        <nav class="nav-shell" aria-label="Điều hướng chính">
            <a class="brand" href="/" aria-label="Portfolio, trang chủ"><span class="brand-mark">P</span> Portfolio<span class="brand-dot">.</span></a>
            <div class="nav-links">
                <a href="#about">Giới thiệu</a>
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
                <p class="eyebrow"><span class="eyebrow-line"></span> Portfolio cá nhân</p>
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
                <div class="profile-card">
                    <div class="profile-card-top"><span class="mini-label">THÔNG TIN CÁ NHÂN</span><span class="card-spark" aria-hidden="true">✳</span></div>
                    <div class="avatar" aria-hidden="true"><?= e($initial) ?></div>
                    <p class="card-name"><?= e($profile['display_name']) ?></p>
                    <p class="card-role"><?= e($profile['role_title']) ?></p>
                    <div class="card-divider"></div>
                    <div class="card-detail"><span>ĐỊNH HƯỚNG</span><strong>Web · Hệ thống · DevOps</strong></div>
                    <div class="card-detail"><span>TRẠNG THÁI</span><strong><span class="status-dot"></span> Đang phát triển</strong></div>
                </div>
                <span class="floating-tag tag-code">&lt;build /&gt;</span>
                <span class="floating-tag tag-stack">PHP · MySQL · Docker</span>
            </div>
        </section>

        <section class="content-section" id="about">
            <div class="section-wrap split-section">
                <div><p class="eyebrow">01 / Giới thiệu</p><h2 class="section-title">Một chút về tôi<span class="brand-dot">.</span></h2></div>
                <div class="about-copy"><p><?= nl2br(e($profile['about_text'])) ?></p>
                    <div class="about-facts"><div><strong>01</strong><span>Luôn tò mò</span></div><div><strong>02</strong><span>Học qua thực hành</span></div><div><strong>03</strong><span>Chia sẻ điều hữu ích</span></div></div>
                </div>
            </div>
        </section>

        <section class="content-section section-tint" id="skills">
            <div class="section-wrap">
                <p class="eyebrow">02 / Năng lực</p><h2 class="section-title">Kỹ năng đang phát triển<span class="brand-dot">.</span></h2>
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
                <p class="eyebrow">03 / Sản phẩm</p><h2 class="section-title">Một số dự án<span class="brand-dot">.</span></h2>
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
                <div><p class="eyebrow">04 / Kết nối</p><h2 class="section-title">Bạn có ý tưởng?<br>Cùng trao đổi nhé<span class="brand-dot">.</span></h2><p class="contact-note">Gửi lời nhắn, thông tin sẽ được lưu an toàn trong hệ thống để tôi phản hồi sau.</p><?php if ($profile['email']): ?><a class="text-link" href="mailto:<?= e($profile['email']) ?>"><?= e($profile['email']) ?> <span aria-hidden="true">↗</span></a><?php endif; ?></div>
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

    <footer class="site-footer"><div class="section-wrap footer-inner"><a class="brand" href="/">Portfolio<span class="brand-dot">.</span></a><span>Được xây dựng bằng PHP, MySQL và Docker Compose.</span><a href="/admin">Trang quản trị</a></div></footer>
</body>
</html>
