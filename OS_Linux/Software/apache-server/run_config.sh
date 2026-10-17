#!/bin/bash
set -e

echo "🧹 Step 1: Purging old directories and extracting fresh source..."
cd /run/media/jago_dragon/WebDev/PHP-Build
rm -rf php-8.4.4/
tar -xf php-8.4.4.tar.xz
cd php-8.4.4

echo "⚙️ Step 2: Launching Loaded PHP-FPM Configuration Matrix..."
PKG_CONFIG="/bin/true" \
LIBXML_LIBS="-L/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/libxml2-static/lib -lxml2" \
LIBXML_CFLAGS="-I/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/libxml2-static/include/libxml2" \
OPENSSL_LIBS="-L/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/openssl-static/lib64 -lssl -lcrypto -ldl" \
OPENSSL_CFLAGS="-I/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/openssl-static/include" \
CURL_LIBS="-L/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/curl-static/lib -L/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/openssl-static/lib64 -L/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/zlib-static/lib -lcurl -lssl -lcrypto -lz -ldl" \
CURL_CFLAGS="-I/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/curl-static/include" \
CFLAGS="-I/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/zlib-static/include -I/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/openssl-static/include -I/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/curl-static/include" \
LDFLAGS="-static -static-pie -s" \
CC="musl-gcc -static -static-pie" ./configure \
    --prefix=/run/media/jago_dragon/WebDev/Servers-linux/php-desktop \
    --with-layout=GNU \
    --without-pear \
    --disable-shared \
    --enable-static \
    --disable-all \
    --enable-fpm \
    --enable-embed=static \
    --enable-mbstring \
    --disable-mbregex \
    --enable-bcmath \
    --enable-sockets \
    --enable-opcache \
    --enable-opcache-jit \
    --enable-pdo \
    --with-mysqlnd \
    --with-pdo-mysql=mysqlnd \
    --with-libxml \
    --with-zlib=/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/zlib-static \
    --with-openssl=/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/openssl-static \
    --with-curl=/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/curl-static
