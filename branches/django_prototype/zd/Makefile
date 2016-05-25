D=/var/www/zd
S=/home/hack/svn/zambia/branches/django_prototype/zd
dist:
	tar cjf zd.tbz manage.py zd/*.py zambia/*.py zambia/migrations/*.py zambia/templates
install:
	cp -r zd/*.py $D/zd
	cp -r zambia/*.py $D/zambia
	cp -r zambia/migrations/*.py $D/zambia/migrations
	cp -r zambia/templates $D/zambia
	python manage.py migrate
	cp zd.conf /etc/httpd/conf.d/
	service httpd restart
svn:
	cp manage.py create-db-name.py Makefile schema.diff zd.conf $S
	cp -r zd/*.py $S/zd
	cp -r zambia/*.py $S/zambia
	cp -r zambia/migrations/*.py $S/zambia/migrations
	cp -r zambia/templates $S/zambia
