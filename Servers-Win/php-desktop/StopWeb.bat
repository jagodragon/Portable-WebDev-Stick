@echo off
echo 🛑 Stopping Windows PHP Development Web Server...
:: Forces Windows to terminate any php.exe running out of this specific directory
taskkill /f /im php.exe
