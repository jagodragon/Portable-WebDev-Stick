@echo off
set PHP_FCGI_MAX_REQUESTS=0
rem set PHP_FCGI_CHILDREN=4
"%~dp0php-cgi.exe" -b 127.0.0.1:9000 -c "%~dp0..\..\SharedSettings\php.ini"