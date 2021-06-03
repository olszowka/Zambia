<?php
// This is an example file.  Please copy to db_name.php and edit as needed.
// Copyright (c) 2008-2021 Peter Olszowka. All rights reserved.
// See copyright document for more details.
define("DBHOSTNAME", "localhost");
define("DBUSERID", "zambiademo");
define("DBPASSWORD", "4fandom");
define("DBDB", "zambiademo");
define("CON_NAME", "Zambia Demo");
define("BRAINSTORM_EMAIL", "brain@somewhere.net");
define("PROGRAM_EMAIL", "program@somewhere.net");
define("CON_NUM_DAYS", 5); // code works for 1 - 8
define("PHP_DEFAULT_TIMEZONE", "AMERICA/NEW_YORK"); // must be valid argument to php date_default_timezone_set()  Should correspond with DB configuration
define("DB_DEFAULT_TIMEZONE", "US/Eastern"); // must be valid argument to set time_zone,  Should correspond with PHP configuration
define("CON_START_DATIM", "2009-08-06 00:00:00"); // Broadly used.  Must be in mysql format: "YYYY-MM-DD HH:MM:SS" (HH:00-23) HH:MM:SS probably should be 00:00:00
define("DAY_CUTOFF_HOUR", 8); // times before this hour (of 0-23) are considered previous day
		// used for Participant Availability only
define("FIRST_DAY_START_TIME", "17:30"); // next 5 are for grid scheduler
define("OTHER_DAY_STOP_TIME", "25:00");
define("OTHER_DAY_START_TIME", "8:30");
define("LAST_DAY_STOP_TIME", "16:00");
define("STANDARD_BLOCK_LENGTH", "1:30"); // "1:00" and "1:30" are only values supported
        // Block includes length of panel plus time to get to next panel, e.g. 55 min plus 5 min.
define("DURATION_IN_MINUTES", FALSE); // TRUE: in mmm; FALSE: in hh:mm
        // affects session edit/create page only, not reports
define("DEFAULT_DURATION", "1:15"); // must correspond to DURATION_IN_MINUTES
define("SMTP_ADDRESS", "smtp-out.netbusters.com"); // See documentation for your mail relay service.
define("SMTP_PORT", "587"); // Likely options are "587", "2525", "25", or "465".  See documentation for your mail relay service.
define("SMTP_PROTOCOL", "TLS"); // Options are "", "SSL", or "TLS".  Blank/Default is no encryption. See documentation for your mail relay service.
define("SMTP_USER", "foo"); // Use "" to skip authentication. See documentation for your mail relay service.
define("SMTP_PASSWORD", "bar"); // Use "" to skip authentication. See documentation for your mail relay service.
define("PREF_TTL_SESNS_LMT", 10); // Input data verification limit for preferred total number of sessions
define("PREF_DLY_SESNS_LMT", 5); // Input data verification limit for preferred daily limit of sessions
define("AVAILABILITY_ROWS", 8); // Number of rows of availability records to render
define("MAX_BIO_LEN", 1000); // Maximum length (in characters) permitted for participant biographies
define("MY_AVAIL_KIDS", FALSE); // Enables questions regarding no. of kids in Fasttrack on "My Availability"
define("ENABLE_SHARE_EMAIL_QUESTION", TRUE); // Enables question regarding sharing participant email address
define("ENABLE_USE_PHOTO_QUESTION", TRUE); // Enables question regarding using participant photo for promotional purposes
define("ENABLE_BESTWAY_QUESTION", FALSE); // Enables question regarding best way to contact participant
define("BILINGUAL", TRUE); // Triggers extra fields in Session and "My General Interests"
define("SECOND_LANG", "FRENCH");
define("SECOND_TITLE_CAPTION", "Titre en fran&ccedil;ais");
define("SECOND_DESCRIPTION_CAPTION", "Description en fran&ccedil;ais");
define("SECOND_BIOGRAPHY_CAPTION", "Biographie en fran&ccedil;ais");
define("SHOW_BRAINSTORM_LOGIN_HINT", FALSE);
define("USER_ID_PROMPT", "User ID"); // What to label User ID / Badge ID
define("RESET_PASSWORD_SELF", TRUE); // User can reset own password.  Requires email and reCAPTCHA integration.
define("ROOT_URL", "https://zambia.server.com/"); // URL to reach this zambia server. Required to generate and email password reset link. Include trailing /
define("PASSWORD_RESET_LINK_TIMEOUT", "PT01H"); // How long until password reset link expires See https://www.php.net/manual/en/dateinterval.construct.php for format.
define("PASSWORD_RESET_LINK_TIMEOUT_DISPLAY", "1 hour"); // Text description of PASSWORD_RESET_LINK_TIMEOUT
// Self service reset of password via email link requires use of reCAPTCHA to prevent bad actors from using page to send email
define("RECAPTCHA_SITE_KEY", ""); // Register the domain you use for Zambia with Google reCAPTCHA to acquire site key ...
define("RECAPTCHA_SERVER_KEY", ""); // ... and server key
define("PASSWORD_RESET_FROM_EMAIL", "admin@somewhere.net"); // From address to be used for password reset emails
define("ENCRYPT_KEY", "jowigQuT9ruM287LEG9M4GuCfRcjpPr9ABA5ZhSj5QFYUv5VV3HLLVSuinBjrcCg"); // used for encrypting hidden inputs; I suggest finding a random password generator and putting in a 64 character alphanumeric only password
define("DEFAULT_USER_PASSWORD", "changeme"); // Note, Zambia will never directly set a user's password to this default nor will it
// create users with a default password, but some external integrations to create users do so.  In that case, Zambia can
// identify users with this default password and prompt them to change it as well as report to staff. If your installation
// does not use a default password, leave this empty ''.
define("TRACK_TAG_USAGE", "TAG_OVER_TRACK"); // Describe how Track and Tag fields are used -- one of 4 following values:
// "TAG_ONLY" : Track field is not used and will be hidden where possible.
//      NOTE: TAG_ONLY requires that trackid 1 exist in Tracks, be the hidden track for TAG_ONLY and have selfselect be set to 1 (1, "Tag Based", 10, 1)
// "TAG_OVER_TRACK" : Both fields are used, but primary sorting and filtering is by Tag.
// "TRACK_OVER_TAG" : Both fields are used, but primary sorting and filtering is by Track.
// "TRACK_ONLY" : Tag field is not used and will be hidden where possible.
define("REQUIRE_CONSENT", TRUE); // Require Data Collection Consent from all users
define("USE_REG_SYSTEM", FALSE);
// True -> Zambia users loaded from reg system into CongoDump; staff users cannot edit them
// False -> Zambia users created and edited by staff users in Zambia
define("USE_PRONOUNS", FALSE); // let participants specify their pronouns
define("REG_PART_PREFIX", "");
// only needed for USE_REG_SYSTEM = FALSE; prefix portion of userid/badgeid before counter; can be empty string for no prefix
define("CON_THEME", "");
// if con-specific theming should be applied, you can reference a theme css here.
// for example: define("CON_THEME", "themes/reallybigcon/main.css");
define("CON_HEADER_IMG", "");
// to improve the con branding, you can define a con-specific header image that will take the place of the 
// Zambia illustrated "Z" image, like so: define("CON_HEADER_IMG", "themes/reallybigcon/header.jpg");
define("CON_HEADER_IMG_ALT", "");
// to improve the con branding, you can specify the alt-text of the header image. For example:
// define("CON_HEADER_IMG_ALT", "Really Big Con Logo);
?>
