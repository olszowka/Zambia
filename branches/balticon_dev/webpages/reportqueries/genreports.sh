#!/bin/sh

export TZ=US/Eastern

DATABASE=`cat ../db_name.php | awk -F'"' '/DBDB/ {print $4}'`
DBUSERNAME=`cat ../db_name.php | awk -F'"' '/DBUSERID/ {print $4}'`
DBPASSWORD=`cat ../db_name.php | awk -F'"' '/DBPASSWORD/ {print $4}'`
CON_NAME=`cat ../db_name.php | awk -F'"' '/CON_NAME/ {print $4}'`
HOST_NAME=`cat ../db_name.php | awk -F'"' '/DBHOSTNAME/ {print $4}'`

SRCDIR="."
#DESTDIR="../reports"
DESTDIR=".."

umask 022

#mysql -u $DBUSERNAME -H $DATABASE -p $DBPASSWORD -e '\. fixnames'

for x in ${SRCDIR}/*query ; do
  MIGRATED="false"
  name=`echo $x | sed "s%${SRCDIR}/%%" | sed "s/query$//"`
  eval `cat $x`
  echo $x
  if [[ -n $MIGRATED && $MIGRATED != "false" ]]; then
    continue;
  fi;

  cat genreportheader.php | sed "s/REPORT_TITLE/$TITLE/" | \
                            sed "s/REPORT_DATE/`date`/" | \
                            sed "s/REPORT_LINK/${name}report.php/" | \
                            sed "s%REPORT_DESCRIPTION%$DESCRIPTION%" | \
                            sed "s%CON_NAME%$CON_NAME%" > $DESTDIR/${name}report.php

  echo $QUERY | mysql -h $HOST_NAME -u $DBUSERNAME -H $DATABASE -p$DBPASSWORD >> $DESTDIR/${name}report.php

  cat genreportfooter.php >> $DESTDIR/${name}report.php
  
  DESCRIPTION="" ; QUERY="" ; TITLE="" ; MIGRATED="false" # zero out before looping
done