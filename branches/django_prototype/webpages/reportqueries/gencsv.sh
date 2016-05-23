#!/bin/sh

DATABASE=`cat ../../db_name.php | awk -F'"' '/DBDB/ {print $4}'`
DBUSERNAME=`cat ../../db_name.php | awk -F'"' '/DBUSERID/ {print $4}'`
DBPASSWORD=`cat ../../db_name.php | awk -F'"' '/DBPASSWORD/ {print $4}'`
SRCDIR="."
#DESTDIR="../reports"
DESTDIR=".."

umask 022 

for x in ${SRCDIR}/4*query ; do
  name=`echo $x | sed "s%${SRCDIR}/%%" | sed "s/query$//"`
  eval `cat $x`
  echo $x

  echo $QUERY | ~/usr/bin/mysql -u $DBUSERNAME -H $DATABASE -p$DBPASSWORD | \
     sed 's%"%""%g' |\
     sed 's%MAKEMEACOMMA ,%</TD><TD>%g' |\
     sed 's%MAKEMEACOMMA </TD>%</TD>%g' |\
     sed 's%<TD>MAKEMEACOMMA%<TD>%g' |\
     sed 's%MAKEMEACOMMA%","%g' |\
     sed 's%<TABLE BORDER=1>%%g' |\
     sed 's%</TABLE>%%g' |\
     sed 's%<TD>%"%g' |\
     sed 's%<TH>%"%g' |\
     sed 's%</TD>%",%g' |\
     sed 's%</TH>%",%g' |\
     sed 's%<TR>%%g' |\
     sed 's%\015%\\n%g' |\
     sed 's%\\n\\n%\\n%g' |\
     sed 's%\\n\\n%\\n%g' |\
     sed 's%,</TR>%\015%g' |\
     tr '\015' '\n' |\
     tr '\240\351\221\222\223\224\225\226\227'  ' e`\047""*--' |\
     tr '\200-\377' '?' > $DESTDIR/${name}report.csv
  
  DESCRIPTION="" ; QUERY="" ; TITLE="" # zero out before looping
done
