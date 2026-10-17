#!/bin/bash
cd "$(dirname "$0")"

echo "🧹 Cleaning stale locks..."
pkill -9 -f mariadbd

# LIVE BOOT COMPATIBILITY LAYER
# Check if the host system lacks libcrypt.so.1 but has the modern version .2
if [ ! -f "/usr/lib/libcrypt.so.1" ] && [ -f "/usr/lib/libcrypt.so.2" ]; then
    echo "🛠️ Live environment detected (Missing libcrypt.so.1). Injecting local compatibility layer..."
    # Create a local 'libs' directory inside your portable database folder if it doesn't exist
    mkdir -p ./libs
    # Safely map a local symbolic link pointing modern .2 to the name version .1 expects
    ln -sf /usr/lib/libcrypt.so.2 ./libs/libcrypt.so.1
    # Tell the Linux linker to prioritize looking inside our local folder first!
    export LD_LIBRARY_PATH="$(pwd)/libs:$LD_LIBRARY_PATH"
fi

echo "🚀 Booting Portable MariaDB Server..."
./bin/mariadbd --defaults-file=../../SharedSettings/mariadb-configs.cnf --socket=/tmp/portable-mariadb.sock --user=root
