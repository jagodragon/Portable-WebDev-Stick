#!/bin/bash
cd "$(dirname "$0")"
echo "🛑 Stopping Production Caddy and FastCGI Processes..."
# Safely kill the Caddy engine proxy first
pkill -f caddy_linux_amd64
# Clean up the background FastCGI process manager
pkill -f "php-bin -b 127.0.0.1:8000"
# Clean up any residual routing paths mapped to port 9000
pkill -f "php-bin -S 127.0.0.1:9000"
