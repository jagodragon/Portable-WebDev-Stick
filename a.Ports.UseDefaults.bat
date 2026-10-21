@echo off
setlocal EnableExtensions EnableDelayedExpansion
rem ===========================================================================
rem  a.Ports.UseDefaults.bat - return the stack to its default ports (Windows).
rem
rem  Deletes SharedSettings\Ports.InUse.conf. From then on every start/stop
rem  script resolves its ports from SharedSettings\Ports.Default.conf again.
rem
rem  Run it while the stack is STOPPED. It refuses to run while a stack server
rem  is alive: the stop scripts resolve ports from the same files, and they
rem  must always be able to find the servers they started.
rem ===========================================================================

set "USB_ROOT=%~dp0"
set "INUSE=%USB_ROOT%SharedSettings\Ports.InUse.conf"
set "DATA_DIR=%USB_ROOT%SharedFolders\data"

if exist "%DATA_DIR%" (
    for %%F in ("%DATA_DIR%\*.pid") do (
        set "PID="
        set /p PID=<"%%F"
        if defined PID (
            tasklist /FI "PID eq !PID!" /NH 2>nul | findstr /B /C:"!PID!" >nul
            if not errorlevel 1 (
                echo *** a stack server is still running ^(pid !PID!^). Stop the stack first,
                echo *** then run this again.
                goto :fail
            )
        )
    )
)

if not exist "%INUSE%" (
    echo Ports.InUse.conf is already absent - the stack is on its default ports.
    goto :end
)

findstr /B /C:"[InUsePorts]" "%INUSE%" >nul
if errorlevel 1 (
    echo No port decision is in force - the stack is on its default ports.
    goto :end
)

del "%INUSE%"
if errorlevel 1 (
    echo *** could not delete Ports.InUse.conf.
    goto :fail
)
echo Ports.InUse.conf deleted. The launchers will use Ports.Default.conf.
echo a.Ports.FixConflicts will generate a fresh Ports.InUse.conf if you
echo ever need one again.
goto :end

:fail
pause
endlocal
exit /b 1

:end
pause
endlocal
exit /b 0
