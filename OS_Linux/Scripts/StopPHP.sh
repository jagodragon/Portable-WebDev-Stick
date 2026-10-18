#!/bin/bash
# Stop the portable PHP built-in server.
# "php-bin" is this stack's own binary name (not a stock host binary) and the
# launch arguments are unique to this stack, so this full-command-line match
# cannot hit a host machine's PHP. If you later capture a pid at launch
# (php-bin ... & echo $! > php.pid), switch this to the pid-file pattern.
cd "$(dirname "$0")"

echo "🛑 Stopping Portable PHP Development Web Server..."
if pkill -f "php-bin -S 127.0.0.1:8000"; then
    echo "✅ PHP server stopped."
else
    echo "ℹ️ No matching PHP server found — already stopped?"
fi
