@echo off
rem Stop Caddy via its own admin API ("caddy stop") - no process killing at all.
rem Adjust CADDY_BIN if your binary lives somewhere else in the tree.
cd /d "%~dp0"
set "USB_ROOT=%~dp0..\.."
set "CADDY_BIN=%USB_ROOT%\OS_Windows\Software\caddy\caddy_windows_amd64.exe"

echo Stopping Portable Caddy...
if exist "%CADDY_BIN%" (
    "%CADDY_BIN%" stop 2>nul
    echo Caddy stopped - or it was not running.
) else (
    echo Caddy binary not found at %CADDY_BIN% - edit CADDY_BIN at the top of this script.
)
