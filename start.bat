@echo off
chcp 65001 > nul
title TMDT E-Commerce Laravel Server
color 0B

echo =====================================================================
echo           HE THONG THUONG MAI DIEN TU (TMDT) - LARAVEL
echo =====================================================================
echo.

:: 1. Setup PATH
set "PATH=C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64;C:\laragon\bin\composer;C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin;C:\Program Files\MySQL\MySQL Server 8.0\bin;%PATH%"

:: 2. Kiem tra Port 3306 va khoi dong MySQL
echo [1/3] Kiem tra ket noi MySQL (Port 3306)...
netstat -ano | findstr ":3306" | find "LISTENING" > nul
if errorlevel 1 (
    echo [*] Dang tu dong khoi dong MySQL Server...
    start /B "" "C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysqld.exe" --datadir="C:\laragon\data\mysql" > nul 2>&1
    timeout /t 3 /nobreak > nul
    echo [OK] MySQL da khoi dong san sang!
) else (
    echo [OK] MySQL dang hoat dong tot tren Port 3306.
)

:: 3. Kiem tra va Run Migration & Seeder
echo.
echo [2/3] Kiem tra CSDL va Migration...
php artisan migrate --force --seed > nul 2>&1
echo [OK] CSDL 'ecommerce2024' da san sang!

:: 4. Khoi dong Web Server
echo.
echo [3/3] Dang khoi dong Web Server...
echo =====================================================================
echo   >> Website:          http://127.0.0.1:8000
echo   >> Quan ly Danh muc: http://127.0.0.1:8000/categories
echo   >> Nhan Ctrl + C de dung Server
echo =====================================================================
echo.

php artisan serve --port=8000
pause
