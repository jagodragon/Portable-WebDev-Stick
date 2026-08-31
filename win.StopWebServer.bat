@echo off
cd /d "%~dp0"
echo 🛑 Shutting down Windows Development Web Server...
call "Servers-Win\php-desktop\StopWeb.bat"
