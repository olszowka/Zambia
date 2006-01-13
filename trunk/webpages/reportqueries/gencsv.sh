#!/bin/sh

SRCDIR="."
#DESTDIR="../reports"
DESTDIR=".."

for x in ${SRCDIR}/4*query ; do
  name=`echo $x | sed "s%${SRCDIR}/%%" | sed "s/query$//"`
  eval `cat $x`
  echo $x

  echo $QUERY | mysql -u olszowka -H trgprod | \
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
     sed 's%,</TR>%
%g' |\
     tr '
' '\n' > $DESTDIR/${name}report.csv
  
  DESCRIPTION="" ; QUERY="" ; TITLE="" # zero out before looping
done
