@echo off
echo 🛑 Stopping Windows Production Caddy and FastCGI Processes...
:: Forcefully terminates both the Windows Caddy engine and the FastCGI background manager
taskkill /f /im caddy_windows_amd64.exe >nul 2>&1
taskkill /f /im php-cgi.exe >nul 2>&1
