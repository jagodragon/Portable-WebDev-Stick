# 🚀 Portable Multi-Platform WebDev Sandbox Station

A 100% self-contained, zero-dependency local development workspace designed to run entirely off a portable USB drive. This repository bundles a relational MariaDB database engine and a high-performance multi-threaded Caddy web proxy server layer supporting isolated PHP script rendering across both **Linux (x86_64)** and **Windows (x64)** environments without altering local host system packages.

## 🗂️ Workspace Architecture Blueprint

```text
WebDev/
├── mariadb-data/                     # Shared database storage layers (InnoDB)
├── Servers-linux/                    # Isolated Linux binary modules
│   ├── caddy-server/                 # Standalone Linux Caddy Engine
│   ├── mariadb-12.3.3-linux/         # Standalone Linux MariaDB Engine
│   └── php-desktop/                  # Statically-linked Linux PHP CLI Binary
├── Servers-Win/                      # Isolated Windows engine modules (.exe/.dll)
│   ├── caddy-server/                 # Standalone Windows Caddy Engine
│   ├── mariadb-12.3.3-win/           # Standalone Windows MariaDB Engine
│   └── php-desktop/                  # Portable Windows PHP TS Binaries
├── SharedSettings/                   # Unified cross-platform config mapping
│   └── Caddyfile                     # Network parsing proxy routing rules
├── www/                              # Shared codebase repository (HTML/PHP)
│   ├── config.php                    # Global database connection adapter
│   └── index.php                     # Default workspace dashboard homepage
└── *.desktop / *.bat                 # One-click automation shortcut handlers
```

## 🐧 Linux Operation Dashboard (PikaOS, Ubuntu, Arch, etc.)

Execution commands run directly within standard user-space and require **zero** root (`sudo`) permissions:

*   **Start Database Server:** Double-click `linux.StartDB.desktop` to initialize your local MariaDB instance over network port `3306`.
*   **Start Production Web Server:** Double-click `linux.StartProductionServer.desktop`. This boots your background FastCGI process, starts Caddy, and automatically slides your default browser open to `http://127.0.0.1:8080`.
*   **Stop Production Server:** Double-click `linux.StopProductionServer.desktop` to sweep background port listeners and drop threads instantly.
*   **Stop Database Server:** Double-click `linux.StopDB.desktop` to safely park your tables and commit transactional data structures to disk layout segments.

## 🪟 Windows Operation Dashboard (x64 Architectures)

Automated tracking batch wrappers handle standard Windows shell loop configurations:

*   **Start Database Server:** Double-click `win.StartDB.bat`.
*   **Start Production Web Server:** Double-click `win.StartProductionServer.bat`.
*   **Stop Production Server:** Double-click `win.StopProductionServer.bat`.
*   **Stop Database Server:** Double-click `win.StopDB.bat`.

## 🛠️ Global Application Database Connection Adapter (`www/config.php`)

To connect any project script files directly to your live portable data layer seamlessly across both operating systems, use this clean, pre-configured PDO adapter module array format:

```php
<?php
\$host    = '127.0.0.1';
\(db      = 'your_database_name'; // Change to your project target database\)user    = 'root';               # Unified blank password user profile
\$pass    = '';
\(port    = '3306';\)charset = 'utf8mb4';

\$dsn = "mysql:host=host;dbname=db;port=port;charset=charset";
pdo = new PDO(dsn, user, pass, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
]);
```
