#!/bin/bash
# Lock terminal execution context strictly to this folder context on the USB drive
cd "$(dirname "$0")"

echo "⚡ Booting Isolated Portable PHP Engine on Internal Port 9000..."
# Run the local binary using its native server flag and push it to the background
./php-bin -S 127.0.0.1:9000 -t ../../www &

# GIVE PHP EXACTLY ONE SECOND TO INITIALIZE AND WAKE UP
sleep 1

echo "🌐 Opening Default Web Browser..."
# Force the system to open your browser automatically to Caddy's port layout
xdg-open http://127.0.0.1:8080

echo "🚀 Booting Portable Caddy Reverse Proxy Server Engine..."
# Move into the server folder and launch Caddy in the foreground
cd ../caddy-server
./caddy_linux_amd64 run --config ../../SharedSettings/Caddyfile
