#!/bin/bash
# Stop Apache by pid file — never by process name.
# Prerequisite: httpd.conf sets  PidFile "${USB_ROOT}/SharedFolders/Data/httpd.pid"
cd "$(dirname "$0")"
USB_ROOT="$(cd ../.. && pwd)"
PID_FILE="$USB_ROOT/SharedFolders/Data/httpd.pid"

echo "🛑 Stopping Portable Apache Web Server..."
if [ -f "$PID_FILE" ]; then
    PID=$(cat "$PID_FILE")
    if [ -n "$PID" ] && kill -0 "$PID" 2>/dev/null; then
        kill "$PID"   # SIGTERM — Apache's graceful stop signal for the parent process
        for i in 1 2 3 4 5; do
            kill -0 "$PID" 2>/dev/null || break
            sleep 1
        done
        kill -0 "$PID" 2>/dev/null && kill -9 "$PID"
        echo "✅ Apache stopped (pid $PID)."
    else
        echo "ℹ️ Pid file found, but process $PID is not running — clearing the stale file."
    fi
    rm -f "$PID_FILE"
else
    echo "ℹ️ No pid file — Apache was not started by this stack, or is already stopped."
fi
