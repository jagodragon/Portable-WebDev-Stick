@echo off
cd /d "%~dp0"
set "USB_ROOT=%cd%"

echo Sweeping hanging web tasks clean...
taskkill /f /im httpd.exe >nul 2>&1
taskkill /f /im php-cgi.exe >nul 2>&1
taskkill /f /im redis-server.exe >nul 2>&1
taskkill /f /im node.exe >nul 2>&1

if exist "%USB_ROOT%\Servers-Win\apache-server\logs\httpd.pid" del /f /q "%USB_ROOT%\Servers-Win\apache-server\logs\httpd.pid"
ping 127.0.0.1 -n 2 >nul

echo Booting Isolated Portable Redis Cache Server on Port 6379...
rem start "" "%USB_ROOT%\Servers-Win\redis-server\redis-server.exe" "%USB_ROOT%\SharedSettings\redis.conf"
start "Redis-Server" /min "%USB_ROOT%\Servers-Win\redis-server\redis-server.exe" "%USB_ROOT%\SharedSettings\redis.conf"
ping 127.0.0.1 -n 2 >nul

rem echo Booting Silent Portable PHP FastCGI Gateway on Port 9000...
rem set PHP_FCGI_MAX_REQUESTS=0
rem set PHP_FCGI_CHILDREN=0
rem start "" /b "%USB_ROOT%\Servers-Win\php-desktop\php-cgi.exe" -b 127.0.0.1:9000 -c "%USB_ROOT%\SharedSettings\php.ini"
rem start "PHP-FastCGI" /b "%USB_ROOT%\Servers-Win\php-desktop\php-cgi.exe" -b 127.0.0.1:9000 -c "%USB_ROOT%\SharedSettings\php.ini"
rem start "PHP-FastCGI" /b "%USB_ROOT%\Servers-Win\php-desktop\run-php-cgi.bat"
ping 127.0.0.1 -n 2 >nul

if exist "%USB_ROOT%\SharedSettings\node-app.js" (
    echo Booting Silent Portable JavaScript Backend Runtime Engine on Port 3000...
    start "" "%USB_ROOT%\Servers-Win\node-desktop\node.exe" "%USB_ROOT%\SharedSettings\node-app.js"
    ping 127.0.0.1 -n 2 >nul
)

echo Opening Default Web Browser...
:: loads base website
start http://127.0.0.1:8080
:: Loads node.js status
:: start http://127.0.0.1:3000
:: Loads php status
:: start http://127.0.0.1:8080/status.php

:: Add portable PHP binary folder to process PATH so DLL dependencies resolve cleanly
set PATH=%USB_ROOT%\Servers-Win\php-desktop;%PATH%

echo Booting Portable Primary Apache Web Server for Windows on Port 8080...
"%USB_ROOT%\Servers-Win\apache-server\bin\httpd.exe" -D WIN -D FOREGROUND -d "%USB_ROOT%\Servers-Win\apache-server" -f "%USB_ROOT%\SharedSettings\httpd.conf" -c "Define USB_ROOT %USB_ROOT%"
pause
