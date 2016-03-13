#!/bin/bash



### Dump tables
#
MYDIR=`dirname $0`
cd $MYDIR
../sbin/db-dump-table-data wp_options > config.sql
