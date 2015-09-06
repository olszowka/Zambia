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
define("CON_START_DATIM","2009-08-06 00:00:00"); // Used by reports
define("DAY_CUTOFF_HOUR",8); // times before this hour (of 0-23) are considered previous day
		// used for Participant Availability only
define("FIRST_DAY_START_TIME","17:30"); // next 5 are for grid scheduler
define("OTHER_DAY_STOP_TIME","25:00");
define("OTHER_DAY_START_TIME","8:30");
define("LAST_DAY_STOP_TIME","16:00");
define("STANDARD_BLOCK_LENGTH","1:30"); // "1:00" and "1:30" are only values supported
        // Block includes length of panel plus time to get to next panel, e.g. 55 min plus 5 min.
define("DURATION_IN_MINUTES","FALSE"); // TRUE: in mmm; False: in hh:mm
        // affects session edit/create page only, not reports
define("DEFAULT_DURATION","1:15"); // must correspond to DURATION_IN_MINUTES
define("SMTP_ADDRESS","smtp-out.netbusters.com");
define("SWIFT_DIRECTORY","/home/zambia_admin/Swift/"); //location of installed swift library
define("PREF_TTL_SESNS_LMT",10); // Input data verification limit for preferred total number of sessions
define("PREF_DLY_SESNS_LMT",5); // Input data verification limit for preferred daily limit of sessions
define("AVAILABILITY_ROWS",8); // Number of rows of availability records to render
define("MAX_BIO_LEN",1000); // Maximum length (in characters) permitted for participant biographies
define("MY_AVAIL_KIDS","FALSE"); // Enables questions regarding no. of kids in Fasttrack on "My Availability"
define("ENABLE_SHARE_EMAIL_QUESTION",true); // Enables question regarding sharing participant email address
define("ENABLE_USE_PHOTO_QUESTION",TRUE); // Enables question regarding using participant photo for promotional purposes
define("ENABLE_BESTWAY_QUESTION",false); // Enables question regarding best way to contact participant
define("BILINGUAL","TRUE"); // Triggers extra fields in Session and "My General Interests"
define("SECOND_LANG","FRENCH");
define("SECOND_TITLE_CAPTION","Titre en fran&ccedil;ais");
define("SECOND_DESCRIPTION_CAPTION","Description en fran&ccedil;ais");
define("SECOND_BIOGRAPHY_CAPTION","Biographie en fran&ccedil;ais");
define("BASESESSIONDIR","/var/lib/php5"); // vestigial
define("SHOW_BRAINSTORM_LOGIN_HINT",FALSE);
global $daymap;
$daymap = array ('long' => array(1 => "Thursday", 2 => "Friday", 3 => "Saturday", 4 => "Sunday", 5 => "Monday"),
    'short' => array(1 => 'Thu', 2 => 'Fri', 3 => 'Sat', 4 => 'Sun', 5 => 'Mon'));
define("stripfancy_from",""); //db & html now is utf8, so supports many characters and this feature is less likely
//                              to be useful.  Define these two strings
//                              if you need some characters not to be used.  "from" and "to" should be equal length
//                              strings.  Any character matched in "from" will be replaced by corresponding character
//                              in "to".
define("stripfancy_to","");
?>
