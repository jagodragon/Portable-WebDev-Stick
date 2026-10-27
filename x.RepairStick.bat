@echo off
rem ============================================================================
rem  Portable WebDev Stick - Repair Stick (Windows)
rem  File: x.RepairStick.bat   Lives at: the root of the stick itself.
rem  Run:   double-click x.RepairStick.bat
rem
rem  THE "MY CONFIG IS BROKEN" TOOL. It returns the stick's SETTINGS and
rem  SETUP STATE to factory condition, so 1stRun.Setup can run again on a
rem  known-clean configuration - without touching your work.
rem
rem  WHAT IT DOES:
rem    - Restores every file in SharedSettings\ from the factory copies in
rem      SharedFolders\usr\ (one .zip per file). Hand edits to those files
rem      are lost - that is the point of the tool.
rem    - Deletes SharedSettings\db-credentials.php and Ports.InUse.conf.
rem      The next 1stRun.Setup run writes fresh ones, and re-keys the
rem      MariaDB root and webdev accounts and the editor sign-in then.
rem    - Empties SharedFolders\data\ and SharedFolders\tmp\ (pid files,
rem      the Redis snapshot, PHP sessions, the .setup-done marker).
rem    - Deletes log residue in the OS trees and a stale FPM pid file.
rem    - Renames a spent z.SetupAlreadyRan.* pair back to 1stRun.Setup.*.
rem
rem  WHAT IT NEVER TOUCHES:
rem    - Your databases. Every database and its contents survive exactly
rem      as they are. Accounts too: your current passwords keep working
rem      until you run 1stRun.Setup again and choose new ones.
rem    - www\ - your projects and the kit's pages alike.
rem    - The program files under OS_Windows\ and OS_Linux\.
rem  If program FILES are damaged (not just settings), this is the wrong
rem  tool - that is what the maintainer's clone-based reset is for.
rem
rem  SAFETY:
rem    - It only runs on a stick: the stick's signature folders must sit
rem      beside it, including the SharedFolders\usr\ factory copies.
rem    - It refuses while ANY stack server is running from the stick
rem      (every pid file under SharedFolders\data\ is checked for
rem      liveness, and every running process's executable is checked for
rem      living on the stick).
rem    - It prints exactly what will happen and proceeds only when you
rem      type the word REPAIR.
rem    - Every step is safe to redo: if a repair is interrupted, run it
rem      again and it finishes the job.
rem
rem  NEEDS: nothing to install. Unzipping uses Expand-Archive, which is
rem  built into Windows PowerShell. A repair tool depends on the
rem  operating system, never on the thing being repaired.
rem
rem  A NOTE FOR ANYONE EDITING THIS FILE: the control flow is goto-style
rem  on purpose. cmd.exe misparses echoed parentheses inside ( ) blocks
rem  - the error is ". was unexpected at this time." - so this file
rem  keeps its messages at top level and jumps to them, the same shape
rem  as the maintainer's reset scripts. Do not "tidy" it back into
rem  nested blocks with messages inside them.
rem ============================================================================

setlocal EnableDelayedExpansion
set "STICK=%~dp0"
if "%STICK:~-1%"=="\" set "STICK=%STICK:~0,-1%"
set "PAYLOAD=%STICK%\SharedFolders\usr"
set "STAGE=%STICK%\SharedFolders\tmp\.repair-stage"
set "FACTORY_FILES=Ports.Default.conf httpd-ssl.conf httpd-vhosts.conf httpd.conf mariadb-configs.cnf node-app.js php-fpm.conf php.ini python-env.sh redis.conf setup-generate.php setup.sql"
set "PARTIAL=The repair is PARTIAL - run x.RepairStick again to finish it."

rem ---- Am I standing on a stick? ---------------------------------------------
if not exist "%STICK%\SharedSettings" goto :bad_stick
if not exist "%STICK%\mariadb-data" goto :bad_stick
if not exist "%STICK%\OS_Windows" goto :bad_stick
if not exist "%STICK%\www" goto :bad_stick
if not exist "%PAYLOAD%" goto :no_payload
goto :fences
:bad_stick
echo *** Repair stopped: run me from the root of a WebDev stick - SharedSettings\, mariadb-data\, OS_Windows\ and www\ must sit beside me.
goto :end_fail
:no_payload
echo *** Repair stopped: the factory copies are missing - SharedFolders\usr\ not found beside me. Without them there is nothing to repair FROM.
goto :end_fail
:fences

rem ---- The whole stack must be stopped ---------------------------------------
set "BADPID="
for %%F in ("%STICK%\SharedFolders\data\*.pid") do (
    set "PID="
    set /p PID=<"%%F"
    if defined PID (
        powershell -NoProfile -Command "if (Get-Process -Id !PID! -ErrorAction SilentlyContinue) { exit 1 } else { exit 0 }" >nul 2>&1
        if errorlevel 1 set "BADPID=!PID!"
    )
    if defined BADPID (
        set "BADFILE=%%F"
        goto :still_running
    )
)
goto :sweep
:still_running
echo *** Repair stopped: a stack server is still running from the stick - pid %BADPID%, recorded in %BADFILE%. Stop the stack first - repairing under running servers is how files get half-written.
goto :end_fail
:sweep
rem Servers with no pid file (Redis, the Node app) and orphans that
rem outlived theirs: sweep the process list for anything whose
rem executable lives on the stick. (This script runs from the stick's
rem root, but its interpreter is Windows itself, never a stick binary.)
powershell -NoProfile -Command "if (Get-CimInstance Win32_Process | Where-Object { $_.ExecutablePath -and $_.ExecutablePath.StartsWith('%STICK%') }) { exit 1 } else { exit 0 }" >nul 2>&1
if errorlevel 1 goto :sweep_running
goto :plan
:sweep_running
echo *** Repair stopped: something is still running from the stick. Stop the stack - and close anything else running from the stick - first.
goto :end_fail
:plan

rem ---- Say exactly what is about to happen, then make a human agree -----------
echo REPAIR STICK - the stick's settings and setup state will be restored
echo to factory condition. The stick is: %STICK%
echo.
echo RESTORED from the factory copies in SharedFolders\usr\:
echo   every file in SharedSettings\ (hand edits to them are lost)
echo DELETED:
echo   db-credentials.php and Ports.InUse.conf (setup writes fresh ones),
echo   everything in SharedFolders\data\ and SharedFolders\tmp\, log residue.
echo RENAMED BACK: a spent z.SetupAlreadyRan.* pair becomes 1stRun.Setup.*
echo NEVER TOUCHED: your databases and their contents, www\, the
echo   program files. Your current passwords keep working until you run
echo   1stRun.Setup again - running it is the final step of the repair.
echo.
set "ANSWER="
set /p ANSWER=Type REPAIR to proceed, anything else to abort:
if "%ANSWER%"=="REPAIR" goto :confirmed
echo Aborted. Nothing was changed.
goto :end_fail
:confirmed

rem ---- 1) Stage the factory copies and verify them BEFORE touching anything -
if exist "%STAGE%" rd /s /q "%STAGE%"
md "%STAGE%" 2>nul
for %%F in (%FACTORY_FILES%) do (
    set "CURF=%%F"
    if not exist "%PAYLOAD%\%%F.zip" goto :stage_missing
    powershell -NoProfile -Command "Expand-Archive -LiteralPath '%PAYLOAD%\%%F.zip' -DestinationPath '%STAGE%' -Force" >nul 2>&1
    if errorlevel 1 goto :stage_extract
    if not exist "%STAGE%\%%F" goto :stage_content
)
goto :stage_ok
:stage_missing
echo *** Repair stopped: the factory copy SharedFolders\usr\%CURF%.zip is missing. Nothing was changed - restore the usr\ folder (it ships with the kit) and run the repair again.
goto :end_fail
:stage_extract
echo *** Repair stopped: the factory copy SharedFolders\usr\%CURF%.zip could not be extracted - damaged? Nothing was changed.
goto :end_fail
:stage_content
echo *** Repair stopped: the factory copy SharedFolders\usr\%CURF%.zip did not contain %CURF%. Nothing was changed.
goto :end_fail
:stage_ok

rem ---- 2) Restore SharedSettings ----------------------------------------------
for %%F in (%FACTORY_FILES%) do (
    set "CURF=%%F"
    copy /y "%STAGE%\%%F" "%STICK%\SharedSettings\%%F" >nul
    if errorlevel 1 goto :restore_fail
)
goto :restore_ok
:restore_fail
echo *** Repair FAILED while restoring SharedSettings\%CURF%. %PARTIAL%
goto :end_fail
:restore_ok

rem ---- 3) Delete the per-stick state files ------------------------------------
del /q /f "%STICK%\SharedSettings\db-credentials.php" 2>nul
del /q /f "%STICK%\SharedSettings\Ports.InUse.conf" 2>nul

rem ---- 4) Empty the runtime folders, back to their first-run state ------------
rem (usr\ is NOT under either of these - the factory copies survive.)
del /q /f "%STICK%\SharedFolders\data\*" 2>nul
for /d %%D in ("%STICK%\SharedFolders\data\*") do rd /s /q "%%D" 2>nul
del /q /f "%STICK%\SharedFolders\tmp\*" 2>nul
for /d %%D in ("%STICK%\SharedFolders\tmp\*") do rd /s /q "%%D" 2>nul
type nul > "%STICK%\SharedFolders\data\tmp"
type nul > "%STICK%\SharedFolders\tmp\tmp"
if exist "%STICK%\SharedFolders\data\.setup-done" goto :partial_done
goto :residue
:partial_done
echo *** Repair FAILED: .setup-done is still present after clearing SharedFolders\data\. %PARTIAL%
goto :end_fail
:residue

rem ---- 5) Log and pid residue in the OS trees ---------------------------------
del /q /f /s "%STICK%\OS_Windows\Software\apache-server\logs\*" 2>nul
del /q /f /s "%STICK%\OS_Linux\Software\apache-server\logs\*" 2>nul
del /q /f "%STICK%\OS_Linux\Software\php-desktop\php-fpm.pid" 2>nul
del /q /f "%STICK%\mariadb-data\*.err" 2>nul

rem ---- 6) The setup entry points get their fresh names back -------------------
if exist "%STICK%\z.SetupAlreadyRan.bat" if not exist "%STICK%\1stRun.Setup.bat" ren "%STICK%\z.SetupAlreadyRan.bat" 1stRun.Setup.bat
if exist "%STICK%\z.SetupAlreadyRan.desktop" if not exist "%STICK%\1stRun.Setup.desktop" ren "%STICK%\z.SetupAlreadyRan.desktop" 1stRun.Setup.desktop
if exist "%STICK%\z.SetupAlreadyRan.bat" if exist "%STICK%\1stRun.Setup.bat" echo NOTE: both z.SetupAlreadyRan.bat and 1stRun.Setup.bat exist - leaving both as they are.
if exist "%STICK%\z.SetupAlreadyRan.desktop" if exist "%STICK%\1stRun.Setup.desktop" echo NOTE: both z.SetupAlreadyRan.desktop and 1stRun.Setup.desktop exist - leaving both as they are.

echo.
echo Repair complete. Settings and setup state are factory-fresh; your
echo databases, projects and program files were not touched.
echo Next step: run 1stRun.Setup and choose new passwords - until you
echo do, the current passwords keep working.
goto :end_ok

:end_fail
echo.
pause
exit /b 1
:end_ok
echo.
pause
exit /b 0
