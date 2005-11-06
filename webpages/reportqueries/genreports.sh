#!/bin/sh

SRCDIR="."
#DESTDIR="../reports"
DESTDIR=".."

for x in ${SRCDIR}/*query ; do
  name=`echo $x | sed "s%${SRCDIR}/%%" | sed "s/query$//"`
  eval `cat $x`

  cat genreportheader.php | sed "s/REPORT_TITLE/$TITLE/" | \
                            sed "s/REPORT_DESCRIPTION/$DESCRIPTION/" > $DESTDIR/${name}report.php

  echo $QUERY | mysql -u olszowka -H trgprod >> $DESTDIR/${name}report.php

  cat genreportfooter.php >> $DESTDIR/${name}report.php
  
  DESCRIPTION="" ; QUERY="" ; TITLE="" # zero out before looping
done
