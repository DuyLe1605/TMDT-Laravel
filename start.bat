@echo off
chcp 65001 > nul
title TMDT E-Commerce Laravel Server
color 0B

echo =====================================================================
echo           HE THONG THUONG MAI DIEN TU (TMDT) - LARAVEL 11/12
echo =====================================================================
echo.

:: 1. Setup PATH cho PHP, Composer, MySQL
set "PATH=C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64;C:\laragon\bin\composer;C:\Program Files\MySQL\MySQL Server 8.0\bin;%PATH%"

:: 2. Kiem tra va khoi dong Service MySQL80
echo [1/3] Kiem tra dich vu MySQL80...
sc query MySQL80 | find "RUNNING" > nul
if errorlevel 1 (
    echo [*] Dang khoi dong service MySQL80...
    net start MySQL80 > nul 2>&1
    if errorlevel 1 (
        echo [!] Chu y: Neu MySQL80 chua chay, hay bat qua Services.msc hoac Task Manager.
    ) else (
        echo [OK] MySQL80 da chay thanh cong!
    )
) else (
    echo [OK] MySQL80 dang hoat dong san sang.
)

:: 3. Kiem tra va tao Database / Run Migration
echo.
echo [2/3] Kiem tra ket noi CSDL va Migration...
php artisan migrate --force --seed > nul 2>&1
if errorlevel 1 (
    echo [!] Neu gap loi ket noi CSDL, hay kiem tra lai DB_PASSWORD trong file .env
) else (
    echo [OK] CSDL va Migration da san sang!
)

:: 4. Khoi dong Laravel Development Server
echo.
echo [3/3] Dang khoi dong Web Server...
echo.
echo =====================================================================
echo   >> Website dang chay tai: http://127.0.0.1:8000
echo   >> Quan ly Danh muc:     http://127.0.0.1:8000/categories
echo   >> Nhan Ctrl + C de dung Server
echo =====================================================================
echo.

php artisan serve --port=8000
pause
