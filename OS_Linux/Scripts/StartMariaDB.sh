#!/bin/bash
cd "$(dirname "$0")"
# FIX (item 8): USB_ROOT is assigned AND exported here, in the same scope that expands it.
USB_ROOT="$(cd ../.. && pwd)"
export USB_ROOT

# Ports: environment, then Ports.InUse.conf, then Ports.Default.conf.
. "$(dirname "$0")/ports-resolve.sh"
resolve_ports

echo "🧹 Checking for a previous instance of this stack's MariaDB..."
# If some other MariaDB owns the configured MariaDB port, mariadbd below will fail to bind with a clear
# error — which is correct: report the conflict, never clear the field by name.
PID_FILE="$USB_ROOT/SharedFolders/data/mariadb.pid"
if [ -f "$PID_FILE" ]; then
    OLD_PID=$(cat "$PID_FILE")
    if [ -n "$OLD_PID" ] && kill -0 "$OLD_PID" 2>/dev/null; then
        echo "Previous instance still running (pid $OLD_PID) — stopping it first."
        kill "$OLD_PID"
        for i in 1 2 3 4 5; do kill -0 "$OLD_PID" 2>/dev/null || break; sleep 1; done
        kill -0 "$OLD_PID" 2>/dev/null && kill -9 "$OLD_PID"
    fi
    rm -f "$PID_FILE"
fi

# LIVE BOOT COMPATIBILITY LAYER
# Check if the host system lacks libcrypt.so.1 but has the modern version .2
if [ ! -f "/usr/lib/libcrypt.so.1" ] && [ -f "/usr/lib/libcrypt.so.2" ]; then
    echo "🛠️ Live environment detected (Missing libcrypt.so.1). Injecting local compatibility layer..."
    # Create a local 'libs' directory inside your portable database folder if it doesn't exist
    mkdir -p ./../Software/mariadb/libs
    # Safely map a local symbolic link pointing modern .2 to the name version .1 expects
    ln -sf /usr/lib/libcrypt.so.2 ./../Software/mariadb/libs/libcrypt.so.1
    # Tell the Linux linker to prioritize looking inside our local folder first!
    export LD_LIBRARY_PATH="$(pwd)/../Software/mariadb/libs:$LD_LIBRARY_PATH"
fi

echo "🚀 Booting Portable MariaDB Server..."
# FIX (item 8): --datadir quoted (paths with spaces) and taken from USB_ROOT.
# FIX (item 15): --pid-file added so StopMariaDB.sh can stop THIS server by pid file.
# FIX (first Linux boot, Oct 17): --defaults-file MUST be the first argument —
# MariaDB only honors it in first position; anywhere else it aborts with
# "unknown variable 'defaults-file=...'". (The Windows .bat always had it first.)
./../Software/mariadb/bin/mariadbd --defaults-file=../../SharedSettings/mariadb-configs.cnf --datadir="$USB_ROOT/mariadb-data" --pid-file="$USB_ROOT/SharedFolders/data/mariadb.pid" --socket=/tmp/portable-mariadb.sock --port="$PORT_MARIADB" --user=root
