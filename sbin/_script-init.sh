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
VARDIR="$ROOTDIR/var"
LOGDIR="$ROOTDIR/var/log"
TMPDIR="$ROOTDIR/var/tmp"
SBINDIR="$ROOTDIR/sbin"
ANSIBLEDIR="$ROOTDIR/sbin/ansible"



### Initialize ansible if necessary
#
ANSIBLE_PATH=`which ansible`
ANSIBLE_REALPATH=`realpath $ANSIBLE_PATH`
ANSIBLE_DIRPATH=`dirname $ANSIBLE_REALPATH`
source $ANSIBLE_DIRPATH/../hacking/env-setup



### Change dir to project repo root
#
cd $ROOTDIR
