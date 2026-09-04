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

## Backend

- PHP
- Node.js
- Python
- Redis

## Databases

- MariaDB

## Development Tools

- DBeaver Portable

## Portable Design

- No installation required
- No system package modifications
- Runs entirely from removable storage
- Shared data and project workspace between operating systems

## Supported Filesystem
```text
✓ ExFAT (Recommended) - Stable and reliable for Linux and Windows
✓ NTFS (Not Recommended) - Unreliable on Linux but might work depending on your Distro.
```

## Unsupported Filesystems
these file system really only work for one operating system
```text
✗ FAT32 - Windows Only
  -Does not preserve Linux file permisions or symbolic links.
  -The Linux servers in this package will not work with this format.
✗ VFAT  - Windows Only
  -Does not preserve Linux file permisions or symbolic links.
  -The Linux servers in this package will not work with this format.
✗ ext4  - Linux Only Filesystem
✗ Btrfs - Linux Only Filesystem
✗ UDF   - May work but really not suitable for this project
```

---
# 📁 Project Structure
```text
WebDev
├── mariadb-data              # Shared MariaDB data storage
├── OS_Linux                  # Single Directory for all things Linux sepcific
│   ├── DesktopApps           # Preloaded desktop application(s)
│   │   └── dbeaver-portable
│   ├── Scripts               # Single point storage for all linux script
│   └── Software              # All software required for Linux server to run
│       ├── apache-server
│       ├── caddy-server
│       ├── mariadb
│       ├── node-desktop
│       ├── php-desktop
│       ├── python
│       └── redis-server
├── OS_Windows                # Single Directory for all things Windows sepcific
│   ├── DesktopApps           # Preloaded desktop application(s)
│   │   └── dbeaver-portable
│   ├── Scripts               # Single point storage for all Windows script
│   └── Software              # All software required for Windows server to run
│       ├── apache-server
│       ├── caddy-server
│       ├── mariadb
│       ├── node-desktop
│       ├── php-desktop
│       ├── python
│       └── redis-server
├── SharedSettings            # Shared cross-platform configuration files
└── www                       # WebHost files

```
---

# 🔧 Shared Configuration Files
The SharedSettings folder contains configuration files used by both Windows and Linux environments.
---
```text
SharedSettings/
├── Caddyfile
├── httpd.conf
├── httpd-ssl.conf (not configured yet)
├── httpd-vhosts.conf (not configured yet)
├── mariadb-configs.cnf
├── node-app.js
├── php.ini
├── php-fpm.conf
├── python-env.sh
└── redis.conf
```
💡 Quick Linux Mounting Tip:
If your Linux distribution mounts the ExFAT partition without execution tags by default, mount it via your terminal using mask options to enforce drive-wide execution privileges seamlessly:

sudo mount -t exfat /dev/sdX1 /media/webdev -o uid=1000,gid=1000,fmask=000,dmask=000
---

# ⚡ Quick Start (Windows)

## Start MariaDB

Run:

win.StartMariaDB.bat

## Start Development Web Server

Run:

win.StartPrimaryWebServer_Apache.bat
or
win.StartSecondaryWebServer_Caddy.bat

Then browse to:
```text
http://127.0.0.1:8080
```

## Stop Services

Run:

win.StopPrimaryWebServer_Apache.bat
or
win.StopSecondaryWebServer_Caddy.bat

Run:

win.StopMariaDB.bat

---

# ⚡ Quick Start (Linux)

## Start MariaDB

Run:

linux.StartMariaDB.desktop

## Start Development Web Server

Run:

Linux.StartPrimaryWebServer_Apache.desktop
or
Linux.StartSecondaryWebServer_Caddy.desktop

Then browse to:
```text
http://127.0.0.1:8080
```

## Stop Services

Run:

Linux.StopPrimaryWebServer_Apache.desktop
or
Linux.StopSecondaryWebServer_Caddy.desktop

Run:

Linux.StopMariaDB.desktop

---

# 🔄 Cross Platform Workflow

1. Connect the USB drive to a Windows computer.
2. Start MariaDB.
3. Start the prefered web server.
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
✅ Jago pronouncing "conf" like a psychopath

# 📋 Todo

- [ ] Add reverse proxy for Node.js
- [ ] Implement Java for web apps (probably Tomcat)
- [ ] Add Composer to the toolbox for web devs
- [ ] Add Symfony to the toolbox for web devs
