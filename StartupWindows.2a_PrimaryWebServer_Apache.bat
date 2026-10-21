@echo off
setlocal EnableExtensions EnableDelayedExpansion
cd /d "%~dp0"
set "USB_ROOT=%cd%"
set "SETTINGS=%USB_ROOT%\SharedSettings"
call :resolve_port WEB PORT_WEB 8080
call :resolve_port REDIS PORT_REDIS 6379
call :resolve_port FPM PORT_FPM 9000
call :resolve_port NODE PORT_NODE 3000
set "WEBDEV_PORT_WEB=%PORT_WEB%"
set "WEBDEV_PORT_REDIS=%PORT_REDIS%"
set "WEBDEV_PORT_FPM=%PORT_FPM%"
set "WEBDEV_PORT_NODE=%PORT_NODE%"

rem echo Sweeping hanging web tasks clean...

echo Booting Isolated Portable Redis Cache Server on Port %PORT_REDIS%...

start "Redis-Server" /min "%USB_ROOT%\OS_Windows\Software\redis-server\redis-server.exe" "%USB_ROOT%\SharedSettings\redis.conf" --port %PORT_REDIS%
ping 127.0.0.1 -n 2 >nul

ping 127.0.0.1 -n 2 >nul

if exist "%USB_ROOT%\SharedSettings\node-app.js" (
    echo Booting Silent Portable JavaScript Backend Runtime Engine on Port %PORT_NODE%...
    start "" "%USB_ROOT%\OS_Windows\Software\node-desktop\node.exe" "%USB_ROOT%\SharedSettings\node-app.js"
    ping 127.0.0.1 -n 2 >nul
)

echo Opening Default Web Browser...
:: loads base website
start http://127.0.0.1:%PORT_WEB%
:: Loads node.js status
start http://127.0.0.1:%PORT_NODE%
:: Loads php status
:: start http://127.0.0.1:%PORT_WEB%/status.php

:: Define Portable runtime locations
set PHP_HOME=%USB_ROOT%\OS_Windows\Software\php-desktop
set NODE_HOME=%USB_ROOT%\OS_Windows\Software\node-desktop
set PYTHON_HOME=%USB_ROOT%\OS_Windows\Software\python\python
set REDIS_HOME=%USB_ROOT%\OS_Windows\Software\redis-server
set APACHE_HOME=%USB_ROOT%\OS_Windows\Software\apache-server

:: Add portable runtimes to PATH
set PATH=%PHP_HOME%;%NODE_HOME%;%PYTHON_HOME%;%PYTHON_HOME%\Scripts;%PATH%

echo.
echo Runtime Locations:
echo PHP     = %PHP_HOME%
echo Node.js = %NODE_HOME%
echo Python  = %PYTHON_HOME%
echo.

echo Booting Portable Primary Apache Web Server for Windows on Port %PORT_WEB%...
"%USB_ROOT%\OS_Windows\Software\apache-server\bin\httpd.exe" -D WIN -D FOREGROUND -d "%USB_ROOT%\OS_Windows\Software\apache-server" -f "%USB_ROOT%\SharedSettings\httpd.conf" -c "Define USB_ROOT %USB_ROOT%"
pause
goto :eof

rem --- Port helpers -------------------------------------------------------------
:resolve_port
rem %1 = key, %2 = out var, %3 = built-in default.
rem Order: WEBDEV_PORT_<key> environment, Ports.InUse.conf, Ports.Default.conf.
set "%~2="
if defined WEBDEV_PORT_%~1 set "%~2=!WEBDEV_PORT_%~1!"
if not defined %~2 call :conf_get "%SETTINGS%\Ports.InUse.conf" InUsePorts %~1 %~2
if not defined %~2 call :conf_get "%SETTINGS%\Ports.Default.conf" DefaultPorts %~1 %~2
if not defined %~2 set "%~2=%~3"
exit /b

:conf_get
rem %1 = file, %2 = section, %3 = key, %4 = out var. Empty when not found.
set "%~4="
set "CG_FILE=%~1"
set "CG_SEC=%~2"
set "CG_KEY=%~3"
for /f "usebackq delims=" %%V in (`powershell -NoProfile -Command "$sec=''; foreach($l in [IO.File]::ReadLines($env:CG_FILE)){ $t=$l.Trim(); if($t.StartsWith('[')){ $sec=$t.Trim('[',']') } elseif($sec -eq $env:CG_SEC){ $kv=$t -split '=',2; if($kv.Count -eq 2 -and $kv[0].Trim() -eq $env:CG_KEY){ $v=($kv[1] -split '[;#]')[0].Trim(); Write-Output $v; break } } }"`) do set "%~4=%%V"
exit /b
