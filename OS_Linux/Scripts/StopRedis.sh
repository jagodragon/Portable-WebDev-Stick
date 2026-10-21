#!/bin/bash
# Stop Redis the graceful way: redis-cli SHUTDOWN saves the RDB snapshot first.
# No pid file needed. (After the setup script adds auth, this needs: redis-cli -a <password> shutdown)
cd "$(dirname "$0")"
USB_ROOT="$(cd ../.. && pwd)"

# Ports: environment, then Ports.InUse.conf, then Ports.Default.conf.
. "$(dirname "$0")/ports-resolve.sh"
resolve_ports

echo "🛑 Stopping Portable Redis..."
if "$USB_ROOT/OS_Linux/Software/redis-server/bin/redis-cli" -h 127.0.0.1 -p "$PORT_REDIS" shutdown 2>/dev/null; then
    echo "✅ Redis stopped (snapshot saved)."
else
    echo "ℹ️ Redis did not answer on 127.0.0.1:$PORT_REDIS — already stopped?"
fi
