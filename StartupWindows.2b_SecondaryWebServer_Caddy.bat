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

echo Sweeping hanging web tasks clean...
taskkill /f /im caddy_windows_amd64.exe >nul 2>&1
taskkill /f /im php-cgi.exe >nul 2>&1
taskkill /f /im redis-server.exe >nul 2>&1
taskkill /f /im node.exe >nul 2>&1
ping 127.0.0.1 -n 2 >nul

echo Booting Isolated Portable Redis Cache Server on Port %PORT_REDIS%...
start "Redis-Server" /min "%USB_ROOT%\OS_Windows\Software\redis-server\redis-server.exe" "%USB_ROOT%\SharedSettings\redis.conf" --port %PORT_REDIS%
ping 127.0.0.1 -n 2 >nul

if exist "%USB_ROOT%\SharedSettings\node-app.js" (
    echo Booting Silent Portable JavaScript Backend Runtime Engine on Port %PORT_NODE%...
    start "" "%USB_ROOT%\OS_Windows\Software\node-desktop\node.exe" "%USB_ROOT%\SharedSettings\node-app.js"
    ping 127.0.0.1 -n 2 >nul
)

echo Booting Silent Portable PHP FastCGI Gateway on Port %PORT_FPM%...
:: Navigate directly into the PHP folder so relative "ext" lookups work natively!
cd /d "%USB_ROOT%\OS_Windows\Software\php-desktop"
start "PHP-FastCGI-Caddy" /b php-cgi.exe -b 127.0.0.1:%PORT_FPM% -c "%USB_ROOT%\SharedSettings\php.ini"

:: Jump back to USB root so relative folder pointers operate properly
cd /d "%USB_ROOT%"

echo Opening Default Web Browser...
start http://127.0.0.1:%PORT_WEB%

:: Define Portable runtime locations (Inherited by the runtime console)
set PHP_HOME=%USB_ROOT%\OS_Windows\Software\php-desktop
set NODE_HOME=%USB_ROOT%\OS_Windows\Software\node-desktop
set PYTHON_HOME=%USB_ROOT%\OS_Windows\Software\python\python
set REDIS_HOME=%USB_ROOT%\OS_Windows\Software\redis-server

:: Add portable runtimes to path variable memory spaces
set PATH=%PHP_HOME%;%NODE_HOME%;%PYTHON_HOME%;%PYTHON_HOME%\Scripts;%PATH%

echo.
echo Runtime Locations:
echo PHP     = %PHP_HOME%
echo Node.js = %NODE_HOME%
echo.
echo ⚡ Launching Portable Windows Secondary Web Server Stack - CADDY (Port %PORT_WEB%)...

cd OS_Windows\Software\caddy-server
caddy_windows_amd64.exe run --config ..\..\..\SharedSettings\Caddyfile
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
