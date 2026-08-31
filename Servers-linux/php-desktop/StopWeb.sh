#!/bin/bash
cd "$(dirname "$0")"
echo "🛑 Stopping Portable PHP Development Web Server..."
# This finds and kills the specific php built-in server process on port 8000
pkill -f "php-bin -S 127.0.0.1:8000"
