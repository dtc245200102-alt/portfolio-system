CREATE DATABASE IF NOT EXISTS portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE portfolio;

CREATE TABLE IF NOT EXISTS profile (
    id TINYINT UNSIGNED NOT NULL PRIMARY KEY,
    display_name VARCHAR(120) NOT NULL,
    student_code VARCHAR(40) NOT NULL DEFAULT '',
    role_title VARCHAR(180) NOT NULL,
    tagline VARCHAR(240) NOT NULL,
    about_text TEXT NOT NULL,
    email VARCHAR(190) NOT NULL DEFAULT '',
    github_url VARCHAR(500) NOT NULL DEFAULT '',
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS skills (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    level TINYINT UNSIGNED NOT NULL DEFAULT 50,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS projects (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    tech_stack VARCHAR(240) NOT NULL DEFAULT '',
    project_url VARCHAR(500) NOT NULL DEFAULT '',
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS contact_messages (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_contact_created_at (created_at)
) ENGINE=InnoDB;

INSERT IGNORE INTO profile (id, display_name, student_code, role_title, tagline, about_text, email, github_url)
VALUES (1, 'Nguyễn Văn Khánh', 'DTC245200102', 'Sinh viên',
        'Portfolio cá nhân và các dự án thực hành của tôi.',
        'Tôi là Nguyễn Văn Khánh, sinh viên mã số DTC245200102. Đây là portfolio cá nhân để giới thiệu thông tin, kỹ năng và các dự án thực hành môn Triển khai và Quản trị Hệ thống Phần mềm.',
        '', '');

INSERT INTO skills (name, level, sort_order)
SELECT 'PHP', 65, 1 WHERE NOT EXISTS (SELECT 1 FROM skills);
INSERT INTO skills (name, level, sort_order)
SELECT 'MySQL', 60, 2 WHERE (SELECT COUNT(*) FROM skills) = 1;
INSERT INTO skills (name, level, sort_order)
SELECT 'Docker', 55, 3 WHERE (SELECT COUNT(*) FROM skills) = 2;

INSERT INTO projects (title, description, tech_stack, project_url, sort_order)
SELECT 'Portfolio cá nhân', 'Website giới thiệu bản thân, quản lý nội dung bằng trang quản trị và lưu dữ liệu trong MySQL.', 'PHP · MySQL · Docker Compose', '', 1
WHERE NOT EXISTS (SELECT 1 FROM projects);
