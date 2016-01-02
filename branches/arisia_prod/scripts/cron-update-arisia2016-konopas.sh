#!/bin/sh

ts=`date '+%Y-%m-%d_%H:%M'`
cm="konopas.appcache"
raw="data/arisia2016.js"
tgt="$raw.gz"
tmp="$tgt.$ts"

zambia_url="http://zambia.arisia.org/konOpas.php"
# update_url="https://konopas-server.appspot.com/arisia2015/update_prog"

cd /home/hosting/public_html/schedule.arisia.org/

curl "$zambia_url" > "$raw" 2>/dev/null

gzip -c "$raw" > "$tmp" 2>/dev/null

if ! zcmp "$tmp" "$tgt" >/dev/null 2>&1
then
	cp "$tmp" "$tgt"
	d=`date`
	sed -i "s/^# .*/# $d/" $cm 2>/dev/null
#	curl "$update_url" >/dev/null 2>&1
else
	rm "$tmp"
fi
