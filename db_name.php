<?php
// This is an example file.  Please copy to db_name.php and edit as needed.
define("DBHOSTNAME","127.0.0.1");
define("DBUSERID","zambia_web");
define("DBPASSWORD","zambia");
define("DBDB","zambia_arisia_prod");
define("CON_NAME","Head on mac mini");
define("ADMIN_EMAIL","zambia@arisia.org");
define("BRAINSTORM_EMAIL","brain@somewhere.net");
define("PROGRAM_EMAIL","programming@arisia.org");
define("REG_EMAIL","registration@arisia.org");
define("CON_NUM_DAYS",4); // code works for 1 - 8
define("CON_START_DATIM","2015-01-16 00:00:00"); // Intended for use by report scripts; currently used by static
        // grids only (just a checkin test)
define("DAY_CUTOFF_HOUR",6); // times before this hour (of 0-23) are considered previous day
		// used for Participant Availability only
define("FIRST_DAY_START_TIME","17:30");
define("OTHER_DAY_STOP_TIME","25:00");
define("OTHER_DAY_START_TIME","8:30");
define("LAST_DAY_STOP_TIME","16:00");
define("STANDARD_BLOCK_LENGTH","1:30");
//define("SMTP_ADDRESS","mail.arisia.org");
define("SMTP_ADDRESS","mbarr.xen.prgmr.com");
define("SMTP_PORT","2525");
define("SWIFT_DIRECTORY","../../Swift-5.0.1/lib/"); //location of installed swift library
define("PREF_TTL_SESNS_LMT",10); // Input data verification limit for preferred total number of sessions
define("PREF_DLY_SESNS_LMT",5); // Input data verification limit for preferred daily limit of sessions
define("AVAILABILITY_ROWS",8); // Number of rows of availability records to render
define("MAX_BIO_LEN",1000); // Maximum length (in characters) permitted for participant biographies
define("MY_AVAIL_KIDS",FALSE); // Enables questions regarding no. of kids in Fasttrack on "My Availability"
define("ENABLE_SHARE_EMAIL_QUESTION",TRUE); // Enables question regarding sharing participant email address
define("ENABLE_USE_PHOTO_QUESTION",TRUE); // Enables question regarding using participant photo for promotional purposes
define("ENABLE_BESTWAY_QUESTION",FALSE); // Enables question regarding best way to contact participant
define("BILINGUAL","FALSE"); // Triggers extra fields in Session and "My General Interests"
define("SECOND_LANG","FRENCH");
define("SECOND_TITLE_CAPTION","Titre en fran&ccedil;ais");
define("SECOND_DESCRIPTION_CAPTION","Description en fran&ccedil;ais");
define("SECOND_BIOGRAPHY_CAPTION","Biographie en fran&ccedil;ais");
define("DURATION_IN_MINUTES","FALSE"); // TRUE: in mmm; False: in hh:mm
        // affects session edit/create page only, not reports
define("DEFAULT_DURATION","1:15"); // must correspond to DURATION_IN_MINUTES
define("BASESESSIONDIR","/var/lib/php5");
define("SHOW_BRAINSTORM_LOGIN_HINT",FALSE);
global $daymap;
$daymap = array ('long' => array(1 => "Friday", 2 => "Saturday", 3 => "Sunday", 4 => "Monday"),
    'short' => array(1 => 'Fri', 2 => 'Sat', 3 => 'Sun', 4 => 'Mon'));
define("stripfancy_from","");
define("stripfancy_to","");
?>
