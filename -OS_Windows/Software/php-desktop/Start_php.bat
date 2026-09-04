@echo off
cd /d "%~dp0"
echo 🚀 Starting Windows PHP Development Server...
start http://127.0.0.1:8000
php.exe -S 127.0.0.1:8000 -t ..\..\www
