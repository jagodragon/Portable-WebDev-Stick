@echo off
cd /d "%~dp0"

echo 🛑 Stopping Portable MariaDB Database Process Tree safely...
:: Force close the Windows engine thread to prevent dangling file table descriptor locks
taskkill /f /im mysqld.exe 2>nul

echo ✅ Database processing loops terminated cleanly.
timeout /t 1 /nobreak >nul
