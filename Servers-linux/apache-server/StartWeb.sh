#!/bin/bash
# Automatically anchor execution context relative to this directory pathway
cd "$(dirname "$0")"

echo "🧹 Sweeping hanging web tasks clean..."
pkill -9 -f bin/httpd || true
pkill -9 -f php-bin || true
pkill -9 -f php-fpm || true
pkill -9 -f redis-server || true
pkill -9 -f node-desktop || true
killall -9 redis-server php-fpm httpd php-bin node 2>/dev/null || true

# 🌟 ADD THIS LINE: Force remove any stale Apache pid lock file structures
rm -f logs/httpd.pid || true
sleep 1

echo "🧠 Booting Isolated Portable Redis Cache Server on Port 6379..."
../redis-server/bin/redis-server ../../SharedSettings/redis.conf &
sleep 1

echo "⚡ Booting Silent Portable PHP FastCGI Gateway on Port 9000..."
CONF_DIR="$(cd ../../SharedSettings && pwd)"

../php-desktop/php-bin \
    -c "$CONF_DIR" \
    -d opcache.enable=1 \
    -d opcache.enable_cli=1 \
    -d opcache.memory_consumption=128 \
    -d opcache.interned_strings_buffer=8 \
    -d opcache.max_accelerated_files=10000 \
    -d opcache.validate_timestamps=1 \
    -d opcache.revalidate_freq=2 \
    -d opcache.jit=1255 \
    -d opcache.jit_buffer_size=32M \
    -d memory_limit=256M \
    -d output_buffering=4096 \
    -y "$CONF_DIR/php-fpm.conf" -D
sleep 1

if [ -f "../../SharedSettings/node-app.js" ]; then
    echo "🟩 Booting Silent Portable JavaScript Backend Runtime Engine on Port 3000..."
    ../node-desktop/bin/node "../../SharedSettings/node-app.js" &
    sleep 1
fi

echo "🌐 Opening Default Web Browser..."
# Target your main original 8080 address space layout
xdg-open "http://127.0.0.1:3000"

echo "🚀 Booting Portable Static Apache Web Server on Port 8080..."
USB_ROOT="$(cd ../.. && pwd)"

./bin/httpd -D LIN -D FOREGROUND \
    -d "$USB_ROOT/Servers-linux/apache-server" \
    -f "$USB_ROOT/SharedSettings/httpd.conf"
