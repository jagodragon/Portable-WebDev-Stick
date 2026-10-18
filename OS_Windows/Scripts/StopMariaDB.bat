@echo off
rem Stop MariaDB by pid file - never by image name.
rem Prerequisite: win.StartMariaDB.bat passes --pid-file="%USB_ROOT%\SharedFolders\Data\mariadb.pid"
cd /d "%~dp0"
set "USB_ROOT=%~dp0..\.."
set "PID_FILE=%USB_ROOT%\SharedFolders\Data\mariadb.pid"

echo Stopping Portable MariaDB Server...
if not exist "%PID_FILE%" (
    echo No pid file - MariaDB was not started by this stack, or is already stopped.
    exit /b 0
)
set /p PID=<"%PID_FILE%"
taskkill /pid %PID% 2>nul
timeout /t 3 /nobreak >nul
tasklist /fi "PID eq %PID%" 2>nul | find "%PID%" >nul && taskkill /f /pid %PID% 2>nul
del "%PID_FILE%" 2>nul
echo MariaDB stopped (pid %PID%).
