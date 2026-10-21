  # Portable WebDev Stick

A complete web development stack that runs from a USB drive on **Windows and Linux** — no installation, no admin rights, no changes to the host machine. Unplug from one OS, plug into the other, and keep working: same projects, same database, same configuration.

## What's inside

- **Apache 2.4** (primary web server) and **Caddy** (fallback)
- **MariaDB 12.3** — one shared data folder used by both operating systems
- **PHP 8.4** (mod\_php on Windows, PHP-FPM on Linux)
- **Node.js 24** and **Python 3.14**
- **Redis 7.2**
- **Adminer Editor 6.1.2** — database editing in the browser, behind a sign-in you choose yourself (see First run)

Everything binds to `127.0.0.1` only. Nothing is exposed to the network.

| Service | Port |
| --- | --- |
| Web (Apache/Caddy) | 8080 |
| Node app | 3000 |
| MariaDB | 3306 |
| PHP-FPM (Linux) | 9000 |
| Redis | 6379 |

## First run — once per stick

Before the first start, run the setup script once. It takes about a minute.

- **Windows:** double-click `1stRun.Setup.bat`
- **Linux:** double-click `1stRun.Setup.desktop`

Setup asks you to choose three things: a password for the MariaDB **root** account (the master key — it is never stored on the stick, so keep it somewhere safe), a password for the **webdev** account your projects will connect with, and a username and password for the database editor web page. It then sets the accounts, verifies that the passwords are really enforced, and writes the webdev credentials to `SharedSettings/db-credentials.php` so your projects — and the editor — can connect. **Treat that file as a secret: never share it and never commit it to git.**

When setup succeeds, the setup entry points rename themselves to `z.SetupAlreadyRan.*` and sink to the bottom of the folder listing — the `1` that put them at the top has done its job. Running setup again (from either OS) notices it has already run and politely does nothing; delete `SharedFolders/data/.setup-done` if you ever need to run it again, for example to change the passwords.

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

Open **http://127.0.0.1:8080/AdminerEditor/** and sign in with the editor username and password you chose during setup. The landing page is Adminer Editor — a safe, data-only view that opens the database you picked at setup (a demo database named `TestTable` by default). The full Adminer (SQL console, structure, users) is one address away at `…/AdminerEditor/adminer-6.1.2.php` — it asks for real database credentials, so use the root or webdev account there.

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

Working now: the full stack on Windows and Linux with a shared database and webroot; a tested first-run setup that gives MariaDB real passwords (no passwordless mode anywhere in the kit); and browser-based database editing behind your own sign-in.

In progress: unified one-click **Start launchers** that bring the whole stack up in the right order — one per combination (Windows/Linux × Apache/Caddy) — with a **port coordinator** that checks every port before starting anything and moves to a fallback port when one is already taken, instead of failing. DBeaver Portable is still bundled but is being replaced by Adminer and will be removed.

Planned: SQLite support for lightweight projects that don't need a server database; a bundled Java runtime further out; a bundled git server (Gitea) under evaluation; reverse proxy for Node apps; Composer in the toolbox. A pre-release cleanup pass will retire the remaining debug aids before the kit is published.
