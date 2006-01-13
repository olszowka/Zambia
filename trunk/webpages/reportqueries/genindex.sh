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
  case "$x" in 
     *4*)
         echo '<DT> <a href="'${name}report.csv'">'$TITLE - csv'</a></DT><DD>'$DESCRIPTION in csv format'</DD>' >> $DEST
     ;;
  esac

  DESCRIPTION="" ; QUERY="" ; TITLE="" # zero out before looping
done

  

echo '</DL>' >> $DEST
cat genreportfooter.php >> $DEST
