# Portable WebDev Stick — To Do

Updated 2026-10-21

## Features to add

- [ ] SQLite support (enable PHP extensions; dev-only, MariaDB stays the main database)
- [ ] Composer in the toolbox
- [ ] Node reverse proxy (serve Node apps through the web server)
- [ ] Bundled Java runtime (H2 jar rides along in the Java folder)
- [ ] Install scripts for the recommended desktop editors — HeidiSQL, Beekeeper Studio, DBeaver Community (scripts only, never bundled)
- [ ] Local mail catcher (needs a port slot)
- [ ] Status landing page (what's running, actual ports, links to projects and the DB editor)
- [ ] Xdebug toggle for PHP (off by default)
- [ ] Backup tool (dated zip: www + MariaDB dump + SharedSettings)
- [ ] Versions report (print the bundled versions in a table)
- [ ] Improper-shutdown recovery script (find servers left running with no pid file — e.g. after Ctrl+C in a launcher window, where answering N to "Terminate batch job?" resumes the script and the pid file gets deleted under a live server; clear stale pid/lock files only when the process is confirmed dead; gracefully stop or re-adopt orphans; verify MariaDB comes back clean after an unclean stop)

## Finishing tasks

- [ ] Port system boot test on real hardware (Windows .bat files; PHP-FPM port expansion; Apache port expansion)
- [ ] Delete the stale root a.Ports.*.sh copies from the clone (v1→v2 cleanup)
- [ ] README next pass: remove the one-click launchers "in progress" line
- [ ] 0.START-HERE.txt pointer file (discussed, not locked — decide)
- [ ] Pre-release cleanup pass (retire the remaining debug aids)
- [ ] Tip jar (kit stays free)
- [ ] Share plan when the stick is declared done (where, in what order, with what text)

## Last: the Go installer (replaces the 1stRun scripts entirely)

- [ ] Discuss and settle the UI design
- [ ] Single static Go binary per OS, independent of the core packages
- [ ] Checkbox feature list with a live required-USB-size total
- [ ] Scrolling feature-list pane, with the total and Build controls fixed in view
- [ ] Windows support and Linux support as selectable features
- [ ] External features manifest (add a feature = add a folder + a manifest entry, no rebuild)
- [ ] Detect what's already installed and pre-check those boxes
- [ ] Add/remove features on reruns (never touch user projects or data)
- [ ] Assembly + settings updates + the full setup census (passwords, credentials file, Ports.InUse stamp)
- [ ] Source stored in a new folder at the distro root
- [ ] Retire the 1stRun.Setup scripts once the installer lands

## Suggestions (not adopted)

- [ ] "New project" scaffolder (starter folder in www, optional database)
- [ ] Gitea git server (still under evaluation — decide, then give it a port slot)

## Closed / ruled out

- [x] One-click stack launchers — closed by Robert (don't force people to wait for things they don't need)
- [x] PostgreSQL — ruled out (doesn't fit the core ideals)
- [x] phpMyAdmin — ruled out (version-incompatible)
- [x] Bundled third-party software (incl. DBeaver) — ruled out (recommendations and install scripts only)
