## Comments
  - This document is still pretty rough.
  - You really want to read this whole file before doing anything.
  - Please help us improve this document. 

## 0 - Prep work

You need a server with: 
  - apache
     - Zambia might work with other web servers, but hasn't been tested on them.
  - php
     - version 8.0.X or 8.1.X.  It might work with 8.2, but hasn't been tested on it. 
     - needs to have xsl and multibyte libraries installed and enabled
       (often, but not always, included by default)
  - mysql
    - version 5.7.X or 8.0.X
    - server property `ONLY_FULL_GROUP_BY` is disabled
[(notes)](https://github.com/olszowka/Zambia/wiki/MySQL-Issue----ONLY_FULL_GROUP_BY)
  - a mail relay to accept mail for sending via smtp
       (optional; needed only if you want to send email from Zambia)
Please test them and make sure they work.  

You need to decide on: 
  - database name          (`zambiademo` is used herein)
  - database user name     (`zambiademo` is used herein)
  - database user password (`4fandom` is used herein)
  - web install location   (`/home/petero/public_html/zambiademo` is used herein)

## 1 -  Create a database and a user

You need to have root access to the mysql instance to do this.   
If you don't please ask the person who does to do these 4 steps. 

    mysql -u root -p

    mysql> create database zambiademo;
    Query OK, 1 row affected (0.00 sec)

    mysql> grant all on zambiademo.* to 'zambiademo'@'localhost' 
           identified by '4fandom'; 
    Query OK, 0 rows affected (0.07 sec)

    mysql> grant lock tables on zambiademo.* to 'zambiademo'@'localhost' ;
    Query OK, 0 rows affected (0.00 sec)

    mysql> flush privileges;
    Query OK, 0 rows affected (0.31 sec)

You may want to create multiple MySQL users with each having only the necessary privileges, e.g.

 - administrator — Everything as shown above
 - php user — SELECT, INSERT, UPDATE, DELETE, and LOCK TABLES
 - backup user — SELECT and LOCK TABLES

## 2 -  Setting up the database 

You'll need the account and the database created in step 1. 

    mysql -u zambiademo -p

    mysql> use zambiademo
    Database changed

    mysql> \. EmptyDbase.dump
    Query OK ...   (snipped for sanity)

Now you have an empty database that is ready for use with Zambia.  You may instead
use DemoDbase.dump or SampleDbase.dump.
 - `EmptyDbase.dump` — only tables required for Zambia to run at all or with hard coded values are populated.
 - `SampleDbase.dump` — contains data from `EmptyDbase.dump` plus some configuration data you will need to edit.  Also contains users “1” and “2” with password “demo”.
 - `DemoDbase.dump` — contains data from `EmptyDbase.dump`, `SampleDbase.dump`, and some participant preference and schedule data.

## 3 - Setting up the webpages 

Checking out the html and php code. 

    cd /home/petero/public_html
    git clone https://github.com/olszowka/Zambia.git zambiademo

## 4 - Tweak the configuration to use your database and specify other preferences

You want to copy `db_name_sample.php` to `../db_name.php` and edit it as needed.  In other words, `db_name.php` should be in the parent
directory of Zambia, a directory which is not served by apache.  The file
`db_functions.php` loads this file, so you may edit the location if necessary.

## 5 -  Check it all out

    http://zambia.dreamhosters.com/~petero/zambiademo 

or whatever your URL is... 

## 6 - Ah, an account for in zambia so you can log in.  That would be useful.

Zambia may take a feed from the registration system to create and configure users.  However, the
code for handling that is specific to each registration system and not in the master branch. 

There is a script `add_zambia_users.php` in `scripts` directory to add users.

Usage:

    php -f add_zambia_users.php input_file.csv

The input_file should have field names in first row.  See `add_user_sample_input.csv`.  If there is are one or more
columns in `CongoDump` you don't care about, you may skip them entirely including skipping them from the header row. The
other fields are all required.

## 7 - Mail relay configuration

If you want to have Zambia send email for password resets and for mailmerges to various groups of users, you
need to arrange for a mail relay serving and configure Zambia to connect to it.  Consult the documentation for your
mail relay service and configure the following constants in `db_name.php`:

 - `SMTP_ADDRESS`
 - `SMTP_PORT`
 - `SMTP_PROTOCOL`
 - `SMTP_USER`
 - `SMTP_PASSWORD`
    
You may leave `SMTP_USER` and/or `SMTP_PASSWORD` blank to skip authentication if that is appropriate for your mail relay.
Likely options for `SMTP_PORT` are "587", "2525", "25", or "465".
Options for `SMTP_PROTOCOL` are "", "SSL", or "TLS".  Blank/Default is no encryption.

## 8 - reCAPTCHA

Because the mechanism for users to reset their own passwords can send email from an unauthenticated user,
that functionality is gated by reCAPTCHA to prevent bots from using it. If you will be allowing users to
reset their own passwords, do the following to configure reCAPTCHA.
 1. If you don't have one, create an account with Google reCAPTCHA. Zambia uses v2.
 2. Configure that account with the URL you'll be using to serve Zambia.
 3. Get the following parameters from the reCAPTCHA admin page and configure in db_name.php
   - `RECAPTCHA_SITE_KEY`
   - `RECAPTCHA_SERVER_KEY`

## 9 - Backups are a good thing 

If you are changing php and html files, I suggest you fork Zambia on github and commit your changes to your fork.

If you care about dbase content, see `backup_mysql` and `clean_backups` in the 
scripts directory.  You'll want to run them or something similar.   

## 10 - Reaching us

I can be reached via github.

   Have Fun!

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-- PeterO

## 11 - Wiki

More documentation, particularly about configuration, can be found on the [wiki](https://github.com/olszowka/Zambia/wiki).
