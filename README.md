# 🚀 Portable Multi-Platform WebDev Sandbox Station

A self-contained, portable full-stack web development environment that runs directly from a USB drive on both Windows and Linux without requiring installation, administrator privileges, or modifications to the host operating system.

Develop on Windows. Unplug the drive. Plug it into Linux. Continue exactly where you left off using the same databases, project files, and configuration.

---

# ✨ Features

## Cross-Platform Development

- Windows x64 support
- Linux x86_64 support
- Shared project files
- Shared database storage
- Shared configuration files

## Web Servers

- Apache HTTP Server
- Caddy Web Server (Backup/Fallback Server)

## Backend Development

- PHP
- Node.js
- Python

## Databases

- MariaDB
- Redis

## Development Tools

- DBeaver Portable

## Portable Design

- No installation required
- No system package modifications
- Runs entirely from removable storage
- Shared data and project workspace between operating systems

---
# 📁 Project Structure
```text
WebDev
├── mariadb-data              # Shared MariaDB data storage
├── SharedLibs                # Shared libraries used by multiple services
├── SharedSettings            # Shared cross-platform configuration files
├── www                       # Application source code
├── Servers-linux
│   ├── apache-server
│   ├── caddy-server
│   ├── mariadb
│   ├── node-desktop
│   ├── php-desktop
│   ├── python
│   └── redis-server
├── Servers-Win
│   ├── apache-server
│   ├── caddy-server
│   ├── mariadb
│   ├── node-desktop
│   ├── php-desktop
│   ├── python
│   └── redis-server
├── software-linux
│   └── dbeaver-portable
└── Software-win
    └── dbeaver-portable
```
---

# 🔧 Shared Configuration Files
The SharedSettings folder contains configuration files used by both Windows and Linux environments.
---
```text
SharedSettings/
├── Caddyfile
├── httpd.conf
├── httpd-ssl.conf
├── httpd-vhosts.conf
├── mariadb-configs.cnf
├── node-app.js
├── php.ini
├── php-fpm.conf
├── python-env.sh
└── redis.conf
```
---

# ⚡ Quick Start (Windows)

## Start MariaDB

Run:

win.StartDB.bat

## Start Development Web Server

Run:

win.StartProductionServer.bat

Then browse to:
```text
http://127.0.0.1:8080
```
## If Apache Fails

Run:

win.StartBackupServer-Caddy.bat

## Stop Services

Run:

win.StopProductionServer.bat

Run:

win.StopDB.bat

---

# ⚡ Quick Start (Linux)

## Start MariaDB

Run:

linux.StartDB.desktop

## Start Development Web Server

Run:

linux.StartProductionServer.desktop

Then browse to:
```text
http://127.0.0.1:8080
```
## If Apache Fails

Run:

linux.StartBackupServer-Caddy.desktop

## Stop Services

Run:

linux.StopProductionServer.desktop

Run:

linux.StopDB.desktop

---

# 🔄 Cross Platform Workflow

1. Connect the USB drive to a Windows computer.
2. Start MariaDB.
3. Start the production web server.
4. Develop normally.
5. Shut down services.
6. Eject the USB drive.
7. Plug the USB drive into a Linux computer.
8. Start services.
9. Continue developing using the same files, databases, and configuration.

No export/import process is required.

---

# 🗄️ Database Access Example
```text
config.php

<?php

$host    = '127.0.0.1';
$db      = 'your_database_name';
$user    = 'webdev';
$pass    = '';
$port    = 3306;
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;port=$port;charset=$charset";

$pdo = new PDO(
    $dsn,
    $user,
    $pass,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]
);
```
---

# 🎯 Project Goals

- Portable
- Cross-platform
- Self-contained
- Full-stack capable
- USB-friendly
- No host system modifications
- Easy to back up
- Easy to move between machines

---

# 📋 Current Status

Active development.

The stack currently includes:

✅ Apache  
✅ Caddy  
✅ MariaDB  
✅ Redis  
✅ PHP  
✅ Node.js  
✅ Python  
✅ DBeaver Portable  
✅ Windows Support  
✅ Linux Support
