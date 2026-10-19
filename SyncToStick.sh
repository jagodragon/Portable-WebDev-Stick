#!/usr/bin/env bash
# ============================================================================
#  Portable WebDev Stick - Sync To Stick (Linux)
#  File: SyncToStick.sh     Lives at: the root of your LOCAL REPO CLONE.
#  Run:   bash SyncToStick.sh              (the stick is found automatically)
#         bash SyncToStick.sh /path/to/stick   (or name it yourself)
#
#  One-way refresh: LOCAL CLONE  ->  STICK. It gives you a current working
#  copy on the stick for testing, without the repo ever being touched.
#  (Stick -> repo stays a deliberate, human-reviewed step in SourceGit.)
#
#  WHAT IT COPIES
#    - the root launchers and README            (update only)
#    - the root setup scripts, either name      (update only)
#    - OS_Linux/Scripts and OS_Windows/Scripts (MIRRORED - the repo owns
#      these two folders, so files removed there are removed on the stick)
#    - SharedSettings/setup.sql, setup-generate.php   (named files only)
#    - www/index.php, www/config.php, www/status.php  (named files only)
#    - www/AdminerEditor                        (MIRRORED)
#
#  WHAT IT NEVER TOUCHES
#    - mariadb-data/                  (your databases)
#    - SharedFolders/                 (pids, logs, the .setup-done marker)
#    - SharedSettings/db-credentials.php  (generated on the stick, secret)
#    - SharedSettings/*.cnf, php.ini  (server config - during testing the
#      stick is usually AHEAD of the repo on these; syncing them would
#      fight the tester. Commit them through SourceGit instead.)
#    - anything else under www/       (your own projects)
#
#  SAFETY
#    - The target must look like a WebDev stick (SharedSettings/ and
#      mariadb-data/ side by side), or nothing is written anywhere.
#    - It refuses to run while the stack is up (MariaDB's pid file).
#
#  NEEDS: rsync. If rsync is missing this script tries to install it with
#  apt first (you will be asked for your password, in the open - this script
#  never raises its own privileges silently), and only gives up if that
#  fails too:  "try to fix yourself if you want to fail."
# ============================================================================

set -u

SOURCE_DIR="$(cd "$(dirname "$0")" && pwd)"

say()  { printf '%s\n' "$*"; }
fail() { printf '\n*** Sync stopped: %s\n' "$*" >&2; exit 1; }

# ---- Am I standing in the clone? --------------------------------------------
[ -d "$SOURCE_DIR/SharedSettings" ] && [ -d "$SOURCE_DIR/www" ] && [ -d "$SOURCE_DIR/OS_Linux" ] \
    || fail "run me from the root of your Portable-WebDev-Stick clone (I could not find SharedSettings/, www/ and OS_Linux/ beside me)."

# ---- Dependency: rsync (try to fix yourself if you want to fail) ------------
if ! command -v rsync >/dev/null 2>&1; then
    say "rsync is not installed - it is the one tool this script needs."
    say "Trying to install it with apt now. You will be asked for your password, in the open:"
    sudo apt-get update && sudo apt-get install -y rsync
    command -v rsync >/dev/null 2>&1 \
        || fail "rsync is still missing. Install it manually with:  sudo apt install rsync"
    say "rsync installed."
fi

# ---- Find the stick ----------------------------------------------------------
STICK="${1:-}"
if [ -z "$STICK" ]; then
    for base in "/run/media/$USER" "/media/$USER"; do
        [ -d "$base" ] || continue
        for cand in "$base"/*/; do
            if [ -d "${cand}SharedSettings" ] && [ -d "${cand}mariadb-data" ] && [ -d "${cand}OS_Windows" ]; then
                STICK="${cand%/}"
                break 2
            fi
        done
    done
fi
[ -n "$STICK" ] || fail "could not find the stick. Plug it in, or pass its path:  bash SyncToStick.sh /path/to/stick"
[ -d "$STICK/SharedSettings" ] && [ -d "$STICK/mariadb-data" ] \
    || fail "$STICK does not look like a WebDev stick (SharedSettings/ and mariadb-data/ not found side by side). Refusing to write to it."

# ---- The stack must be stopped ----------------------------------------------
PID_FILE="$STICK/SharedFolders/data/mariadb.pid"
if [ -f "$PID_FILE" ]; then
    P="$(cat "$PID_FILE" 2>/dev/null || true)"
    if [ -n "$P" ] && kill -0 "$P" 2>/dev/null; then
        fail "the stack is running from the stick (MariaDB pid $P). Stop it first - syncing under a running server is how files get half-copied."
    fi
fi

say "Syncing clone -> stick"
say "  from: $SOURCE_DIR"
say "  to:   $STICK"
say ""

# --modify-window=2: the stick's filesystem keeps timestamps only to a
# 2-second grain; without this, rsync would re-copy unchanged files forever.
RSYNC_OPTS="-rlt --modify-window=2"

# 1) Root launchers, README, setup scripts (either name) - update only.
for f in "$SOURCE_DIR"/win.*.bat "$SOURCE_DIR"/linux.*.desktop "$SOURCE_DIR"/README.md \
         "$SOURCE_DIR"/1stRun.Setup.sh "$SOURCE_DIR"/1stRun.Setup.bat \
         "$SOURCE_DIR"/z.SetupAlreadyRan.sh "$SOURCE_DIR"/z.SetupAlreadyRan.bat; do
    [ -e "$f" ] && rsync $RSYNC_OPTS "$f" "$STICK/"
done

# 2) Scripts folders - mirrored (the repo owns these two folders outright).
rsync $RSYNC_OPTS --delete "$SOURCE_DIR/OS_Linux/Scripts/"  "$STICK/OS_Linux/Scripts/"
rsync $RSYNC_OPTS --delete "$SOURCE_DIR/OS_Windows/Scripts/" "$STICK/OS_Windows/Scripts/"

# 3) Setup machinery in SharedSettings - named files only (see header).
for f in setup.sql setup-generate.php; do
    [ -e "$SOURCE_DIR/SharedSettings/$f" ] && rsync $RSYNC_OPTS "$SOURCE_DIR/SharedSettings/$f" "$STICK/SharedSettings/"
done

# 4) www system files - named files only; your projects are never touched.
for f in index.php config.php status.php; do
    [ -e "$SOURCE_DIR/www/$f" ] && rsync $RSYNC_OPTS "$SOURCE_DIR/www/$f" "$STICK/www/"
done

# 5) The database editor folder - mirrored.
[ -d "$SOURCE_DIR/www/AdminerEditor" ] && rsync $RSYNC_OPTS --delete "$SOURCE_DIR/www/AdminerEditor/" "$STICK/www/AdminerEditor/"

say ""
say "Sync complete. The stick is a current working copy; the repo was not touched."
