#!/bin/sh

SRCDIR="."
#DESTDIR="../reports"
DESTDIR=".."
DEST="../ReportIndex.php"


cat genreportheader.php | sed "s/REPORT_TITLE/Available Reports/" | \
                          sed "s/REPORT_DATE/`date`/" | \
                          sed "s/REPORT_DESCRIPTION//" > $DEST

echo '<DL>' >> $DEST

for x in ${SRCDIR}/*query ; do
  name=`echo $x | sed "s%${SRCDIR}/%%" | sed "s/query$//"`
  eval `cat $x`

  echo '<DT> <a href="'${name}report.php'">'$TITLE'</a></DT><DD>'$DESCRIPTION'</DD>' >> $DEST

  DESCRIPTION="" ; QUERY="" ; TITLE="" # zero out before looping
done

echo '</DL>' >> $DEST
cat genreportfooter.php >> $DEST
