<?php
declare(strict_types=1);

function read_secret(string $environmentName): string
{
    $path = getenv($environmentName);
    if (!$path || !is_readable($path)) {
        throw new RuntimeException('Required secret is unavailable: ' . $environmentName);
    }

    $value = trim((string) file_get_contents($path));
    if ($value === '') {
        throw new RuntimeException('Required secret is empty: ' . $environmentName);
    }

    return $value;
}

function db(): PDO
{
    static $connection = null;
    if ($connection instanceof PDO) {
        return $connection;
    }

    $host = getenv('DB_HOST') ?: 'db';
    $name = getenv('DB_NAME') ?: 'portfolio';
    $user = getenv('DB_USER') ?: 'portfolio_app';
    $password = read_secret('DB_PASSWORD_FILE');
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $host, $name);

    $connection = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 5,
    ]);

    return $connection;
}
