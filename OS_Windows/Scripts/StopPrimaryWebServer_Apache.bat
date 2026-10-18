@echo off
title Stopping Web Development Servers...
rem Pid-file / full-path replacement for the old kill-everything-by-image-name sweep.
rem The old version killed EVERY httpd.exe / node.exe / redis-server.exe / php.exe
rem on the machine - including any the host itself was running. This version only
rem ever touches processes this stack started.
cd /d "%~dp0"
set "USB_ROOT=%~dp0..\.."
echo ===================================================
echo   SWEEPING PORTABLE DEV SERVICES - THIS STACK ONLY
echo ===================================================

:: 1. Apache - by pid file (httpd.conf: PidFile "${USB_ROOT}/SharedFolders/data/httpd.pid")
echo Stopping Apache...
set "PID_FILE=%USB_ROOT%\SharedFolders\data\httpd.pid"
if exist "%PID_FILE%" (
    set /p APID=<"%PID_FILE%"
    taskkill /pid %APID% 2>nul
    timeout /t 2 /nobreak >nul
    tasklist /fi "PID eq %APID%" 2>nul | find "%APID%" >nul && taskkill /f /pid %APID% 2>nul
    del "%PID_FILE%" 2>nul
) else (echo   No pid file - Apache not started by this stack.)

:: 2. PHP-CGI - only instances whose executable lives on THIS stick
echo Stopping PHP FastCGI gateways (this stick only)...
powershell -NoProfile -Command "Get-CimInstance Win32_Process -Filter \"name='php-cgi.exe'\" | Where-Object { $_.ExecutablePath -like '%USB_ROOT%\*' } | ForEach-Object { Stop-Process -Id $_.ProcessId -Force }"

:: 3. Node - only the process running THIS stack's node-app.js
echo Stopping Node backend (this stack only)...
powershell -NoProfile -Command "Get-CimInstance Win32_Process -Filter \"name='node.exe'\" | Where-Object { $_.CommandLine -like '*node-app.js*' } | ForEach-Object { Stop-Process -Id $_.ProcessId -Force }"

:: 4. Redis - graceful shutdown via redis-cli (saves the snapshot)
echo Stopping Redis cache...
call "%USB_ROOT%\OS_Windows\Scripts\StopRedis.bat"

:: 5. Caddy - via its own admin API
echo Stopping Caddy proxy...
call "%USB_ROOT%\OS_Windows\Scripts\StopCaddy.bat"

echo ===================================================
echo   PORTABLE SERVICES STOPPED
echo ===================================================
timeout /t 3
exit
