#!/bin/bash



MYDIR=`dirname $0`
cd $MYDIR

$MYDIR/../sbin/db-dump-schema > $MYDIR/schema.sql
