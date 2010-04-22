#!/bin/bash

DBHOSTNAME=`cat ../db_name.php | awk -F'"' '/DBHOSTNAME/ {print $4}'`
DATABASE=`cat ../db_name.php | awk -F'"' '/DBDB/ {print $4}'`
DBUSERNAME=`cat ../db_name.php | awk -F'"' '/DBUSERID/ {print $4}'`
DBPASSWORD=`cat ../db_name.php | awk -F'"' '/DBPASSWORD/ {print $4}'`
SRCDIR="."
#DESTDIR="../reports"
DESTDIR=".."

umask 022 

for x in ${SRCDIR}/4*query ; do
  name=`echo $x | sed "s%${SRCDIR}/%%" | sed "s/query$//"`
  eval `cat $x`
  echo $x

  echo $QUERY | mysql -h$DBHOSTNAME -u$DBUSERNAME -p$DBPASSWORD -B $DATABASE | \
    sed 's/\t/","/g' |
    sed 's/^/"/' | 
    sed 's/$/"/' > $DESTDIR/${name}report.csv
  
  DESCRIPTION="" ; QUERY="" ; TITLE="" # zero out before looping
done
