#!/bin/bash
cd "$(dirname "$0")"

echo "🛑 Stopping Portable Apache Web Server..."
pkill -f bin/httpd

echo "🧹 Sweeping background web engine processors..."
pkill -f php-bin
fuser -k 9000/tcp
