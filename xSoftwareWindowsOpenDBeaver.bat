@echo off
:: Automatically anchor execution context relative to this batch file path location
cd /d "%~dp0"

echo 🚀 Launching Secure Portable Windows DBeaver Control Board...
:: Fire the detached Win64 execution frame pointing right to your portable binary slot
start "" "OS_Windows\DesktopApps\DBeaver-Portable\dbeaver.exe"
exit
