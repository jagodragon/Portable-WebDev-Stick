#!/bin/bash
cd "$(dirname "$0")"
echo "🛑 Safely shutting down Portable MariaDB Server Engine..."
./bin/mariadb-admin -u jago_dragon -h 127.0.0.1 --port=3306 shutdown
