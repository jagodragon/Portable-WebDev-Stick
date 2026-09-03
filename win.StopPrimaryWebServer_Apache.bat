@echo off
title Stopping Web Development Servers...
echo ===================================================
echo   [🧹] SWEEPING HANGING WINDOWS DEV SERVICES CLEAN
echo ===================================================

:: 1. Stop Apache Web Server
echo Stopping Apache Static Server...
taskkill /f /im httpd.exe 2>nul

:: 2. Stop PHP-CGI Processes
echo Stopping PHP FastCGI Gateways...
taskkill /f /im php-cgi.exe 2>nul
taskkill /f /im php.exe 2>nul

:: 3. Stop Node.js Backend Runtimes
echo Stopping Node.js Backend Runtimes...
taskkill /f /im node.exe 2>nul

:: 4. Stop Redis Server Cache
echo Stopping Redis Cache Servers...
taskkill /f /im redis-server.exe 2>nul

:: 5. Stop Caddy Proxy Engine (If running on Windows)
echo Stopping Caddy Proxy Engines...
taskkill /f /im caddy.exe 2>nul

echo ===================================================
echo   [🟩] ALL SERVICES STOPPED SUCCESSFULLY
echo ===================================================
timeout /t 3
exit

