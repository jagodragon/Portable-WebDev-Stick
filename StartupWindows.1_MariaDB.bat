@echo off
setlocal EnableExtensions EnableDelayedExpansion
:: Automatically anchor execution context relative to this batch file path location
cd /d "%~dp0"

echo 🚀 Booting Isolated Portable MariaDB Engine Node on Port %PORT_MARIADB%...
:: Capture the dynamic absolute USB root directory path mapping line natively
for /f "delims=" %%i in ('cd') do set "USB_ROOT=%%i"
set "SETTINGS=%USB_ROOT%\SharedSettings"
call :resolve_port MARIADB PORT_MARIADB 3306
set "WEBDEV_PORT_MARIADB=%PORT_MARIADB%"

echo 🧹 Sweeping hanging database tasks clean from Windows memory...
rem taskkill /f /im mysqld.exe 2>nul
rem timeout /t 1 /nobreak >nul
set "PID_FILE=%USB_ROOT%\SharedFolders\data\mariadb.pid"
if not exist "%PID_FILE%" goto :no_prev_mariadb
set /p PID=<"%PID_FILE%"
echo Previous MariaDB instance still running (pid %PID%) - stopping it first.
taskkill /pid %PID% 2>nul
timeout /t 3 /nobreak >nul
tasklist /fi "PID eq %PID%" 2>nul | find "%PID%" >nul && taskkill /f /pid %PID% 2>nul
del "%PID_FILE%" 2>nul
:no_prev_mariadb

:: Explicitly override internal defaults by passing your shared data paths at runtime
start /b "MariaDB-Core" "OS_Windows\Software\mariadb\bin\mysqld.exe" --defaults-file="%USB_ROOT%\SharedSettings\mariadb-configs.cnf" --datadir="%USB_ROOT%\mariadb-data" --pid-file="%USB_ROOT%\SharedFolders\data\mariadb.pid" --port=%PORT_MARIADB%

echo ✅ Portable Database Engine is standing up silently in the background!
timeout /t 2 /nobreak >nul
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
