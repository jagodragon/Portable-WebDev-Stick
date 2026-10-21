#!/usr/bin/env bash
# ============================================================================
#  Portable WebDev Stick - First-Run Setup (Linux)
#  File: 1stRun.Setup.sh        Installs to: OS_Linux/Scripts on the stick.
#  Run:   double-click 1stRun.Setup.desktop at the stick root (it opens a
#         terminal and runs this script), or from any terminal:
#         bash OS_Linux/Scripts/1stRun.Setup.sh
#         Do NOT double-click THIS file - it needs a terminal for its
#         password prompts, and will say so and stop if it can't get one.
#
#  What this does, once per stick:
#    1. Asks you for a MariaDB root password, a 'webdev' account password,
#       and a username/password for the database editor web page.
#       (Typed passwords are never echoed and never written anywhere except
#       where step 3 puts them.)
#    2. Starts MariaDB once in a one-time setup mode, creates the accounts
#       by running SharedSettings/setup.sql, then stops it again.
#    3. Writes SharedSettings/db-credentials.php - the file the database
#       editor and your projects read to connect. The editor's password is
#       stored there only as a one-way hash. Also stamps
#       SharedSettings/Ports.InUse.conf from Ports.Default.conf, unless a
#       port decision already exists there.
#    4. Starts MariaDB normally and PROVES the new state: root without a
#       password must now FAIL, webdev with its password must succeed.
#    5. Leaves a marker file (SharedFolders/data/.setup-done) so this setup
#       never runs twice by accident.
#    6. Renames the setup entry points at the stick root (the desktop
#       launcher and the Windows script) to z.SetupAlreadyRan.*, so they
#       sink to the bottom of the folder listing - the "1" that put them
#       at the top has done its job. This worker script keeps its own name:
#       the renamed launcher points at it, and a stable path is what lets
#       that launcher work again later (it finds the marker and reports
#       setup as already done).
#
#  Nothing in this script raises its own privileges. If a step ever needed
#  administrator rights it would say so and stop - that day is not today:
#  everything here runs as you, from files already on the stick.
# ============================================================================

set -u

# ---- Where am I? This worker lives in OS_Linux/Scripts; the stick root is
# two levels up. Everything below is relative to the root. ------------------
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
USB_ROOT="$(cd "$SCRIPT_DIR/../.." && pwd)"
cd "$USB_ROOT" || exit 1
SETTINGS="$USB_ROOT/SharedSettings"
DATA_DIR="$USB_ROOT/mariadb-data"
RUNTIME="$USB_ROOT/SharedFolders/data"
WORKTMP="$USB_ROOT/SharedFolders/tmp"
SOCKET="/tmp/portable-mariadb.sock"
PID_FILE="$RUNTIME/mariadb.pid"
MARKER="$RUNTIME/.setup-done"
CREDS_FILE="$SETTINGS/db-credentials.php"
PORTS_DEFAULT="$SETTINGS/Ports.Default.conf"
PORTS_INUSE="$SETTINGS/Ports.InUse.conf"
PORTS_INIT_MSG=""
SQL_TEMPLATE="$SETTINGS/setup.sql"
GENERATOR="$SETTINGS/setup-generate.php"
MARIADBD="$USB_ROOT/OS_Linux/Software/mariadb/bin/mariadbd"
MARIADB="$USB_ROOT/OS_Linux/Software/mariadb/bin/mariadb"
MARIADB_ADMIN="$USB_ROOT/OS_Linux/Software/mariadb/bin/mariadb-admin"
# The generator needs a PHP that can RUN A SCRIPT FILE (a CLI build). This
# stick also carries FPM-only PHP binaries (php-bin, php-fpm) that cannot -
# the CLI lives in the compiled tree's bin folder.
PHP_BIN="$USB_ROOT/OS_Linux/Software/php-desktop/bin/php"

SERVER_STARTED=0
SETUP_COMPLETED=0
TMP_SQL="$WORKTMP/.setup-sql.tmp"

say()  { printf '%s\n' "$*"; }

pause_at_end() {
    # A window opened by the desktop launcher closes the moment this script
    # exits, taking its final message with it - so hold it open for reading.
    # Only when a terminal is really attached: piped, scripted, or otherwise
    # unattended runs must never sit waiting for a keypress that can't come.
    if [ -t 0 ]; then
        printf '\nPress Enter to close...'
        read -r _ || true
        printf '\n'
    fi
}


ports_conf_get() {  # $1 = file, $2 = section, $3 = key; prints value or nothing
    awk -v sec="[$2]" -v key="$3" '
        { sub(/\r$/, "") }
        /^\[/ { insec = ($0 == sec); next }
        insec && $0 ~ "^[ \t]*" key "[ \t]*=" {
            line = $0
            sub(/^[^=]*=/, "", line)
            sub(/[;#].*$/, "", line)
            gsub(/[ \t]/, "", line)
            print line; exit
        }' "$1" 2>/dev/null
}

init_ports_file() {
    # Stamp the active ports file from the declared defaults - unless a
    # port decision already lives there (a.Ports.FixConflicts wrote it),
    # which setup must never clobber.
    if [ -f "$PORTS_INUSE" ] && grep -q '^\[InUsePorts\]' "$PORTS_INUSE"; then
        PORTS_INIT_MSG="existing Ports.InUse.conf kept - it already holds a port decision"
        return 0
    fi
    local web mariadb redis fpm node tmp
    web="$(ports_conf_get "$PORTS_DEFAULT" DefaultPorts WEB)";         [[ "$web" =~ ^[0-9]+$ ]] || web=8080
    mariadb="$(ports_conf_get "$PORTS_DEFAULT" DefaultPorts MARIADB)"; [[ "$mariadb" =~ ^[0-9]+$ ]] || mariadb=3306
    redis="$(ports_conf_get "$PORTS_DEFAULT" DefaultPorts REDIS)";     [[ "$redis" =~ ^[0-9]+$ ]] || redis=6379
    fpm="$(ports_conf_get "$PORTS_DEFAULT" DefaultPorts FPM)";         [[ "$fpm" =~ ^[0-9]+$ ]] || fpm=9000
    node="$(ports_conf_get "$PORTS_DEFAULT" DefaultPorts NODE)";       [[ "$node" =~ ^[0-9]+$ ]] || node=3000
    tmp="$PORTS_INUSE.tmp.$$"
    {
        say "# Ports.InUse.conf - created by 1stRun.Setup from Ports.Default.conf."
        say "# a.Ports.FixConflicts may rewrite it; a.Ports.UseDefaults removes it."
        say "SESSION=SETUP $(date '+%Y-%m-%d %H:%M') on $(hostname)"
        say ""
        say "[InUsePorts]"
        say "WEB=$web"
        say "MARIADB=$mariadb"
        say "REDIS=$redis"
        say "FPM=$fpm"
        say "NODE=$node"
    } > "$tmp" || fail "could not write Ports.InUse.conf."
    mv -f "$tmp" "$PORTS_INUSE" || fail "could not replace Ports.InUse.conf."
    PORTS_INIT_MSG="Ports.InUse.conf created from Ports.Default.conf"
}

fail() { printf '\n*** Setup stopped: %s\n' "$*" >&2; pause_at_end; exit 1; }

stop_server() {
    # Polite shutdown first (works in both setup mode and normal mode),
    # then, only for a server THIS script started, the recorded pid.
    "$MARIADB_ADMIN" --socket="$SOCKET" -u root shutdown >/dev/null 2>&1
    if [ -f "$PID_FILE" ]; then
        local p; p="$(cat "$PID_FILE" 2>/dev/null || true)"
        if [ -n "$p" ] && kill -0 "$p" 2>/dev/null; then
            MYSQL_PWD="${ROOT_PW:-}" "$MARIADB_ADMIN" --socket="$SOCKET" -u root shutdown >/dev/null 2>&1
            sleep 2
        fi
        if [ -n "$p" ] && kill -0 "$p" 2>/dev/null; then kill "$p" 2>/dev/null; fi
    fi
    SERVER_STARTED=0
}

cleanup() {
    # Runs on every exit, success or failure: never leave a server running
    # that this script started, and never leave the filled-in SQL behind -
    # it contains passwords.
    [ "$SERVER_STARTED" = 1 ] && stop_server
    rm -f "$TMP_SQL"
    # If setup did not complete, a credentials file from this run would
    # describe accounts that may not exist - remove it too.
    [ "$SETUP_COMPLETED" = 0 ] && rm -f "$CREDS_FILE"
    unset ROOT_PW WEBDEV_PW GATE_PW 2>/dev/null || true
}
trap cleanup EXIT

wait_ready() {
    # $1 = "setup" or "normal". This probe answers ONE question: is the
    # server up and answering connections? In setup mode the probe query
    # simply succeeds. In normal mode the server may answer with data OR
    # with an "Access denied" refusal - EITHER proves it is alive; only a
    # connection failure means "not yet". The strict password checks are
    # step 7's job, not this probe's. (Listening only for "Access denied"
    # goes deaf in exactly one state - authentication still off, a probe
    # query answered with data - which is the very state step 7(a) exists
    # to catch. Learned from a real test run, Oct 19 2026.)
    local mode="$1" i out
    for i in $(seq 1 30); do
        out="$("$MARIADB" --socket="$SOCKET" -u root -e "SELECT 1" 2>&1)"
        if printf '%s' "$out" | grep -q '^1$'; then return 0; fi
        if [ "$mode" = "normal" ] && printf '%s' "$out" | grep -qi 'Access denied'; then return 0; fi
        sleep 1
    done
    return 1
}

start_server() {
    # $1 = "setup" adds --skip-grant-tables (account surgery mode).
    # --defaults-file MUST be the first argument: MariaDB accepts it only there.
    if [ "${1:-}" = "setup" ]; then
        "$MARIADBD" --defaults-file="$SETTINGS/mariadb-configs.cnf" --skip-grant-tables \
            --datadir="$DATA_DIR" --pid-file="$PID_FILE" --socket="$SOCKET" --user=root &
    else
        "$MARIADBD" --defaults-file="$SETTINGS/mariadb-configs.cnf" \
            --datadir="$DATA_DIR" --pid-file="$PID_FILE" --socket="$SOCKET" --user=root &
    fi
    SERVER_STARTED=1
}

prompt_secret() {
    # $1 = description. Result in REPW. Loops until non-empty and matching.
    # A failed read means there is no terminal to type into (this file was
    # double-clicked instead of launched) - stop with the cause named,
    # never spin on empty input forever.
    local a b
    while true; do
        printf 'Enter %s: ' "$1"
        read -r -s a || fail "no input available. Run setup from a terminal, or by double-clicking 1stRun.Setup.desktop at the stick root - not by double-clicking this script file."
        printf '\n'
        printf 'Repeat %s: ' "$1"
        read -r -s b || fail "no input available. Run setup from a terminal, or by double-clicking 1stRun.Setup.desktop at the stick root - not by double-clicking this script file."
        printf '\n'
        [ -n "$a" ] || { say "  (must not be empty - try again)"; continue; }
        [ "$a" = "$b" ] || { say "  (the two entries did not match - try again)"; continue; }
        REPW="$a"; return 0
    done
}

# ============================================================================
say "==================================================================="
say " Portable WebDev Stick - first-run setup"
say "==================================================================="

# ---- Step 1: is everything this script needs actually here? ----------------
# Everything used below ships ON the stick, so a missing file means this
# copy of the kit is incomplete - no package manager can fix that, and this
# script will not pretend otherwise. It says exactly what is missing.
for f in "$MARIADBD" "$MARIADB" "$MARIADB_ADMIN" "$PHP_BIN" "$SQL_TEMPLATE" "$GENERATOR"; do
    [ -e "$f" ] || fail "missing file: $f - this copy of the stick is incomplete. Re-copy the kit, then run setup again."
done
# Existence is not capability: prove the PHP binary can actually execute
# code before trusting it with the generator (see the PHP_BIN note above).
"$PHP_BIN" -r 'exit(0);' >/dev/null 2>&1 \
    || fail "the PHP program at $PHP_BIN cannot run scripts (an FPM-only build cannot). Setup needs the CLI build at OS_Linux/Software/php-desktop/bin/php."
[ -d "$DATA_DIR" ] || fail "missing folder: $DATA_DIR - this does not look like a WebDev stick root."
mkdir -p "$RUNTIME" "$WORKTMP"

# ---- Step 2: once per stick -------------------------------------------------
if [ -f "$MARKER" ]; then
    say "First-run setup was already completed for this stick:"
    cat "$MARKER"
    init_ports_file
    say "$PORTS_INIT_MSG."
    say "Nothing to do. (To run setup again, delete that marker file first.)"
    SETUP_COMPLETED=1   # an earlier, completed setup owns the credentials file
    pause_at_end
    exit 0
fi

# ---- Step 3: the stack must be stopped --------------------------------------
if [ -f "$PID_FILE" ]; then
    OLD_PID="$(cat "$PID_FILE" 2>/dev/null || true)"
    if [ -n "$OLD_PID" ] && kill -0 "$OLD_PID" 2>/dev/null; then
        fail "MariaDB is running from this stick (pid $OLD_PID). Stop the stack first, then run setup again."
    fi
fi

# ---- Step 4: collect the secrets --------------------------------------------
say ""
say "You will set three things. Passwords are not echoed as you type."
say ""
prompt_secret "a password for the MariaDB ROOT account (the master key - keep it somewhere safe)"
ROOT_PW="$REPW"
prompt_secret "a password for the WEBDEV account (your projects will connect with this one)"
WEBDEV_PW="$REPW"
printf 'Username for the database editor web page [admin]: '
read -r GATE_USER; GATE_USER="${GATE_USER:-admin}"
prompt_secret "a password for the database editor web page (separate from the database passwords)"
GATE_PW="$REPW"
printf 'Database the editor should open [TestTable]: '
read -r EDITOR_DB; EDITOR_DB="${EDITOR_DB:-TestTable}"

# ---- Step 5: generate the working files -------------------------------------
# The generator (PHP, shared with the Windows script) does all password
# string handling in one audited place and writes:
#   - the one-time SQL file (deleted in step 6, and by cleanup on any exit)
#   - SharedSettings/db-credentials.php
export SETUP_ROOT_PW="$ROOT_PW" SETUP_WEBDEV_PW="$WEBDEV_PW"
export SETUP_GATE_USER="$GATE_USER" SETUP_GATE_PW="$GATE_PW"
export SETUP_EDITOR_DB="$EDITOR_DB"
export SETUP_SQL_TEMPLATE="$SQL_TEMPLATE" SETUP_TMP_SQL="$TMP_SQL"
export SETUP_CREDS_FILE="$CREDS_FILE"
"$PHP_BIN" "$GENERATOR" || fail "could not generate the setup files (see the message above)."
unset SETUP_ROOT_PW SETUP_WEBDEV_PW SETUP_GATE_PW

# ---- Step 6: start in setup mode and create the accounts --------------------
say ""
say "Starting MariaDB in one-time setup mode..."
start_server setup
wait_ready setup || fail "MariaDB did not come up in setup mode. The data folder may be damaged - setup changed nothing."
"$MARIADB" --socket="$SOCKET" -u root < "$TMP_SQL" || fail "creating the accounts failed - setup is not complete."
rm -f "$TMP_SQL"
say "Accounts created: root and webdev now have the passwords you set."
stop_server

# ---- Step 7: start normally and prove it ------------------------------------
say "Restarting MariaDB normally to verify..."
start_server
wait_ready normal || fail "MariaDB did not restart in normal mode."

# (a) Root WITHOUT a password must now fail. If it succeeds, authentication
#     is still switched off in the shared config - name the exact cause.
if MYSQL_PWD= "$MARIADB" -h 127.0.0.1 -P 3306 -u root -e "SELECT 1" >/dev/null 2>&1; then
    fail "root can still log in WITHOUT a password. The accounts are set, but authentication is still off - most likely the line 'skip-grant-tables' is still present in SharedSettings/mariadb-configs.cnf. Remove that one line, then run this setup again to finish verification."
fi
# (b) webdev over TCP (the way PHP connects) must succeed.
MYSQL_PWD="$WEBDEV_PW" "$MARIADB" -h 127.0.0.1 -P 3306 -u webdev -e "SELECT 1" >/dev/null 2>&1 \
    || fail "webdev could not log in over TCP with its new password - setup is not complete."
# (c) webdev over the socket (the 'localhost' account form) must succeed.
MYSQL_PWD="$WEBDEV_PW" "$MARIADB" --socket="$SOCKET" -u webdev -e "SELECT 1" >/dev/null 2>&1 \
    || fail "webdev could not log in over the socket with its new password - setup is not complete."
# (d) root WITH its password must succeed.
MYSQL_PWD="$ROOT_PW" "$MARIADB" -h 127.0.0.1 -P 3306 -u root -e "SELECT 1" >/dev/null 2>&1 \
    || fail "root could not log in with its new password - setup is not complete."
stop_server

# ---- Step 8: marker + summary -----------------------------------------------
init_ports_file
{
    echo "Portable WebDev Stick first-run setup completed."
    echo "Date: $(date '+%Y-%m-%d %H:%M:%S %Z')"
    echo "Setup version: 1"
} > "$MARKER"
SETUP_COMPLETED=1

say ""
say "==================================================================="
say " Setup complete."
say "==================================================================="
say " - MariaDB root password:   the master key you set (not stored on the stick)."
say " - webdev account:          password stored in SharedSettings/db-credentials.php"
say "                            so your projects can connect. Treat that file as a"
say "                            secret: never share it, never commit it to git."
say " - Database editor:         open http://127.0.0.1:8080/AdminerEditor/ after"
say "                            starting the stack, and sign in with the editor"
say "                            username '$GATE_USER' and the editor password you set."
say " - Ports:                   $PORTS_INIT_MSG."
say " - The stack now starts the normal way (MariaDB first, then a web server)."
say " - The setup entry points at the stick root (the desktop launcher and"
say "   the Windows script) now rename themselves to z.SetupAlreadyRan.*"
say "   and sink to the bottom of the folder. Setup is done."
say ""
say "One polite request before you go:"
say ""
say "######    ########    ####    ######      ########  ##    ##  ########"
say "##    ##  ##        ##    ##  ##    ##        ##      ##    ##  ##"
say "######    ######    ########  ##    ##        ##      ########  ######"
say "##  ##    ##        ##    ##  ##    ##        ##      ##    ##  ##"
say "##    ##  ########  ##    ##  ######        ##      ##    ##  ########"
say ""
say "     ######    ########    ####    ######    ##      ##  ########"
say "     ##    ##  ##        ##    ##  ##    ##  ####  ####  ##"
say "     ######    ######    ########  ##    ##  ##  ##  ##  ######"
say "     ##  ##    ##        ##    ##  ##    ##  ##      ##  ##"
say "     ##    ##  ########  ##    ##  ######    ##      ##  ########"
say ""
say "Everything important is in README.md at the stick root - what each"
say "launcher does, in what order, and what to do when something goes"
say "wrong. Two minutes there will save you twenty later. Thank you!"
say ""

# ---- Step 9: sink out of the way --------------------------------------------
# Rename the two root entry points. This worker script keeps its name on
# purpose: the renamed launcher points at this exact path, and keeping it
# stable is what lets that launcher run again later (it finds the marker
# and simply reports setup as already done).
if [ -f "$USB_ROOT/1stRun.Setup.desktop" ]; then
    mv -f "$USB_ROOT/1stRun.Setup.desktop" "$USB_ROOT/z.SetupAlreadyRan.desktop"
fi
if [ -f "$USB_ROOT/1stRun.Setup.bat" ]; then
    mv -f "$USB_ROOT/1stRun.Setup.bat" "$USB_ROOT/z.SetupAlreadyRan.bat"
fi

# Last of all: hold the completion summary on screen (see pause_at_end).
pause_at_end
