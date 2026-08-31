@echo off
:: Automatically anchor execution context relative to this batch file path location
cd /d "%~dp0"

echo 🧹 Sweeping hanging database tasks clean from Windows memory...
taskkill /f /im mysqld.exe 2>nul
timeout /t 1 /nobreak >nul

echo 🚀 Booting Isolated Portable MariaDB Engine Node on Port 3306...
:: Capture the dynamic absolute USB root directory path mapping line natively
for /f "delims=" %%i in ('cd') do set "USB_ROOT=%%i"

:: Explicitly override internal defaults by passing your shared data paths at runtime
start /b "MariaDB-Core" "Servers-Win\mariadb-12.3.3-win\bin\mysqld.exe" --defaults-file="%USB_ROOT%\SharedSettings\mariadb-configs.cnf" --datadir="%USB_ROOT%\mariadb-data"

echo ✅ Portable Database Engine is standing up silently in the background!
timeout /t 2 /nobreak >nul
