@echo off
cd /d "%~dp0"
set "USB_ROOT=%cd%"

rem echo Sweeping hanging web tasks clean...

echo Booting Isolated Portable Redis Cache Server on Port 6379...

start "Redis-Server" /min "%USB_ROOT%\OS_Windows\Software\redis-server\redis-server.exe" "%USB_ROOT%\SharedSettings\redis.conf"
ping 127.0.0.1 -n 2 >nul

ping 127.0.0.1 -n 2 >nul

if exist "%USB_ROOT%\SharedSettings\node-app.js" (
    echo Booting Silent Portable JavaScript Backend Runtime Engine on Port 3000...
    start "" "%USB_ROOT%\OS_Windows\Software\node-desktop\node.exe" "%USB_ROOT%\SharedSettings\node-app.js"
    ping 127.0.0.1 -n 2 >nul
)

echo Opening Default Web Browser...
:: loads base website
start http://127.0.0.1:8080
:: Loads node.js status
start http://127.0.0.1:3000
:: Loads php status
:: start http://127.0.0.1:8080/status.php

:: Define Portable runtime locations
set PHP_HOME=%USB_ROOT%\OS_Windows\Software\php-desktop
set NODE_HOME=%USB_ROOT%\OS_Windows\Software\node-desktop
set PYTHON_HOME=%USB_ROOT%\OS_Windows\Software\python\python
set REDIS_HOME=%USB_ROOT%\OS_Windows\Software\redis-server
set APACHE_HOME=%USB_ROOT%\OS_Windows\Software\apache-server

:: Add portable runtimes to PATH
set PATH=%PHP_HOME%;%NODE_HOME%;%PYTHON_HOME%;%PYTHON_HOME%\Scripts;%PATH%

echo.
echo Runtime Locations:
echo PHP     = %PHP_HOME%
echo Node.js = %NODE_HOME%
echo Python  = %PYTHON_HOME%
echo.

echo Booting Portable Primary Apache Web Server for Windows on Port 8080...
"%USB_ROOT%\OS_Windows\Software\apache-server\bin\httpd.exe" -D WIN -D FOREGROUND -d "%USB_ROOT%\OS_Windows\Software\apache-server" -f "%USB_ROOT%\SharedSettings\httpd.conf" -c "Define USB_ROOT %USB_ROOT%"
pause
