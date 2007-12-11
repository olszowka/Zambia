#!/bin/sh

umask 022

SRCDIR="."
#DESTDIR="../reports"
DESTDIR=".."

categories="GAMING PROG EVENTS GOH PUBS FASTTRACK TECH HOTEL CONFLICT REG GRIDS"

CVSONLY=0; GENCVS=0 ; DESCRIPTION="" ; QUERY="" ; TITLE="" # zero out before looping
for i in $categories ; do 

   echo $i

   DEST="../report${i}.php"
   
   cat genreportheader.php | sed "s/REPORT_TITLE/$i Reports/" | \
                             sed "s/REPORT_DATE/`date`/" | \
                             sed "s/REPORT_DESCRIPTION/For report changes email zambia@arisia.org./" > $DEST

   echo '<DL>' >> $DEST

   for x in `grep -l ${i}WANTS=1 ${SRCDIR}/*query` ; do 
     name=`echo $x | sed "s%${SRCDIR}/%%" | sed "s/query$//"`
     eval `cat $x`

     if [ $CVSONLY = 0 ] ; then
       echo '<DT> <a href="'${name}report.php'">'$TITLE'</a></DT><DD>'$DESCRIPTION'</DD>' >> $DEST
     fi
     if [ $GENCVS = 1 ] ; then
       echo '<DT> <a href="'${name}report.csv'">'$TITLE - csv'</a></DT><DD>'$DESCRIPTION in csv format'</DD>' >> $DEST
     fi

     # zero out before looping
     CVSONLY=0; GENCVS=0 ; DESCRIPTION="" ; QUERY="" ; TITLE="" 
   done

  echo '</DL>' >> $DEST
  cat genreportfooter.php >> $DEST
done
