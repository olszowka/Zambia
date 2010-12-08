#!/bin/sh

export TZ=US/Eastern

SRCDIR="."
#DESTDIR="../reports"
DESTDIR=".."
DEST="../reportindex.php"

umask 022

cat genreportheader.php | sed "s/REPORT_TITLE/Available Reports/" | \
                          sed "s/REPORT_DATE/`date`/" | \
                          sed "s/REPORT_DESCRIPTION//" > $DEST

echo '<DL>' >> $DEST

CSVONLY=0
GENCSV=0
for x in ${SRCDIR}/*query ; do
  name=`echo $x | sed "s%${SRCDIR}/%%" | sed "s/query$//"`
  eval `cat $x`


  if [ $CSVONLY = 0 ] ; then 
    echo '<DT> <a href="'${name}report.php'">'$TITLE'</a></DT><DD>'$DESCRIPTION'</DD>' >> $DEST
  fi
  if [ $GENCSV = 1 ] ; then 
       echo '<DT> <a href="'${name}report.csv'">'$TITLE - csv'</a></DT><DD>'$DESCRIPTION in csv format'</DD>' >> $DEST
  fi

  CSVONLY=0; GENCSV=0 ; DESCRIPTION="" ; QUERY="" ; TITLE="" # zero out before looping
done

  

echo '</DL>' >> $DEST
cat genreportfooter.php >> $DEST
