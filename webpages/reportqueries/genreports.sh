#!/bin/sh

DATABASE="zambiademo"
DBUSERNAME="zambiademo"
DBPASSWORD="4fandom"

SRCDIR="."
#DESTDIR="../reports"
DESTDIR=".."

#mysql -u $DBUSERNAME -H $DATABASE -p $DBPASSWORD -e '\. fixnames'

for x in ${SRCDIR}/*query ; do
  name=`echo $x | sed "s%${SRCDIR}/%%" | sed "s/query$//"`
  eval `cat $x`
  echo $x

  cat genreportheader.php | sed "s/REPORT_TITLE/$TITLE/" | \
                            sed "s/REPORT_DATE/`date`/" | \
                            sed "s%REPORT_DESCRIPTION%$DESCRIPTION%" > $DESTDIR/${name}report.php

  echo $QUERY | mysql -u $DBUSERNAME -H $DATABASE -p $DBPASSWORD >> $DESTDIR/${name}report.php

  cat genreportfooter.php >> $DESTDIR/${name}report.php
  
  DESCRIPTION="" ; QUERY="" ; TITLE="" # zero out before looping
done
