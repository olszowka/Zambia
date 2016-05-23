mkdir tmp
for x in *query ; do 
  cat $x | sed "s/DATE_FORMAT(ADDTIME('2009-01-16 00:00:00/DATE_FORMAT(ADDTIME('2011-01-14 00:00:00/g" > tmp/$x
done
