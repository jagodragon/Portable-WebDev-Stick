# ports-resolve.sh - shared port resolution for the Portable WebDev Stick.
#
# Sourced by the start/stop scripts (. ports-resolve.sh), never run
# directly. The caller must set USB_ROOT first. Calling resolve_ports sets
# PORT_WEB, PORT_MARIADB, PORT_REDIS, PORT_FPM and PORT_NODE, and exports
# the matching WEBDEV_PORT_* variables so the servers themselves - httpd's
# ${} config expansion, PHP-FPM's listen line
# and node-app.js - all see the same decided values.
#
# Resolution order, per port:
#   1. the environment variable WEBDEV_PORT_<KEY>
#   2. SharedSettings/Ports.InUse.conf, section [InUsePorts]
#   3. SharedSettings/Ports.Default.conf, section [DefaultPorts]
#   4. the built-in default below
# a.Ports.FixConflicts writes (2); a.Ports.UseDefaults deletes it.

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

resolve_one_port() {  # $1 = key, $2 = built-in default; prints the port
    local ev="WEBDEV_PORT_$1" v=""
    v="${!ev:-}"
    if [ -z "$v" ]; then
        v="$(ports_conf_get "$USB_ROOT/SharedSettings/Ports.InUse.conf" InUsePorts "$1")"
    fi
    if [ -z "$v" ]; then
        v="$(ports_conf_get "$USB_ROOT/SharedSettings/Ports.Default.conf" DefaultPorts "$1")"
    fi
    [[ "$v" =~ ^[0-9]+$ ]] || v="$2"
    printf '%s' "$v"
}

resolve_ports() {
    PORT_WEB="$(resolve_one_port WEB 8080)"
    PORT_MARIADB="$(resolve_one_port MARIADB 3306)"
    PORT_REDIS="$(resolve_one_port REDIS 6379)"
    PORT_FPM="$(resolve_one_port FPM 9000)"
    PORT_NODE="$(resolve_one_port NODE 3000)"
    export WEBDEV_PORT_WEB="$PORT_WEB" WEBDEV_PORT_MARIADB="$PORT_MARIADB" \
           WEBDEV_PORT_REDIS="$PORT_REDIS" WEBDEV_PORT_FPM="$PORT_FPM" \
           WEBDEV_PORT_NODE="$PORT_NODE"
}
