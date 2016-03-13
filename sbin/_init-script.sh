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
