#!/usr/bin/env bash
# ============================================================================
#  Portable WebDev Stick - Repair Stick (Linux)
#  File: x.RepairStick.sh   Lives at: OS_Linux/Scripts/ on the stick.
#  Run:   double-click x.RepairStick.desktop at the stick root, or run
#         bash x.RepairStick.sh from this folder.
#
#  THE "MY CONFIG IS BROKEN" TOOL. It returns the stick's SETTINGS and
#  SETUP STATE to factory condition, so 1stRun.Setup can run again on a
#  known-clean configuration - without touching your work.
#
#  WHAT IT DOES:
#    - Restores every file in SharedSettings/ from the factory copies in
#      SharedFolders/usr/ (one .zip per file). Hand edits to those files
#      are lost - that is the point of the tool.
#    - Deletes SharedSettings/db-credentials.php and Ports.InUse.conf.
#      The next 1stRun.Setup run writes fresh ones, and re-keys the
#      MariaDB root and webdev accounts and the editor sign-in then.
#    - Empties SharedFolders/data/ and SharedFolders/tmp/ (pid files,
#      the Redis snapshot, PHP sessions, the .setup-done marker).
#    - Deletes log residue in the OS trees and a stale FPM pid file.
#    - Renames a spent z.SetupAlreadyRan.* pair back to 1stRun.Setup.*.
#
#  WHAT IT NEVER TOUCHES:
#    - Your databases. Every database and its contents survive exactly
#      as they are. Accounts too: your current passwords keep working
#      until you run 1stRun.Setup again and choose new ones.
#    - www/ - your projects and the kit's pages alike.
#    - The program files under OS_Windows/ and OS_Linux/.
#  If program FILES are damaged (not just settings), this is the wrong
#  tool - that is what the maintainer's clone-based reset is for.
#
#  SAFETY:
#    - It only runs on a stick: the stick's signature folders must sit
#      beside it, including the SharedFolders/usr/ factory copies.
#    - It refuses while ANY stack server is running from the stick
#      (every pid file under SharedFolders/data/ is checked for
#      liveness, and every running process's executable is checked for
#      living on the stick).
#    - It prints exactly what will happen and proceeds only when you
#      type the word REPAIR. Closed input (EOF) aborts - it cannot run
#      blind.
#    - Every step is safe to redo: if a repair is interrupted, run it
#      again and it finishes the job.
#
#  NEEDS: unzip. If unzip is missing this script tries to install it
#  with apt first (you will be asked for your password, in the open -
#  this script never raises its own privileges silently), and only
#  gives up if that fails too: "try to fix yourself if you want to
#  fail." It needs nothing from the stack itself: a repair tool
#  depends on the operating system, never on the thing being repaired.
# ============================================================================

set -u

# Locate the stick from THIS FILE's path, never from $0 alone: when a
# script is sourced ( . x.RepairStick.sh ) $0 stays pointing at the
# shell itself (e.g. /bin/bash) and the stick "moves" to /bin.
# BASH_SOURCE names the file actually being run either way. This file
# lives in OS_Linux/Scripts/, so the stick root is two levels up -
# the same resolution the setup script uses.
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]:-$0}")" && pwd)"
STICK="$(cd "$SCRIPT_DIR/../.." && pwd)"
SETTINGS="SharedSettings"
RUNTIME="SharedFolders"
PAYLOAD="$STICK/$RUNTIME/usr"
STAGE="$STICK/$RUNTIME/tmp/.repair-stage"

# The SharedSettings files that have a factory copy in SharedFolders/usr/.
# (db-credentials.php and Ports.InUse.conf are deliberately NOT in this
# list - they are per-stick state, deleted below, never restored.)
FACTORY_FILES="Ports.Default.conf httpd-ssl.conf httpd-vhosts.conf httpd.conf mariadb-configs.cnf node-app.js php-fpm.conf php.ini python-env.sh redis.conf setup-generate.php setup.sql"

say()  { printf '%s\n' "$*"; }
fail() { printf '\n*** Repair stopped: %s\n' "$*" >&2; pause_at_end; exit 1; }

pause_at_end() {
    if [ -t 0 ]; then
        printf '\nPress Enter to close...'
        read -r _ || true
    fi
}

# ---- Am I standing on a stick? ----------------------------------------------
[ -d "$STICK/$SETTINGS" ] && [ -d "$STICK/mariadb-data" ] && [ -d "$STICK/OS_Windows" ] && [ -d "$STICK/www" ] \
    || fail "I don't appear to be inside a WebDev stick (SharedSettings/, mariadb-data/, OS_Windows/ and www/ must sit two levels above OS_Linux/Scripts/)."
[ -d "$PAYLOAD" ] \
    || fail "the factory copies are missing ($RUNTIME/usr/ not found on this stick). Without them there is nothing to repair FROM."

# ---- Dependency: unzip (try to fix yourself if you want to fail) ------------
if ! command -v unzip >/dev/null 2>&1; then
    say "unzip is not installed - it is the one tool this script needs."
    say "Trying to install it with apt now. You will be asked for your password, in the open:"
    sudo apt-get update && sudo apt-get install -y unzip
    command -v unzip >/dev/null 2>&1 \
        || fail "unzip is still missing. Install it manually with:  sudo apt install unzip"
    say "unzip installed."
fi

# ---- The whole stack must be stopped ----------------------------------------
for pidfile in "$STICK/$RUNTIME/data"/*.pid; do
    [ -e "$pidfile" ] || continue
    P="$(cat "$pidfile" 2>/dev/null || true)"
    if [ -n "$P" ] && kill -0 "$P" 2>/dev/null; then
        fail "a stack server is still running from the stick (pid $P, recorded in $pidfile). Stop the stack first - repairing under running servers is how files get half-written."
    fi
done
# Servers with no pid file (Redis, the Node app) and orphans that
# outlived theirs: check every running process's executable instead.
RUNNING=""
for exe in /proc/[0-9]*/exe; do
    [ -e "$exe" ] || continue
    target="$(readlink "$exe" 2>/dev/null || true)"
    case "$target" in
        "$STICK"/*)
            pid="${exe#/proc/}"; pid="${pid%/exe}"
            RUNNING="${RUNNING}  pid $pid  $target
"
            ;;
    esac
done
if [ -n "$RUNNING" ]; then
    say "Processes still running from the stick:"
    printf '%s' "$RUNNING"
    fail "something is still running from the stick (see the list above). Stop the stack - and close anything else running from the stick - first."
fi

# ---- Say exactly what is about to happen, then make a human agree ------------
say "REPAIR STICK - the stick's settings and setup state will be restored"
say "to factory condition. The stick is: $STICK"
say ""
say "RESTORED from the factory copies in $RUNTIME/usr/:"
say "  every file in SharedSettings/ (hand edits to them are lost)"
say "DELETED:"
say "  db-credentials.php and Ports.InUse.conf (setup writes fresh ones),"
say "  everything in $RUNTIME/data/ and $RUNTIME/tmp/, log residue."
say "RENAMED BACK: a spent z.SetupAlreadyRan.* pair becomes 1stRun.Setup.*"
say "NEVER TOUCHED: your databases and their contents, www/, the"
say "  program files. Your current passwords keep working until you run"
say "  1stRun.Setup again - running it is the final step of the repair."
say ""
printf 'Type REPAIR to proceed, anything else to abort: '
if ! read -r ANSWER; then
    fail "no answer received (input closed). Repair aborted - this script will not run blind."
fi
[ "$ANSWER" = "REPAIR" ] || fail "aborted (you typed '$ANSWER', not REPAIR). Nothing was changed."

PARTIAL="The repair is PARTIAL - run x.RepairStick again to finish it."

# ---- 1) Stage the factory copies and verify them BEFORE touching anything --
rm -rf "$STAGE"
mkdir -p "$STAGE" || fail "could not create the staging folder ($STAGE)."
for f in $FACTORY_FILES; do
    [ -e "$PAYLOAD/$f.zip" ] \
        || fail "the factory copy $RUNTIME/usr/$f.zip is missing. Nothing was changed - restore the usr/ folder (it ships with the kit) and run the repair again."
    unzip -o -q "$PAYLOAD/$f.zip" -d "$STAGE" \
        || fail "the factory copy $RUNTIME/usr/$f.zip could not be extracted (damaged?). Nothing was changed."
    [ -e "$STAGE/$f" ] \
        || fail "the factory copy $RUNTIME/usr/$f.zip did not contain $f. Nothing was changed."
done

# ---- 2) Restore SharedSettings ----------------------------------------------
for f in $FACTORY_FILES; do
    cp -f "$STAGE/$f" "$STICK/$SETTINGS/$f" \
        || fail "could not restore $SETTINGS/$f (exit $?). $PARTIAL"
done

# ---- 3) Delete the per-stick state files -------------------------------------
rm -f "$STICK/$SETTINGS/db-credentials.php" "$STICK/$SETTINGS/Ports.InUse.conf"

# ---- 4) Empty the runtime folders, back to their first-run state -------------
# (usr/ is NOT under either of these - the factory copies survive.)
find "$STICK/$RUNTIME/data" -mindepth 1 -delete 2>/dev/null
find "$STICK/$RUNTIME/tmp"  -mindepth 1 -delete 2>/dev/null
: > "$STICK/$RUNTIME/data/tmp"
: > "$STICK/$RUNTIME/tmp/tmp"
[ -e "$STICK/$RUNTIME/data/.setup-done" ] \
    && fail ".setup-done is still present after clearing $RUNTIME/data/. $PARTIAL"

# ---- 5) Log and pid residue in the OS trees ----------------------------------
for d in "OS_Windows/Software/apache-server/logs" "OS_Linux/Software/apache-server/logs"; do
    [ -d "$STICK/$d" ] && find "$STICK/$d" -mindepth 1 -delete 2>/dev/null
done
rm -f "$STICK/OS_Linux/Software/php-desktop/php-fpm.pid"
rm -f "$STICK"/mariadb-data/*.err

# ---- 6) The setup entry points get their fresh names back --------------------
for ext in bat desktop; do
    if [ -e "$STICK/z.SetupAlreadyRan.$ext" ] && [ -e "$STICK/1stRun.Setup.$ext" ]; then
        say "NOTE: both z.SetupAlreadyRan.$ext and 1stRun.Setup.$ext exist - leaving both as they are."
    elif [ -e "$STICK/z.SetupAlreadyRan.$ext" ]; then
        mv -f "$STICK/z.SetupAlreadyRan.$ext" "$STICK/1stRun.Setup.$ext" \
            || fail "could not rename z.SetupAlreadyRan.$ext back to 1stRun.Setup.$ext. $PARTIAL"
    fi
done

say ""
say "Repair complete. Settings and setup state are factory-fresh; your"
say "databases, projects and program files were not touched."
say "Next step: run 1stRun.Setup and choose new passwords - until you"
say "do, the current passwords keep working."
pause_at_end
