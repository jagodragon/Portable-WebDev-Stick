# Portable WebDev Stick

A complete web development stack that runs from a USB drive on **Windows and Linux** — no installation, no admin rights, no changes to the host machine. Unplug from one OS, plug into the other, and keep working: same projects, same database, same configuration.

## What's inside

- **Apache 2.4** (primary web server) and **Caddy** (fallback)
- **MariaDB 12.3** — one shared data folder used by both operating systems
- **PHP 8.4** (mod\_php on Windows, PHP-FPM on Linux)
- **Node.js 24** and **Python 3.14**
- **Redis 7.2**
- **Adminer** — database editor in the browser (see below)

Everything binds to `127.0.0.1` only. Nothing is exposed to the network.

| Service | Port |
| --- | --- |
| Web (Apache/Caddy) | 8080 |
| Node app | 3000 |
| MariaDB | 3306 |
| PHP-FPM (Linux) | 9000 |
| Redis | 6379 |

## Quick start

Start MariaDB first, then a web server:

|  | Windows | Linux |
| --- | --- | --- |
| Start database | `win.StartMariaDB.bat` | `linux.StartMariaDB.desktop` |
| Start web server | `win.StartPrimaryWebServer_Apache.bat` | `linux.StartPrimaryWebServer_Apache.desktop` |
| Stop web server | `win.StopPrimaryWebServer_Apache.bat` | `linux.StopPrimaryWebServer_Apache.desktop` |
| Stop database | `win.StopMariaDB.bat` | `linux.StopMariaDB.desktop` |

(Caddy variants of the web-server launchers are included as a fallback.) Then open **http://127.0.0.1:8080**.

**Always stop the servers before ejecting the drive**, and never copy the folders while the database is running.

## Working with the database

Open **http://127.0.0.1:8080/AdminerEditor/**. The landing page is Adminer Editor — a safe, data-only view. The full Adminer (SQL console, structure, users) is one address away at `…/AdminerEditor/adminer-6.1.2.php`.

> **Development status:** MariaDB currently starts without password enforcement so the kit works out of the box. A first-run setup script that sets real passwords is in progress — until it lands, treat this as a local-only development tool (it is; see the loopback note above).

## Folder map

```text
www/             your projects — shared by both OSes
mariadb-data/    the database — shared, raw, do not hand-edit
SharedSettings/  one set of configs both OSes read
SharedFolders/   shared runtime space (temp files, pid files, Redis data)
OS_Windows/      Windows binaries, scripts, apps
OS_Linux/        Linux binaries, scripts, apps
win.* / linux.*  the launchers (this folder)
```

## Filesystem

- **exFAT — recommended.** Works on both OSes.
- **FAT32/vfat — do not use.** They lose Linux permissions and symlinks; the Linux side will not run.
- **ext4/Btrfs — Linux only.** Windows can't read the stick.
- **NTFS — not recommended.** Unreliable under Linux depending on distro.

Linux mount tip, if your distro mounts the stick without execute permission:

```bash
sudo mount -t exfat /dev/sdX1 /media/webdev -o uid=1000,gid=1000,fmask=000,dmask=000
```

## Switching machines

1. Stop the web server, then the database.
2. Eject the drive.
3. Plug it into the other machine and start the same two launchers.
4. Continue working. No export or import — the files never moved.

## Status and roadmap

Working now: the full stack on Windows and Linux, shared database and webroot, browser-based database editing.

In progress: the first-run setup script (real database passwords); a start/stop coordinator that reassigns ports automatically when one is taken. DBeaver Portable is still bundled but is being replaced by Adminer and will be removed.

Planned: reverse proxy for Node apps, Composer in the toolbox.
