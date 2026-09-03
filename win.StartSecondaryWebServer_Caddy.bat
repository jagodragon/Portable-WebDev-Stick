@echo off
cd /d "%~dp0"
echo ⚡ Launching Portable Windows Production Server Stack (Port 8080)...

:: Start your Windows portable PHP engine in quiet background FastCGI mode
start /B "" "OS_Windows\Software\php-desktop\php-cgi.exe" -b 127.0.0.1:8000

:: Turn on your portable Windows Caddy reverse proxy server pointing to your shared config
cd OS_Windows\Software\caddy-server
caddy.exe run --config ..\..\..\SharedSettings\Caddyfile
