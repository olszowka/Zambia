## Comments
  - This document is still pretty rough.
  - You really want to read this whole file before doing anything.
  - Please help us improve this document. 

## 0 - Prep work

You need a server with: 
  - apache
     - Zambia might work with other web servers, but hasn't been tested on them.
  - php
     - version 8.2.X.  It might work with 8.3 or higher, but hasn't been tested on it. 
     - needs to have xsl and multibyte libraries installed and enabled
       (I haven't seen these missing in years)
  - MySQL or MariaDB
    - MySQL versions 8 or 9
    - MariaDB version 10
[(notes)](https://github.com/olszowka/Zambia/wiki/MySQL-Issue----ONLY_FULL_GROUP_BY)
  - a mail relay to accept mail for sending via smtp
       (optional; needed only if you want to send email from Zambia)
Please test them and make sure they work.  

You need to decide on: 
  - database name          (`zambiademo` is used herein)
  - database user name     (`zambiademo` is used herein)
  - database user password (`4fandom` is used herein)
  - web install location   (`/home/petero/zambiademo` is used herein)

## 1 -  Create a database and a user

You need to have root access to the mysql instance to do this.   
If you don't please ask the person who does to do these 4 steps. 

    mysql -u root -p

    mysql> create database zambiademo character set utf8mb4 collate utf8mb4_general_ci;
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

    mysql> \. EmptyDbase.sql
    Query OK ...   (snipped for sanity)

Now you have an empty database that is ready for use with Zambia.

You may apply additional scripts to add some more data.

 - `SampleDbase.sql`— contains sample configuration data you will likely want to edit.  See #10 below.  While there
 is sample data for many configuration tables that might be close to what many cons will want to use, the "rooms" table
 must be created specifically for each host facility.<br /><br />
 - `DemoDbase.sql`— contains data that would normally come from users actually using Zambia.  In particular, it
 contains two demo users: administrator user with userid 1 and password "demo" and participant user with userid 3 and
 password "demo".

## 3 - Setting up the webpages 

Checking out the html and php code. 

    cd /home/petero/
    git clone https://github.com/olszowka/Zambia.git zambiademo

Configure your webserver to serve the `webpages` directory within the repo as the root of your server/domain.

## 4 - Tweak the configuration to use your database and specify other preferences

You want to copy `db_name_sample.php` to `../db_name.php` and edit it as needed.  In other words, `db_name.php` should be in the parent
directory of Zambia(webpages), a directory which is not served by apache(or your other web server).  The file
`db_functions.php` loads this file, so you may edit the location if necessary.

## 5 -  Check it all out

    http://zambia.dreamhosters.com/ 

or whatever your URL is... 

## 6 - Ah, an account for in zambia so you can log in.  That would be useful.

Zambia may take a feed from the registration system to create and configure users.  There is code in the master
branch for integrating with ConTroll.  You'll want to reach out to the Controll developers to use that.

For creating initial users in order to configure Zambia and create users within it, 
there is a script `add_zambia_users.php` in `scripts` directory to add users.

Usage:

    php -f add_zambia_users.php input_file.csv

The input_file should have field names in first row.  See `add_user_sample_input.csv`.  If there are one or more
columns in `CongoDump` you don't care about, you may skip them entirely including skipping them from the header row.
The other fields are all required. Note, permroleid = 1 corresponds to administrator.

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

## 9 - Build Report Menus

The files which specify the reports also specify the menu tree for the reports.  This mechanism makes it very easy
to rearrange the report menues or rename the reports for your convenience.  The first time you deploy Zambia and
aftereach time you edit any report configuration, you need to rebuild the report menus.  Zambia users with
administrative privileges will have a menu item under "Admin" called "Build Report Menus" to do this.

## 10 - Configuration from the Zambia administrative UI

There are quite a few mechanisms for configuring Zambia from its administrative UI.  This UI is the "Admin" menu
which appears for all users with administrative privileges.  See [Configuration](../Documentation/Configuration.md)
for more details.

## 11 - Backups are a good thing 

If you are changing php and html files, I suggest you fork Zambia on github and commit your changes to your fork.

If you care about dbase content, see `backup_mysql` and `clean_backups` in the 
scripts directory.  You'll want to run them or something similar.   

## 12 - Reaching us

I can be reached via github.

   Have Fun!

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-- PeterO

## 13 - Wiki

More documentation, particularly about configuration, can be found on the [wiki](https://github.com/olszowka/Zambia/wiki).
