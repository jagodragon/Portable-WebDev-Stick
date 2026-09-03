@echo off
echo 🛑 Stopping Windows Production Caddy and FastCGI Processes...
:: Forcefully terminates both the Windows Caddy engine and the FastCGI background manager
taskkill /f /im caddy.exe
taskkill /f /im php-cgi.exe
