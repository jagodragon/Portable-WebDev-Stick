#!/bin/bash
# Stop MariaDB by pid file — never by process name.
# Prerequisite: StartMariaDB.sh passes --pid-file="$USB_ROOT/SharedFolders/data/mariadb.pid"
cd "$(dirname "$0")"
USB_ROOT="$(cd ../.. && pwd)"
PID_FILE="$USB_ROOT/SharedFolders/data/mariadb.pid"

echo "🛑 Stopping Portable MariaDB Server..."
if [ -f "$PID_FILE" ]; then
    PID=$(cat "$PID_FILE")
    if [ -n "$PID" ] && kill -0 "$PID" 2>/dev/null; then
        kill "$PID"   # SIGTERM — MariaDB treats this as a clean shutdown request
        for i in 1 2 3 4 5 6 7 8 9 10; do
            kill -0 "$PID" 2>/dev/null || break
            sleep 1
        done
        if kill -0 "$PID" 2>/dev/null; then
            echo "⚠️ Still running after 10s — forcing."
            kill -9 "$PID"
        fi
        echo "✅ MariaDB stopped (pid $PID)."
    else
        echo "ℹ️ Pid file found, but process $PID is not running — clearing the stale file."
    fi
    rm -f "$PID_FILE"
else
    echo "ℹ️ No pid file — MariaDB was not started by this stack, or is already stopped."
fi
