mkdir tmp
for x in *query ; do 
  cat $x | sed "s/DATE_FORMAT(ADDTIME('2008-01-18/DATE_FORMAT(ADDTIME('2009-01-16/g" > tmp/$x
done
