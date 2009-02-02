<?php
// This is an example file.  Please copy to db_name.php and edit as needed.
define("DBHOSTNAME","localhost");
define("DBUSERID","zambiademo");
define("DBPASSWORD","4fandom");
define("DBDB","zambiademo");
define("CON_NAME","Zambia Demo");
define("ADMIN_EMAIL","zambia@somewhere.net");
define("BRAINSTORM_EMAIL","brain@somewhere.net");
define("PROGRAM_EMAIL","program@somewhere.net");
define("REG_EMAIL","registration@somewhere.net");
define("CON_NUM_DAYS",5); // code works for 1 - 8
define("CON_START_DATIM","2009-08-06 00:00:00"); // Actually used by report script, not php as of yet
define("SMTP_ADDRESS","smtp-out.netbusters.com");
define("PREF_TTL_SESNS_LMT",10); // Input data verification limit for preferred total number of sessions
define("PREF_DLY_SESNS_LMT",5); // Input data verification limit for preferred daily limit of sessions
define("AVAILABILITY_ROWS",8); // Number of rows of availability records to render
define("MAX_BIO_LEN",1000); // Maximum length (in characters) permitted for participant biographies
define("BASESESSIONDIR","/var/lib/php5");
global $daymap;
$daymap = array ('long' => array(1 => "Thursday", 2 => "Friday", 3 => "Saturday", 4 => "Sunday", 5 => "Monday"),
    'short' => array(1 => 'Thu', 2 => 'Fri', 3 => 'Sat', 4 => 'Sun', 5 => 'Mon'));
define("stripfancy_from","ÀÁÂÃÄÅÆÇÈÉÊË®");
define("stripfancy_to","AAAAAAECEEEE ");
?>
