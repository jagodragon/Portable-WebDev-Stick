# Portable WebDev Stick — To Do

Updated 2026-10-25

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
- [ ] Improper-shutdown recovery script — the engine behind the front-door app's launch check and recovery button (see "Bad-shutdown detection and recovery" below); also runnable directly without the app (find servers left running with no pid file — e.g. after Ctrl+C in a launcher window, where answering N to "Terminate batch job?" resumes the script and the pid file gets deleted under a live server; clear stale pid/lock files only when the process is confirmed dead; gracefully stop or re-adopt orphans; verify MariaDB comes back clean after an unclean stop)

## Finishing tasks

- [x] Remove Caddy (DECIDED 2026-10-24 — Apache is the only web server; Caddy was never-run insurance from the unproven-Apache period). DONE 2026-10-25, verified in the pushed tree: root 2b/1b Caddy launchers, OS_Windows/Scripts/StopCaddy.bat, OS_Linux/Scripts/StopCaddy.sh, both caddy-server folders, and SharedSettings/Caddyfile all deleted; StopPrimaryWebServer_Apache.bat edited to drop its StopCaddy call. Only curl's bundled test files still mention Caddy, left alone by design.
- [ ] Port system boot test on real hardware (stack boot VERIFIED green on Windows 2026-10-24 and on Linux 2026-10-25, Apache port expansion verified; still owed: PHP-FPM moved-port expansion, Linux side; the FixConflicts occupied-8080 conflict branch on Windows)
- [x] Delete the stale root a.Ports.*.sh copies from the clone (v1→v2 cleanup) — already gone, verified 2026-10-25
- [x] README next pass: remove the one-click launchers "in progress" line — NOT doing: Robert reopened the one-click path on 2026-10-25, so the line stays. The README instead gains either/or instructions when the front-door app ships (see below)
- [ ] 0.START-HERE.txt pointer file (discussed, not locked — decide)
- [ ] Pre-release cleanup pass (retire the remaining debug aids)
- [ ] Tip jar (kit stays free)
- [ ] Share plan when the stick is declared done (where, in what order, with what text)

## Last: the front-door app (Go) — installer, setup, and control panel in one

One app is the kit's front door and the only point of access for users who want that: it runs first-run setup, installs and manages features, and controls the servers. It replaces the 1stRun scripts entirely when it lands; until then the scripts remain the live path. The sequenced Startup*/Stop* launchers and the a.Ports.* tools stay available underneath as the direct path.

- [x] Discuss and settle the UI design (settled 2026-10-25 — one front-door app: setup gate, control panel, ports side panel, feature manager; requirements below)

### The app itself

- [ ] Single static Go binary per OS, stdlib only, independent of the core packages; embedded web UI bound to 127.0.0.1 only — the app serves its own page, so nothing in the stack needs to be running first
- [ ] Source stored in a new folder at the distro root
- [ ] Runs the same completeness census as the scripts (every binary and config the stack needs) and fails clearly, naming what is missing; never elevates privileges silently

### Setup gate (first run)

- [ ] On launch, detect whether first-run setup has completed (the SharedFolders/data/.setup-done sentinel)
- [ ] Setup NOT run → the app opens in the setup flow; no control panel is offered for an un-setup stack
- [ ] Setup flow collects: MariaDB root password, webdev account password, database-editor username and password (masked fields in the browser form)
- [ ] Setup does the full census and credential work the 1stRun scripts do today — accounts created, passwords verified as actually enforced, db-credentials.php written, Ports.InUse stamped — driving the existing generator machinery rather than a second implementation
- [ ] A partial or interrupted setup is detected and resumed or repaired, never blindly restarted (the probes and restore-on-abort behavior of the current scripts carry over)
- [ ] Setup completed → the app opens straight at the control panel
- [ ] A "re-run setup / change passwords" action inside the app, so panel-only users never have to delete the sentinel file by hand

### Control panel (the one-click launcher)

- [ ] Live status for each service (running / stopped)
- [ ] Start All: settle the ports first, then start MariaDB, wait until it answers, then start the web server
- [ ] Stop All in reverse order
- [ ] Individual start/stop per service alongside Start All — both granularities on one surface
- [ ] Every action dispatches to the same worker scripts and logic the sequenced launchers use — the panel composes the existing engine, never a duplicate of it
- [ ] "Open the site" and "Open the database editor" links once the stack is up

### Bad-shutdown detection and recovery

- [ ] At launch, before the control panel is offered, scan for evidence of a bad shutdown: stale pid/lock files, live stack processes with no pid file, and stack ports held by processes the pid files don't account for
- [ ] If anything is found, notify the user in plain language what was found and that the app is fixing it — the repair is automatic, but never silent
- [ ] Repairs are the safe set only: stale pid/lock files are removed only when the process is confirmed dead; orphaned live servers are stopped gracefully or re-adopted (shown as running in the panel), never force-killed as a first resort; MariaDB's own crash recovery is allowed to run and its clean return is verified
- [ ] Anything that cannot be fixed safely is escalated to the user with the choice, not forced
- [ ] A "Recover from bad shutdown" button on the control panel runs the same detection-and-repair routine on demand
- [ ] The routine is the standalone recovery worker (the last item in Features to add) — the app invokes it rather than reimplementing it: one engine, two triggers (launch check and button), and it stays runnable directly without the app

### Ports side panel

- [ ] A persistent side panel listing each service's resolved port (Web, Node, MariaDB, PHP-FPM, Redis) — the resolved value from the env → Ports.InUse → Ports.Default chain, not one file's contents
- [ ] A port that differs from its default shows as moved (e.g. "8081 (default 8080)")
- [ ] Fix Conflicts action (the a.Ports.FixConflicts logic) and Use Defaults action (the a.Ports.UseDefaults logic) in the panel; the list refreshes after either runs
- [ ] If services are running and the resolved ports no longer match the running ones, the panel marks the change "takes effect on restart"

### Feature installer / manager

- [ ] Checkbox feature list with a live required-USB-size total
- [ ] Scrolling feature-list pane, with the total and Build controls fixed in view
- [ ] Windows support and Linux support as selectable features
- [ ] External features manifest (add a feature = add a folder + a manifest entry, no rebuild)
- [ ] Detect what's already installed and pre-check those boxes
- [ ] Add/remove features on reruns (never touch user projects or data)
- [ ] Assembly + settings updates for feature installs and removals (first-run setup itself lives in the Setup gate above)
- [ ] Retire the 1stRun.Setup scripts once the app lands

### Instructions

- [ ] README presents the app and the sequenced scripts as alternatives (app = the easy path; scripts = the manual/teaching path), and the roadmap's "In progress" line is updated to match (one launcher per OS — Apache is the only web server)

## Suggestions (not adopted)

- [ ] "New project" scaffolder (starter folder in www, optional database)
- [ ] Gitea git server (still under evaluation — decide, then give it a port slot)

## Closed / ruled out

- [x] ~~One-click stack launchers — closed by Robert (don't force people to wait for things they don't need)~~ REOPENED 2026-10-25: Robert wants the one-click path after all, as the control panel of the front-door app above. Both paths ship — the app for users who want one door, the sequenced scripts underneath for everyone else
- [x] PostgreSQL — ruled out (doesn't fit the core ideals)
- [x] phpMyAdmin — ruled out (version-incompatible)
- [x] Bundled third-party software (incl. DBeaver) — ruled out (recommendations and install scripts only)
