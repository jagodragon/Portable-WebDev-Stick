# Portable WebDev Stick

A complete web development stack that runs from a USB drive on **Windows and Linux** — no installation, no admin rights, no changes to the host machine. Unplug from one OS, plug into the other, and keep working: same projects, same database, same configuration.

## What's inside

- **Apache 2.4**
- **MariaDB 12.3** — one shared data folder used by both operating systems
- **SQLite**
- **PHP 8.5** (mod\_php on Windows, PHP-FPM on Linux)
- **Node.js 24** and **Python 3.14**
- **Redis 7.2**
- **Adminer Editor 6.1.2** — database editing in the browser, behind a sign-in you choose yourself (see First run)

Everything binds to `127.0.0.1` only. Nothing is exposed to the network.

| Service         | Port |
| --------------- | ---- |
| Web (Apache)    | 8080 |
| Node app        | 3000 |
| MariaDB         | 3306 |
| PHP-FPM (Linux) | 9000 |
| Redis           | 6379 |

## First run — once per stick

Before the first start, run the setup script once. It takes about a minute.
(Linux ONLY: Files may be copied to the stick without execute permissions
run this `chmod -R +x .` in the top level of the usb stick if your scripts refuse to launch.)

- **Windows:** double-click `1stRun.Setup.bat`
- **Linux:** double-click `1stRun.Setup.desktop`

Setup asks you to choose three things: a password for the MariaDB **root** account (the master key — it is never stored on the stick, so keep it somewhere safe), a password for the **webdev** account your projects will connect with, and a username and password for the database editor web page. It then sets the accounts, verifies that the passwords are really enforced, and writes the webdev credentials to `SharedSettings/db-credentials.php` so your projects — and the editor — can connect. **Treat that file as a secret: never share it and never commit it to git.**

When setup succeeds, the setup entry points rename themselves to `z.SetupAlreadyRan.*` and sink to the bottom of the folder listing — the `1` that put them at the top has done its job. Running setup again (from either OS) notices it has already run and politely does nothing; delete `SharedFolders/data/.setup-done` if you ever need to run it again, for example to change the passwords.

## Quick start

Start MariaDB first, then a web server. The launcher names are the instructions: **work in number order** — the database (1) starts before the web server (2), and stops after it.

|                           | Windows                                 | Linux                                     |
| ------------------------- | --------------------------------------- | ----------------------------------------- |
| Start database            | `StartupWindows.1_MariaDB.bat`          | `StartupLinux.1_MariaDB.desktop`          |
| Start web server — Apache | `StartupWindows.2_WebServer_Apache.bat` | `StartupLinux.2_WebServer_Apache.desktop` |
| Stop web server — Apache  | `StopWindows.1_WebServer_Apache.bat`    | `StopLinux.1_WebServer_Apache.desktop`    |
| Stop database             | `StopWindows.2_MariaDB.bat`             | `StopLinux.2_MariaDB.desktop`             |

Then open **http://127.0.0.1:8080**.

**Always stop the servers before ejecting the drive**, and never copy the folders while the database is running.

## Port management

Every server binds to `127.0.0.1` on a default port:

| Service         | Default port |
| --------------- | ------------ |
| Web (Apache)    | 8080         |
| MariaDB         | 3306         |
| Redis           | 6379         |
| PHP-FPM (Linux) | 9000         |
| Node app        | 3000         |

The defaults live in `SharedSettings/Ports.Default.conf`, and every launcher takes its ports from there. If a default port is already in use on the machine you're plugged into, run **`a.Ports.FixConflicts`** (Windows: `a.Ports.FixConflicts.bat`; Linux: double-click `a.Ports.FixConflicts.desktop`): it tests each default, and for any port that is taken it scans the fallback range declared in the same file's `[DefaultRanges]` section and records the first free port in `SharedSettings/Ports.InUse.conf`. From then on, the launchers use the InUse ports automatically — start the stack the normal way.

To go back to the defaults, run **`a.Ports.UseDefaults`** (Windows: `a.Ports.UseDefaults.bat`; Linux: double-click `a.Ports.UseDefaults.desktop`): it deletes `Ports.InUse.conf` and every launcher returns to `Ports.Default.conf`. (If the stack is running, restart it so the change takes effect.)

If you know your machines, you can edit `Ports.Default.conf` directly: `[DefaultPorts]` sets the ports themselves, and `[DefaultRanges]` sets where the conflict fixer is allowed to hunt. Nothing ever writes to that file but you.

## When settings get tangled

Edited a file in `SharedSettings/` and now the stack won't start, or the ports are in a knot you don't want to untie by hand? Run the repair tool — **Windows:** double-click `x.RepairStick.bat`; **Linux:** double-click `x.RepairStick.desktop`. Stop the servers first: the tool checks, and refuses to run while anything from the stick is still running.

The repair puts every file in `SharedSettings/` back the way the kit shipped, using factory copies built into the stick. It also deletes the credentials file and the port choices recorded during setup, empties the runtime folders, and renames the setup entry points back to `1stRun.Setup.*` — so the finishing step is to run setup once more and choose your passwords again. Until you do, your current passwords keep working.

**Your work is not touched.** Every database and its contents, everything in `www/`, and the program files themselves survive a repair exactly as they were: this tool fixes *settings* — not your data, and not damaged program files. It prints everything it is about to do before it does it, and only proceeds when you type `REPAIR`.

## Working with the database

Open **http://127.0.0.1:8080/AdminerEditor/** and sign in with the editor username and password you chose during setup. The landing page is Adminer Editor — a safe, data-only view that opens the database you picked at setup (a demo database named `TestTable` by default). The full Adminer (SQL console, structure, users) is one address away at `…/AdminerEditor/adminer-6.1.2.php` — it asks for real database credentials, so use the root or webdev account there, and set its **Server** field to `127.0.0.1`: the form's `localhost` default connects through a socket file this stack doesn't use, and the login fails with "No such file or directory".

## Prefer a desktop database editor?

The kit ships the browser editor only, on purpose — it keeps the stick light and free of other projects' licensing terms. If you want a full desktop application, these are free and good; install one on your own machine (or in a folder of your own on the stick):

- **HeidiSQL** (Windows) — the classic MariaDB/MySQL desktop client, with a portable version.
- **Beekeeper Studio** (Windows, Linux) — a modern editor; the community edition is free.
- **DBeaver Community** (Windows, Linux) — the full-featured option, if you want an IDE-style tool.

## SQLite for lightweight projects

Not every project needs a database server. PHP in this kit has SQLite switched on — the PDO driver and the SQLite3 extension, on Windows and Linux alike — so a project can use a real SQL database that lives in a **single file**: no server to start, no accounts, no passwords.

There is nothing to install and no program to open. Your own PHP code talks to SQLite directly, so using it means adding a few lines to a page in your project and visiting that page in the browser. Try it:

1. Create a text file named `sqlite-test.php` in your project folder inside `www`.
2. Paste this into it and save:

   ```php
   <?php
   $db = new PDO('sqlite:' . __DIR__ . '/app.db');
   $db->exec('CREATE TABLE IF NOT EXISTS notes (id INTEGER PRIMARY KEY, text TEXT)');
   $db->exec("INSERT INTO notes (text) VALUES ('Hello from SQLite')");
   foreach ($db->query('SELECT text FROM notes') as $row) {
       echo $row['text'] . '<br>';
   }
   ```

3. Start the web server — the database server can stay off, SQLite doesn't need it — and open **http://127.0.0.1:8080/your-project/sqlite-test.php** in your browser, with your real folder name in place of `your-project`.

The browser shows "Hello from SQLite", and a new file named `app.db` has appeared in the same folder as the script: that file *is* the database. The first line opens it, creating it if it isn't there; `__DIR__` simply means "the folder this script is in", so the database always lands next to the script. Visit the page a second time and the message appears twice — the note from the first visit was still stored in the file. When your own code opens its database, it uses that same first line, pointed at its own file name.

Keep the `.db` file anywhere in the shared folders and the same file opens on both operating systems. One caution: a `.db` file inside `www` can be downloaded by anyone who can reach the site, so if it holds anything you'd mind sharing, keep it outside `www` — a folder of your own at the top level of the stick works well — and point the path at it there.

SQLite allows one writer at a time, which is exactly right for developing and for small personal tools. When a project outgrows that — several people writing at once, or a site you mean to publish — that's the sign to graduate it to MariaDB, which is what the sections above are for. The full Adminer can open a SQLite file too: on its login form, choose **SQLite3** as the system and enter the file's path.

## Folder map

```text
www/             your projects — shared by both OSes
mariadb-data/    the database — shared, raw, do not hand-edit
SharedSettings/  one set of configs both OSes read
SharedFolders/   shared runtime space (temp files, pid files, Redis data)
OS_Windows/      Windows binaries, scripts, apps
OS_Linux/        Linux binaries, scripts, apps
1stRun.Setup.*   first-run setup (this folder)
Startup*/Stop*   the sequenced launchers (this folder)
x.RepairStick.*  the repair tool (this folder; see When settings get tangled)
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

Working now: the full stack on Windows and Linux with a shared database and webroot; a tested first-run setup that gives MariaDB real passwords (no passwordless mode anywhere in the kit); browser-based database editing behind your own sign-in; sequenced Startup/Stop launchers whose filenames teach the start and stop order; SQLite switched on in PHP for lightweight projects (see SQLite for lightweight projects); and port management with declared defaults, an automatic conflict fixer, and a one-click return to defaults (see Port management); and a repair tool that restores factory settings and setup state from the stick itself while leaving databases and projects untouched (see When settings get tangled). DBeaver Portable has been removed — the browser editor is the kit's database tool (desktop suggestions above).

In progress: one-click **stack launchers** driven by the port tools — one per OS — that settle the ports and start the whole stack in dependency order.

Planned: optional install scripts for the recommended desktop editors above; a bundled Java runtime further out; a bundled git server (Gitea) under evaluation; reverse proxy for Node apps; Composer in the toolbox. A pre-release cleanup pass will retire the remaining debug aids before the kit is published.

## Credits

This kit is assembled from free and open-source projects, each used under its own license — the license text ships with each component, as distributed upstream. Thank you to the people who build and maintain them:

- **Apache HTTP Server** — The Apache Software Foundation — Apache License 2.0
- **MariaDB** — MariaDB Foundation — GNU General Public License v2
- **PHP** — The PHP Group — PHP License 3.01
- **SQLite** — D. Richard Hipp and contributors — public domain
- **Node.js** — OpenJS Foundation and contributors — MIT License
- **Python** — Python Software Foundation — PSF License
- **Redis** — Redis Ltd. and contributors — BSD 3-Clause License
- **Adminer and Adminer Editor** — Jakub Vrána — Apache License 2.0 or GNU GPL v2
- **curl / libcurl** — Daniel Stenberg and contributors — MIT/X License

The Linux PHP binaries were built from the official PHP sources with **static-php-cli** (MIT License).
