#!/bin/bash



MYDIR=`dirname $0`
$MYDIR/../sbin/db-dump-schema > schema.sql
