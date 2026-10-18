@echo off
rem Stop Redis the graceful way: redis-cli SHUTDOWN saves the RDB snapshot first.
rem Adjust REDIS_DIR if your Redis binaries live somewhere else in the tree.
rem (After the setup script adds auth, this needs: redis-cli -a <password> shutdown)
cd /d "%~dp0"
set "USB_ROOT=%~dp0..\.."
set "REDIS_DIR=%USB_ROOT%\OS_Windows\Software\redis"

echo Stopping Portable Redis...
"%REDIS_DIR%\redis-cli.exe" -h 127.0.0.1 -p 6379 shutdown 2>nul
if errorlevel 1 (echo Redis did not answer on 127.0.0.1:6379 - already stopped?) else (echo Redis stopped - snapshot saved.)
