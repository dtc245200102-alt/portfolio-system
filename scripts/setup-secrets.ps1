$ErrorActionPreference = 'Stop'

$repoRoot = Split-Path -Parent $PSScriptRoot
$secretDirectory = Join-Path $repoRoot 'secrets'
$requiredFiles = @(
    'db_root_password.txt',
    'db_app_password.txt',
    'mysql_exporter_password.txt',
    'mysql_exporter.cnf',
    'admin_password.txt',
    'grafana_admin_password.txt'
)

New-Item -ItemType Directory -Force -Path $secretDirectory | Out-Null
$existing = @($requiredFiles | Where-Object { Test-Path (Join-Path $secretDirectory $_) })
if ($existing.Count -gt 0) {
    throw "Secret files already exist. This script does not overwrite them because that could disconnect a running database. Existing: $($existing -join ', ')"
}

function New-RandomHex([int] $byteCount = 32) {
    $bytes = New-Object byte[] $byteCount
    $generator = [System.Security.Cryptography.RandomNumberGenerator]::Create()
    try {
        $generator.GetBytes($bytes)
    } finally {
        $generator.Dispose()
    }
    return [BitConverter]::ToString($bytes).Replace('-', '').ToLowerInvariant()
}

function Write-Secret([string] $name, [string] $value) {
    $path = Join-Path $secretDirectory $name
    [System.IO.File]::WriteAllText($path, $value, [System.Text.Encoding]::ASCII)
}

$rootPassword = New-RandomHex
$appPassword = New-RandomHex
$exporterPassword = New-RandomHex
$adminPassword = New-RandomHex
$grafanaPassword = New-RandomHex

Write-Secret 'db_root_password.txt' $rootPassword
Write-Secret 'db_app_password.txt' $appPassword
Write-Secret 'mysql_exporter_password.txt' $exporterPassword
Write-Secret 'mysql_exporter.cnf' "[client]`nuser=prom_exporter`npassword=$exporterPassword`n"
Write-Secret 'admin_password.txt' $adminPassword
Write-Secret 'grafana_admin_password.txt' $grafanaPassword

Write-Host ''
Write-Host 'Đã tạo thông tin đăng nhập cục bộ trong thư mục secrets (được Git bỏ qua).' -ForegroundColor Green
Write-Host 'Lưu hai mật khẩu dưới đây ở nơi an toàn; script sẽ không hiển thị lại.'
Write-Host "Portfolio admin: username admin | password $adminPassword"
Write-Host "Grafana: username admin | password $grafanaPassword"
Write-Host 'Mật khẩu MySQL nằm trong các file secrets/db_*_password.txt.'
