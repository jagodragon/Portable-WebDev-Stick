@echo off
cd /d "%~dp0"
echo 🛑 Shutting down Windows Production Web Server...
call "Servers-Win\php-desktop\StopProduction.bat"
