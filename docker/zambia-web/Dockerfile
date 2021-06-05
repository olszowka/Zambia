FROM php:5.6.33-apache-jessie

RUN apt-get update && apt-get install -y libxslt-dev
RUN docker-php-ext-install mysql xsl mbstring

COPY db_name.php /var/www/html/
