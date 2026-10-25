#!/bin/bash
# Automatically anchor execution context relative to this directory pathway
# fuser -k 9000/tcp || true

cd "$(dirname "$0")"

USB_ROOT="$(cd ../.. && pwd)"
export USB_ROOT

# Ports: environment, then Ports.InUse.conf, then Ports.Default.conf.
. "$(dirname "$0")/ports-resolve.sh"
resolve_ports  # also exports WEBDEV_PORT_* for httpd, FPM and Node

# Portable Python Runtime
export PYTHON_HOME="$USB_ROOT/OS_Linux/Software/python"
export PYTHONNOUSERSITE=1

export PATH="$PYTHON_HOME/bin:$PATH"

echo "🐍 Python Runtime: $(python3 --version)"
echo "🐍 Python Location: $(which python3)"
echo "🐍 Pip: $(python3 -m pip --version)"

echo "🧹 Sweeping hanging web tasks clean..."
#pkill -9 -f bin/httpd || true
#pkill -9 -f php-bin || true
#pkill -9 -f php-fpm || true
#pkill -9 -f redis-server || true
#pkill -9 -f node-desktop || true
#pkillall -9 redis-server php-fpm httpd php-bin node 2>/dev/null || true
# Item 15: stop OUR OWN previous instances by pid file — never by name, image, or port.
stop_ours() {  # $1 = service name, $2 = pid file
    local name="$1" pidfile="$2" pid=""
    [ -f "$pidfile" ] && pid="$(cat "$pidfile")"
    if [ -n "$pid" ] && kill -0 "$pid" 2>/dev/null; then
        echo "Previous $name instance still running (pid $pid) - stopping it first."
        kill "$pid"
        for _ in 1 2 3 4 5; do kill -0 "$pid" 2>/dev/null || break; sleep 1; done
        kill -0 "$pid" 2>/dev/null && kill -9 "$pid"
    fi
    rm -f "$pidfile"
}
DATA_DIR="$USB_ROOT/SharedFolders/data"
stop_ours "Apache"  "$DATA_DIR/httpd.pid"

# 🌟 ADD THIS LINE: Force remove any stale Apache pid lock file structures
rm -f logs/httpd.pid || true
sleep 1

echo "🧠 Booting Isolated Portable Redis Cache Server on Port $PORT_REDIS..."
../Software/redis-server/bin/redis-server ../../SharedSettings/redis.conf --port "$PORT_REDIS" --dir "$DATA_DIR" &
sleep 1

echo "⚡ Booting Silent Portable PHP FastCGI Gateway on Port $PORT_FPM..."
CONF_DIR="$(cd ../../SharedSettings && pwd)"

../Software/php-desktop/php-fpm \
    -p "$USB_ROOT/OS_Linux/Software/php-desktop" \
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
    echo "🟩 Booting Silent Portable JavaScript Backend Runtime Engine on Port $PORT_NODE..."
    ../Software/node-desktop/bin/node "../../SharedSettings/node-app.js" &
    sleep 1
fi

echo "🌐 Opening Default Web Browser..."
# Target your main original 8080 address space layout
xdg-open "http://127.0.0.1:$PORT_NODE"
xdg-open "http://127.0.0.1:$PORT_WEB"

echo "🚀 Booting Portable Static Apache Web Server on Port $PORT_WEB..."
export USB_ROOT="$(cd ../.. && pwd)"
./../Software/apache-server/bin/httpd -D LIN -D FOREGROUND \
    -d "$USB_ROOT/OS_Linux/Software/apache-server" \
    -f "$USB_ROOT/SharedSettings/httpd.conf"
