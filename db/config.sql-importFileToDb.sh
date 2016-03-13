#!/bin/bash



### Import config table(s)
#
MYDIR_REL=`dirname $0`
MYDIR=`realpath $MYDIR_REL`
cd $MYDIR

../sbin/db-import-table-data $MYDIR/config.sql wp_options
