<?php
declare(strict_types=1);

define('APP_ROOT', dirname(__DIR__));

header('Content-Type: text/html; charset=UTF-8');

session_name('portfolio_session');
session_set_cookie_params([
    'httponly' => true,
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Lax',
    'path' => '/',
]);
session_start();

require_once APP_ROOT . '/app/database.php';
require_once APP_ROOT . '/app/helpers.php';
