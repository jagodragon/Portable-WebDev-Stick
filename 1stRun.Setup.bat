@echo off
setlocal EnableExtensions EnableDelayedExpansion
rem ============================================================================
rem  Portable WebDev Stick - First-Run Setup (Windows)
rem  File: 1stRun.Setup.bat       Installs to: the root of the stick.
rem  Run:   double-click it, or run it from a command prompt in the stick root.
rem
rem  What this does, once per stick (same steps as the Linux script):
rem    1. Asks for a MariaDB root password, a 'webdev' account password, and a
rem       username/password for the database editor web page. Passwords are
rem       typed into a no-echo prompt (via PowerShell's secure prompt) and are
rem       never written anywhere except where step 3 puts them.
rem    2. Starts MariaDB once in a one-time setup mode, creates the accounts by
rem       running SharedSettings\setup.sql, then stops it again.
rem    3. Writes SharedSettings\db-credentials.php - the file the database
rem       editor and your projects read to connect. The editor's password is
rem       stored there only as a one-way hash.
rem    4. Starts MariaDB normally and PROVES the new state: root without a
rem       password must now FAIL, webdev with its password must succeed.
rem    5. Leaves a marker file (SharedFolders\data\.setup-done) so this setup
rem       never runs twice by accident.
rem    6. Renames the setup entry points at the stick root (this script and
rem       the Linux desktop launcher) to z.SetupAlreadyRan.*, so they sink
rem       to the bottom of the folder listing - the "1" that put them at
rem       the top has done its job. The Linux worker script in
rem       OS_Linux\Scripts keeps its name on purpose: the renamed launcher
rem       points at it, and a stable path is what lets that launcher work
rem       again later (it finds the marker and reports setup as done).
rem
rem  Nothing in this script raises its own privileges. If a step ever needed
rem  administrator rights it would say so and stop - that day is not today:
rem  everything here runs as you, from files already on the stick.
rem
rem  One Windows quirk: please avoid the characters %% and ^! in the passwords
rem  you choose here - the Windows command interpreter cannot carry them
rem  safely through this script. Letters, digits and other symbols are fine.
rem ============================================================================

cd /d "%~dp0"
set "USB_ROOT=%CD%"
set "SETTINGS=%USB_ROOT%\SharedSettings"
set "DATA_DIR=%USB_ROOT%\mariadb-data"
set "RUNTIME=%USB_ROOT%\SharedFolders\data"
set "WORKTMP=%USB_ROOT%\SharedFolders\tmp"
set "PID_FILE=%RUNTIME%\mariadb.pid"
set "MARKER=%RUNTIME%\.setup-done"
set "CREDS_FILE=%SETTINGS%\db-credentials.php"
set "PORTS_DEFAULT=%SETTINGS%\Ports.Default.conf"
set "PORTS_INUSE=%SETTINGS%\Ports.InUse.conf"
set "SQL_TEMPLATE=%SETTINGS%\setup.sql"
set "GENERATOR=%SETTINGS%\setup-generate.php"
set "MYSQLD=%USB_ROOT%\OS_Windows\Software\mariadb\bin\mysqld.exe"
set "MARIADB=%USB_ROOT%\OS_Windows\Software\mariadb\bin\mariadb.exe"
set "MARIADB_ADMIN=%USB_ROOT%\OS_Windows\Software\mariadb\bin\mariadb-admin.exe"
set "PHP_EXE=%USB_ROOT%\OS_Windows\Software\php-desktop\php.exe"
set "TMP_SQL=%WORKTMP%\.setup-sql.tmp"
set "SETUP_COMPLETED=0"
set "FAILMSG="

echo ===================================================================
echo  Portable WebDev Stick - first-run setup
echo ===================================================================

rem ---- Step 1: is everything this script needs actually here? ---------------
rem Everything used below ships ON the stick, so a missing file means this
rem copy of the kit is incomplete - no installer can fix that, and this
rem script will not pretend otherwise. It says exactly what is missing.
call :require "%MYSQLD%"
call :require "%MARIADB%"
call :require "%MARIADB_ADMIN%"
call :require "%PHP_EXE%"
call :require "%SQL_TEMPLATE%"
call :require "%GENERATOR%"
if defined FAILMSG goto :fail
if not exist "%DATA_DIR%" set "FAILMSG=missing folder: %DATA_DIR% - this does not look like a WebDev stick root."
if defined FAILMSG goto :fail
if not exist "%RUNTIME%" mkdir "%RUNTIME%"
if not exist "%WORKTMP%" mkdir "%WORKTMP%"

rem ---- Step 2: once per stick ------------------------------------------------
if exist "%MARKER%" (
    echo First-run setup was already completed for this stick:
    type "%MARKER%"
    call :init_ports
    if defined FAILMSG goto :fail
    echo !PORTS_INIT_MSG!.
    echo Nothing to do. To run setup again, delete that marker file first.
    set "SETUP_COMPLETED=1"
    goto :end
)

rem ---- Step 3: the stack must be stopped --------------------------------------
if exist "%PID_FILE%" (
    set /p OLDPID=<"%PID_FILE%"
    if defined OLDPID (
        tasklist /fi "PID eq !OLDPID!" 2>nul | find "!OLDPID!" >nul
        if not errorlevel 1 set "FAILMSG=MariaDB is running from this stick, pid !OLDPID!. Stop the stack first, then run setup again."
    )
)
if defined FAILMSG goto :fail

rem ---- Step 4: collect the secrets --------------------------------------------
echo.
echo You will set three things. Passwords are not echoed as you type.
echo.
call :prompt_twice "MariaDB ROOT password (the master key - keep it somewhere safe)" ROOT_PW
call :prompt_twice "WEBDEV account password (your projects will connect with this one)" WEBDEV_PW
set "GATE_USER="
set /p "GATE_USER=Username for the database editor web page [admin]: "
if not defined GATE_USER set "GATE_USER=admin"
call :prompt_twice "Database editor web page password (separate from the database passwords)" GATE_PW
set "EDITOR_DB="
set /p "EDITOR_DB=Database the editor should open [TestTable]: "
if not defined EDITOR_DB set "EDITOR_DB=TestTable"

rem ---- Step 5: generate the working files -------------------------------------
rem The generator (PHP, shared with the Linux script) does all password
rem string handling in one audited place and writes the one-time SQL file
rem and SharedSettings\db-credentials.php.
set "SETUP_ROOT_PW=%ROOT_PW%"
set "SETUP_WEBDEV_PW=%WEBDEV_PW%"
set "SETUP_GATE_USER=%GATE_USER%"
set "SETUP_GATE_PW=%GATE_PW%"
set "SETUP_EDITOR_DB=%EDITOR_DB%"
set "SETUP_SQL_TEMPLATE=%SQL_TEMPLATE%"
set "SETUP_TMP_SQL=%TMP_SQL%"
set "SETUP_CREDS_FILE=%CREDS_FILE%"
"%PHP_EXE%" "%GENERATOR%"
if errorlevel 1 set "FAILMSG=could not generate the setup files (see the message above)."
set "SETUP_ROOT_PW="
set "SETUP_WEBDEV_PW="
set "SETUP_GATE_PW="
if defined FAILMSG goto :fail

rem ---- Step 6: start in setup mode and create the accounts --------------------
echo.
echo Starting MariaDB in one-time setup mode...
rem --defaults-file is the first MariaDB argument here on purpose: the
rem server accepts it only in first position.
start /b "MariaDB-Setup" "%MYSQLD%" --defaults-file="%SETTINGS%\mariadb-configs.cnf" --skip-grant-tables --datadir="%DATA_DIR%" --pid-file="%PID_FILE%"
set /a TRIES=0
:wait_setup
"%MARIADB%" -h 127.0.0.1 -P 3306 -u root -e "SELECT 1" >nul 2>&1
if not errorlevel 1 goto :ready_setup
set /a TRIES+=1
if !TRIES! geq 30 set "FAILMSG=MariaDB did not come up in setup mode. The data folder may be damaged - setup changed nothing."
if defined FAILMSG goto :fail
timeout /t 1 /nobreak >nul
goto :wait_setup
:ready_setup
"%MARIADB%" -h 127.0.0.1 -P 3306 -u root < "%TMP_SQL%"
if errorlevel 1 set "FAILMSG=creating the accounts failed - setup is not complete."
if defined FAILMSG goto :fail
del "%TMP_SQL%" 2>nul
echo Accounts created: root and webdev now have the passwords you set.
call :stop_any

rem ---- Step 7: start normally and prove it ------------------------------------
echo Restarting MariaDB normally to verify...
start /b "MariaDB-Verify" "%MYSQLD%" --defaults-file="%SETTINGS%\mariadb-configs.cnf" --datadir="%DATA_DIR%" --pid-file="%PID_FILE%"
set /a TRIES=0
:wait_normal
"%MARIADB%" -h 127.0.0.1 -P 3306 -u root -e "SELECT 1" 2>&1 | findstr /c:"Access denied" >nul
if not errorlevel 1 goto :ready_normal
set /a TRIES+=1
if !TRIES! geq 30 set "FAILMSG=MariaDB did not restart in normal mode."
if defined FAILMSG goto :fail
timeout /t 1 /nobreak >nul
goto :wait_normal
:ready_normal

rem (a) Root WITHOUT a password must now fail. If it succeeds, authentication
rem     is still switched off in the shared config - name the exact cause.
set "MYSQL_PWD="
"%MARIADB%" -h 127.0.0.1 -P 3306 -u root -e "SELECT 1" >nul 2>&1
if not errorlevel 1 set "FAILMSG=root can still log in WITHOUT a password. The accounts are set, but authentication is still off - most likely the line 'skip-grant-tables' is still present in SharedSettings\mariadb-configs.cnf. Remove that one line, then run this setup again to finish verification."
if defined FAILMSG goto :fail
rem (b) webdev with its password must succeed (TCP - the way PHP connects).
set "MYSQL_PWD=%WEBDEV_PW%"
"%MARIADB%" -h 127.0.0.1 -P 3306 -u webdev -e "SELECT 1" >nul 2>&1
if errorlevel 1 set "FAILMSG=webdev could not log in with its new password - setup is not complete."
if defined FAILMSG goto :fail
rem (c) root WITH its password must succeed.
set "MYSQL_PWD=%ROOT_PW%"
"%MARIADB%" -h 127.0.0.1 -P 3306 -u root -e "SELECT 1" >nul 2>&1
if errorlevel 1 set "FAILMSG=root could not log in with its new password - setup is not complete."
set "MYSQL_PWD="
if defined FAILMSG goto :fail
call :stop_any

rem ---- Step 8: marker, message, and sink out of the way -----------------------
call :init_ports
if defined FAILMSG goto :fail
> "%MARKER%" echo Portable WebDev Stick first-run setup completed.
>>"%MARKER%" echo Date: %DATE% %TIME%
>>"%MARKER%" echo Setup version: 1
set "SETUP_COMPLETED=1"
call :show_summary
pause
rem Rename the Linux desktop launcher directly (its worker script in
rem OS_Linux\Scripts keeps its name - the renamed launcher points at it),
rem and rename this file through a small delayed helper: a batch file
rem cannot safely rename itself while the interpreter is still reading it,
rem so the helper waits a few seconds - by then the exit on the next line
rem has finished and this file is closed.
if exist "%USB_ROOT%\1stRun.Setup.desktop" ren "%USB_ROOT%\1stRun.Setup.desktop" "z.SetupAlreadyRan.desktop"
if /i "%~nx0"=="1stRun.Setup.bat" start "" /min cmd /c "ping -n 4 127.0.0.1 >nul & ren "%~f0" z.SetupAlreadyRan.bat"
exit /b 0

rem ============================================================================
:require
if not exist "%~1" set "FAILMSG=missing file: %~1 - this copy of the stick is incomplete. Re-copy the kit, then run setup again."
exit /b

:ask_secret
rem %1 = prompt text (no apostrophes - it is passed to PowerShell), %2 = variable
set "_secret="
for /f "delims=" %%p in ('powershell -NoProfile -Command "$s = Read-Host -AsSecureString '%~1'; [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($s))"') do set "_secret=%%p"
set "%~2=%_secret%"
set "_secret="
exit /b

:prompt_twice
rem %1 = label, %2 = variable to set. Loops until non-empty and matching.
:pt_loop
set "_try1="
set "_try2="
call :ask_secret "%~1" _try1
call :ask_secret "Repeat %~1" _try2
if not defined _try1 (
    echo   must not be empty - try again
    goto :pt_loop
)
if not "!_try1!"=="!_try2!" (
    echo   the two entries did not match - try again
    goto :pt_loop
)
set "%~2=!_try1!"
set "_try1="
set "_try2="
exit /b

:stop_any
rem Best-effort shutdown of a server THIS script started: try without
rem credentials (setup mode), then with root's password (normal mode).
set "_keep_mysql_pwd=%MYSQL_PWD%"
set "MYSQL_PWD="
"%MARIADB_ADMIN%" -h 127.0.0.1 -P 3306 -u root shutdown >nul 2>&1
set "MYSQL_PWD=%ROOT_PW%"
"%MARIADB_ADMIN%" -h 127.0.0.1 -P 3306 -u root shutdown >nul 2>&1
set "MYSQL_PWD=%_keep_mysql_pwd%"
set "_keep_mysql_pwd="
exit /b

:init_ports
rem Stamp the active ports file from the declared defaults - unless a port
rem decision already lives there (a.Ports.FixConflicts wrote it), which
rem setup must never clobber.
set "PORTS_INIT_MSG="
if exist "%PORTS_INUSE%" (
    findstr /B /C:"[InUsePorts]" "%PORTS_INUSE%" >nul
    if not errorlevel 1 (
        set "PORTS_INIT_MSG=existing Ports.InUse.conf kept - it already holds a port decision"
        exit /b
    )
)
call :conf_get "%PORTS_DEFAULT%" DefaultPorts WEB P_WEB
if not defined P_WEB set "P_WEB=8080"
call :conf_get "%PORTS_DEFAULT%" DefaultPorts MARIADB P_MARIADB
if not defined P_MARIADB set "P_MARIADB=3306"
call :conf_get "%PORTS_DEFAULT%" DefaultPorts REDIS P_REDIS
if not defined P_REDIS set "P_REDIS=6379"
call :conf_get "%PORTS_DEFAULT%" DefaultPorts FPM P_FPM
if not defined P_FPM set "P_FPM=9000"
call :conf_get "%PORTS_DEFAULT%" DefaultPorts NODE P_NODE
if not defined P_NODE set "P_NODE=3000"
if exist "%PORTS_INUSE%.tmp" del "%PORTS_INUSE%.tmp"
> "%PORTS_INUSE%.tmp" (
    echo # Ports.InUse.conf - created by 1stRun.Setup from Ports.Default.conf.
    echo # a.Ports.FixConflicts may rewrite it; a.Ports.UseDefaults removes it.
    echo SESSION=SETUP %DATE% %TIME% on %COMPUTERNAME%
    echo.
    echo [InUsePorts]
    echo WEB=%P_WEB%
    echo MARIADB=%P_MARIADB%
    echo REDIS=%P_REDIS%
    echo FPM=%P_FPM%
    echo NODE=%P_NODE%
)
if not exist "%PORTS_INUSE%.tmp" set "FAILMSG=could not write Ports.InUse.conf."
if defined FAILMSG exit /b
move /y "%PORTS_INUSE%.tmp" "%PORTS_INUSE%" >nul
if errorlevel 1 set "FAILMSG=could not replace Ports.InUse.conf."
if defined FAILMSG exit /b
set "PORTS_INIT_MSG=Ports.InUse.conf created from Ports.Default.conf"
exit /b

:conf_get
rem %1 = file, %2 = section, %3 = key, %4 = out var. Empty when not found.
set "%~4="
set "CG_FILE=%~1"
set "CG_SEC=%~2"
set "CG_KEY=%~3"
for /f "usebackq delims=" %%V in (`powershell -NoProfile -Command "$sec=''; foreach($l in [IO.File]::ReadLines($env:CG_FILE)){ $t=$l.Trim(); if($t.StartsWith('[')){ $sec=$t.Trim('[',']') } elseif($sec -eq $env:CG_SEC){ $kv=$t -split '=',2; if($kv.Count -eq 2 -and $kv[0].Trim() -eq $env:CG_KEY){ $v=($kv[1] -split '[;#]')[0].Trim(); Write-Output $v; break } } }"`) do set "%~4=%%V"
exit /b

:show_summary
echo.
echo ===================================================================
echo  Setup complete.
echo ===================================================================
echo  - MariaDB root password:   the master key you set (not stored on the stick).
echo  - webdev account:          password stored in SharedSettings\db-credentials.php
echo                             so your projects can connect. Treat that file as a
echo                             secret: never share it, never commit it to git.
echo  - Database editor:         open http://127.0.0.1:8080/AdminerEditor/ after
echo                             starting the stack, and sign in with the editor
echo                             username '%GATE_USER%' and the editor password you set.
echo  - Ports:                   %PORTS_INIT_MSG%.
echo  - The stack now starts the normal way (MariaDB first, then a web server).
echo  - This setup script and the Linux desktop launcher now rename themselves
echo    to z.SetupAlreadyRan.* and sink to the bottom of the folder. Setup is done.
echo.
echo One polite request before you go:
echo.
echo ######    ########    ####    ######      ########  ##    ##  ########
echo ##    ##  ##        ##    ##  ##    ##        ##      ##    ##  ##
echo ######    ######    ########  ##    ##        ##      ########  ######
echo ##  ##    ##        ##    ##  ##    ##        ##      ##    ##  ##
echo ##    ##  ########  ##    ##  ######        ##      ##    ##  ########
echo.
echo      ######    ########    ####    ######    ##      ##  ########
echo      ##    ##  ##        ##    ##  ##    ##  ####  ####  ##
echo      ######    ######    ########  ##    ##  ##  ##  ##  ######
echo      ##  ##    ##        ##    ##  ##    ##  ##      ##  ##
echo      ##    ##  ########  ##    ##  ######    ##      ##  ########
echo.
echo Everything important is in README.md at the stick root - what each
echo launcher does, in what order, and what to do when something goes
echo wrong. Two minutes there will save you twenty later. Thank you!
echo.
exit /b

:fail
call :stop_any
del "%TMP_SQL%" 2>nul
if "%SETUP_COMPLETED%"=="0" del "%CREDS_FILE%" 2>nul
echo.
echo *** Setup stopped: %FAILMSG%
echo.
pause
exit /b 1

:end
pause
endlocal
exit /b 0
