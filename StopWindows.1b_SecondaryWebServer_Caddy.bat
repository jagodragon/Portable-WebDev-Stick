@echo off
cd /d "%~dp0"
echo 🛑 Shutting down Windows Production Web Server...
call "OS_Windows\Scripts\StopCaddy.bat"
