#!/bin/bash
# Stop PHP-FPM by pid file — never by process name.
# php-fpm.conf [global] sets `pid = php-fpm.pid`, which FPM resolves against
# the prefix StartApache.sh passes with -p (the php-desktop folder), so the
# master writes its pid to <php-desktop>/php-fpm.pid.
# SIGQUIT is FPM's graceful shutdown (finishes in-flight requests first);
# SIGTERM, then -9, are the fallbacks.
cd "$(dirname "$0")"
USB_ROOT="$(cd ../.. && pwd)"
PID_FILE="$USB_ROOT/OS_Linux/Software/php-desktop/php-fpm.pid"

echo "🛑 Stopping Portable PHP-FPM..."
if [ -f "$PID_FILE" ]; then
    PID=$(cat "$PID_FILE")
    if [ -n "$PID" ] && kill -0 "$PID" 2>/dev/null; then
        kill -QUIT "$PID"
        for i in 1 2 3 4 5; do kill -0 "$PID" 2>/dev/null || break; sleep 1; done
        if kill -0 "$PID" 2>/dev/null; then
            kill "$PID"
            for i in 1 2 3; do kill -0 "$PID" 2>/dev/null || break; sleep 1; done
            kill -0 "$PID" 2>/dev/null && kill -9 "$PID"
        fi
        echo "✅ PHP-FPM stopped (pid $PID)."
    else
        echo "ℹ️ Stale pid file — PHP-FPM is not running. Cleaning up."
    fi
    rm -f "$PID_FILE"
else
    echo "ℹ️ No pid file — PHP-FPM was not started by this stack, or is already stopped."
fi
