#!/usr/bin/env bash
# a.Ports.UseDefaults.sh - return the stack to its default ports (Linux).
#
# Deletes SharedSettings/Ports.InUse.conf. From then on every start/stop
# script resolves its ports from SharedSettings/Ports.Default.conf again.
#
# Run it while the stack is STOPPED. It refuses to run while a stack server
# is alive: the stop scripts resolve ports from the same files, and they
# must always be able to find the servers they started.
#
# Lives in OS_Linux/Scripts; the double-click entry point is
# a.Ports.UseDefaults.desktop at the stick root.

# This worker lives in OS_Linux/Scripts; the stick root is two levels up.
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
USB_ROOT="$(cd "$SCRIPT_DIR/../.." && pwd)"
INUSE="$USB_ROOT/SharedSettings/Ports.InUse.conf"
DATA_DIR="$USB_ROOT/SharedFolders/data"

say()  { printf '%s\n' "$*"; }
fail() { printf '*** %s\n' "$*" >&2; pause_at_end; exit 1; }
pause_at_end() {
    if [ -t 0 ]; then printf 'Press Enter to close... '; read -r _ || true; fi
}

if [ -d "$DATA_DIR" ]; then
    shopt -s nullglob
    for pidfile in "$DATA_DIR"/*.pid; do
        pid="$(tr -dc '0-9' < "$pidfile")"
        if [ -n "$pid" ] && kill -0 "$pid" 2>/dev/null; then
            fail "a stack server is still running (pid $pid). Stop the stack first, then run this again."
        fi
    done
    shopt -u nullglob
fi

if [ ! -f "$INUSE" ]; then
    say "Ports.InUse.conf is already absent - the stack is on its default ports."
    pause_at_end
    exit 0
fi

# The shipped starter file is comments-only and carries no [InUsePorts]
# entries; if that is all this is, there is no decision to undo.
if ! grep -q '^\[InUsePorts\]' "$INUSE"; then
    say "No port decision is in force - the stack is on its default ports."
    pause_at_end
    exit 0
fi

rm -f "$INUSE" || fail "could not delete Ports.InUse.conf."
say "Ports.InUse.conf deleted. The launchers will use Ports.Default.conf."
say "a.Ports.FixConflicts will generate a fresh Ports.InUse.conf if you"
say "ever need one again."
pause_at_end
