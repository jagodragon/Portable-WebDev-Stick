#!/bin/bash
# Stop Caddy via its own admin API ("caddy stop") — no process killing at all.
# Adjust CADDY_BIN if your binary lives somewhere else in the tree.
cd "$(dirname "$0")"
USB_ROOT="$(cd ../.. && pwd)"
CADDY_BIN="$USB_ROOT/OS_Linux/Software/caddy/caddy_linux_amd64"

echo "🛑 Stopping Portable Caddy..."
if [ -x "$CADDY_BIN" ]; then
    "$CADDY_BIN" stop 2>/dev/null && echo "✅ Caddy stopped." || echo "ℹ️ Caddy did not answer — already stopped?"
else
    echo "⚠️ Caddy binary not found at $CADDY_BIN — edit CADDY_BIN at the top of this script."
fi
