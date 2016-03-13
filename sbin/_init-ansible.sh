#!/bin/bash



### Initialize script framework
#
MYDIR_REL=`dirname "$0"`
MYDIR_ABS=`realpath "$MYDIR_REL"`
source "$MYDIR_ABS/_init-script.sh"



### Initialize ansible if necessary
#
export PATH="/usr/local/ansible-git/bin:$PATH"
ANSIBLE_PATH=`which ansible`
ANSIBLE_REALPATH=`realpath $ANSIBLE_PATH`
ANSIBLE_DIRPATH=`dirname $ANSIBLE_REALPATH`
source $ANSIBLE_DIRPATH/../hacking/env-setup 2>&1 | tail -n+29



### Configuration
#
ANSIBLEDIR="$ROOTDIR/sbin/ansible"
cd $ANSIBLEDIR
