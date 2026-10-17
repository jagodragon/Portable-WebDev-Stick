/*
   +----------------------------------------------------------------------+
   | Copyright (c) The PHP Group                                          |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | https://www.php.net/license/3_01.txt                                 |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Author: Stig Sæther Bakken <ssb@php.net>                             |
   +----------------------------------------------------------------------+
*/

#define CONFIGURE_COMMAND " './configure'  '--prefix=/run/media/jago_dragon/WebDev/Servers-linux/php-desktop' '--with-layout=GNU' '--without-pear' '--disable-shared' '--enable-static' '--disable-all' '--enable-fpm' '--enable-embed=static' '--enable-mbstring' '--disable-mbregex' '--enable-bcmath' '--enable-sockets' '--enable-opcache' '--enable-pdo' '--with-mysqlnd' '--with-pdo-mysql=mysqlnd' '--with-libxml' '--with-zlib=/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/zlib-static' '--with-openssl=/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/openssl-static' '--with-curl=/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/curl-static' 'PKG_CONFIG=/bin/true' 'LIBXML_CFLAGS=-I/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/libxml2-static/include/libxml2' 'LIBXML_LIBS=-L/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/libxml2-static/lib -lxml2' 'OPENSSL_CFLAGS=-I/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/openssl-static/include' 'OPENSSL_LIBS=-L/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/openssl-static/lib64 -lssl -lcrypto -ldl' 'CURL_CFLAGS=-I/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/curl-static/include' 'CURL_LIBS=-L/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/curl-static/lib -L/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/openssl-static/lib64 -L/run/media/jago_dragon/WebDev/Servers-linux/SharedLibs/zlib-static/lib -lcurl -lssl -lcrypto -lz -ldl'"
#define PHP_ODBC_CFLAGS	""
#define PHP_ODBC_LFLAGS		""
#define PHP_ODBC_LIBS		""
#define PHP_ODBC_TYPE		""
#define PHP_PROG_SENDMAIL	"/usr/sbin/sendmail"
#define PEAR_INSTALLDIR         ""
#define PHP_INCLUDE_PATH	".:"
#define PHP_EXTENSION_DIR       "/run/media/jago_dragon/WebDev/Servers-linux/php-desktop/lib/php/20240924"
#define PHP_PREFIX              "/run/media/jago_dragon/WebDev/Servers-linux/php-desktop"
#define PHP_BINDIR              "/run/media/jago_dragon/WebDev/Servers-linux/php-desktop/bin"
#define PHP_SBINDIR             "/run/media/jago_dragon/WebDev/Servers-linux/php-desktop/sbin"
#define PHP_MANDIR              "/run/media/jago_dragon/WebDev/Servers-linux/php-desktop/share/man"
#define PHP_LIBDIR              "/run/media/jago_dragon/WebDev/Servers-linux/php-desktop/lib/php"
#define PHP_DATADIR             "/run/media/jago_dragon/WebDev/Servers-linux/php-desktop/share/php"
#define PHP_SYSCONFDIR          "/run/media/jago_dragon/WebDev/Servers-linux/php-desktop/etc"
#define PHP_LOCALSTATEDIR       "/run/media/jago_dragon/WebDev/Servers-linux/php-desktop/var"
#define PHP_CONFIG_FILE_PATH    "/run/media/jago_dragon/WebDev/Servers-linux/php-desktop/etc"
#define PHP_CONFIG_FILE_SCAN_DIR    ""
#define PHP_SHLIB_SUFFIX        "so"
#define PHP_SHLIB_EXT_PREFIX    ""
