@echo off
setlocal EnableExtensions EnableDelayedExpansion
rem Stop Redis the graceful way: redis-cli SHUTDOWN saves the RDB snapshot first.
rem Adjust REDIS_DIR if your Redis binaries live somewhere else in the tree.
rem (After the setup script adds auth, this needs: redis-cli -a <password> shutdown)
cd /d "%~dp0"
set "USB_ROOT=%~dp0..\.."
set "REDIS_DIR=%USB_ROOT%\OS_Windows\Software\redis-server"
set "SETTINGS=%USB_ROOT%\SharedSettings"
call :resolve_port REDIS PORT_REDIS 6379

echo Stopping Portable Redis...
"%REDIS_DIR%\redis-cli.exe" -h 127.0.0.1 -p %PORT_REDIS% shutdown 2>nul
if errorlevel 1 (echo Redis did not answer on 127.0.0.1:%PORT_REDIS% - already stopped?) else (echo Redis stopped - snapshot saved.)
exit /b

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
