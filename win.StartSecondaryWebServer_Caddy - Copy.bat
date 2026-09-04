@echo off
cd /d "%~dp0"
set "USB_ROOT=%cd%"

echo Sweeping hanging web tasks clean...
taskkill /f /im caddy_windows_amd64.exe >nul 2>&1
taskkill /f /im php-cgi.exe >nul 2>&1
taskkill /f /im redis-server.exe >nul 2>&1
taskkill /f /im node.exe >nul 2>&1
ping 127.0.0.1 -n 2 >nul

echo Booting Isolated Portable Redis Cache Server on Port 6379...
start "Redis-Server" /min "%USB_ROOT%\OS_Windows\Software\redis-server\redis-server.exe" "%USB_ROOT%\SharedSettings\redis.conf"
ping 127.0.0.1 -n 2 >nul

if exist "%USB_ROOT%\SharedSettings\node-app.js" (
    echo Booting Silent Portable JavaScript Backend Runtime Engine on Port 3000...
    start "" "%USB_ROOT%\OS_Windows\Software\node-desktop\node.exe" "%USB_ROOT%\SharedSettings\node-app.js"
    ping 127.0.0.1 -n 2 >nul
)

echo Booting Silent Portable PHP FastCGI Gateway on Port 9000...
:: Navigate directly into the PHP folder so relative "ext" lookups work natively!
cd /d "%USB_ROOT%\OS_Windows\Software\php-desktop"
start "PHP-FastCGI-Caddy" /b php-cgi.exe -b 127.0.0.1:9000 -c "%USB_ROOT%\SharedSettings\php.ini"

:: Jump back to USB root so relative folder pointers operate properly
cd /d "%USB_ROOT%"

echo Opening Default Web Browser...
start http://127.0.0.1:8080

:: Define Portable runtime locations (Inherited by the runtime console)
set PHP_HOME=%USB_ROOT%\OS_Windows\Software\php-desktop
set NODE_HOME=%USB_ROOT%\OS_Windows\Software\node-desktop
set PYTHON_HOME=%USB_ROOT%\OS_Windows\Software\python\python
set REDIS_HOME=%USB_ROOT%\OS_Windows\Software\redis-server

:: Add portable runtimes to path variable memory spaces
set PATH=%PHP_HOME%;%NODE_HOME%;%PYTHON_HOME%;%PYTHON_HOME%\Scripts;%PATH%

echo.
echo Runtime Locations:
echo PHP     = %PHP_HOME%
echo Node.js = %NODE_HOME%
echo.
echo ⚡ Launching Portable Windows Secondary Web Server Stack - CADDY (Port 8080)...

cd OS_Windows\Software\caddy-server
caddy_windows_amd64.exe run --config ..\..\..\SharedSettings\Caddyfile