#!/bin/bash



### Dump config table(s)
#
MYDIR=`dirname $0`
cd $MYDIR

../sbin/db-dump-table-data wp_options > $MYDIR/config.sql
