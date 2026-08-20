# Script khoi dong du an TMDT Laravel 1-Click
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8

Write-Host "=====================================================================" -ForegroundColor Cyan
Write-Host "          HỆ THỐNG THƯƠNG MẠI ĐIỆN TỬ (TMDT) - LARAVEL" -ForegroundColor Cyan
Write-Host "=====================================================================" -ForegroundColor Cyan
Write-Host ""

# 1. Thiet lap PATH moi truong
$env:PATH = "C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64;C:\laragon\bin\composer;C:\Program Files\MySQL\MySQL Server 8.0\bin;" + $env:PATH

# 2. Kiem tra MySQL Service
Write-Host "[1/3] Kiểm tra trạng thái MySQL80..." -ForegroundColor Yellow
$service = Get-Service -Name "MySQL80" -ErrorAction SilentlyContinue
if ($service -and $service.Status -ne "Running") {
    Write-Host "  -> Đang khởi động MySQL80..." -ForegroundColor Yellow
    Start-Service -Name "MySQL80" -ErrorAction SilentlyContinue
}

# 3. Kiem tra va chay Migration
Write-Host "[2/3] Chạy Database Migration..." -ForegroundColor Yellow
php artisan migrate --force --seed 2>$null

# 4. Khoi dong Server
Write-Host ""
Write-Host "[3/3] Đang khởi động máy chủ Laravel..." -ForegroundColor Green
Write-Host "=====================================================================" -ForegroundColor Green
Write-Host "  >> Truy cập Website:  http://127.0.0.1:8000" -ForegroundColor Green
Write-Host "  >> Quản lý Danh mục:  http://127.0.0.1:8000/categories" -ForegroundColor Green
Write-Host "  >> Nhấn Ctrl + C để dừng máy chủ" -ForegroundColor Green
Write-Host "=====================================================================" -ForegroundColor Green
Write-Host ""

php artisan serve --port=8000
