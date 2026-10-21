@echo off
:: Automatically anchor execution context relative to this batch file path location
cd /d "%~dp0"

echo 🚀 Booting Isolated Portable MariaDB Engine Node on Port 3306...
:: Capture the dynamic absolute USB root directory path mapping line natively
for /f "delims=" %%i in ('cd') do set "USB_ROOT=%%i"

echo 🧹 Sweeping hanging database tasks clean from Windows memory...
rem taskkill /f /im mysqld.exe 2>nul
rem timeout /t 1 /nobreak >nul
set "PID_FILE=%USB_ROOT%\SharedFolders\data\mariadb.pid"
if not exist "%PID_FILE%" goto :no_prev_mariadb
set /p PID=<"%PID_FILE%"
echo Previous MariaDB instance still running (pid %PID%) - stopping it first.
taskkill /pid %PID% 2>nul
timeout /t 3 /nobreak >nul
tasklist /fi "PID eq %PID%" 2>nul | find "%PID%" >nul && taskkill /f /pid %PID% 2>nul
del "%PID_FILE%" 2>nul
:no_prev_mariadb

:: Explicitly override internal defaults by passing your shared data paths at runtime
start /b "MariaDB-Core" "OS_Windows\Software\mariadb\bin\mysqld.exe" --defaults-file="%USB_ROOT%\SharedSettings\mariadb-configs.cnf" --datadir="%USB_ROOT%\mariadb-data" --pid-file="%USB_ROOT%\SharedFolders\data\mariadb.pid"

echo ✅ Portable Database Engine is standing up silently in the background!
timeout /t 2 /nobreak >nul
