#!/bin/bash



### Configure shell
#
set -e
set -u



### Initialize directories
#
MYDIR_REL=`dirname "$0"`
MYDIR_ABS=`realpath "$MYDIR_REL"`
ROOTDIR=`dirname "$MYDIR_ABS"`
CONFIGDIR="$ROOTDIR/conf"
PUBDIR="$ROOTDIR/public"
WPDIR="$PUBDIR"
WORDPRESSDIR="$PUBDIR"
VARDIR="$ROOTDIR/var"
LOGDIR="$ROOTDIR/var/log"
TMPDIR="$ROOTDIR/var/tmp"
SBINDIR="$ROOTDIR/sbin"



### Change dir to project repo root
#
cd $ROOTDIR



### Fatal error handler
#
_fatalError() {
    ERR_FILE="$0"
    ERR_MSG="$1"
    ERR_LINE="${2:--}"
    echo "[$ERR_FILE:$ERR_LINE] ERROR: $ERR_MSG" 1>&2
    exit 1
}



### Message output handlers
#
_echo() {
    echo "[$0] $1"
}
_debug() {
    echo "[$0] [DEBUG] $1"
}
