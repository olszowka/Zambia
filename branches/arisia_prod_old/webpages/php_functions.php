<?php
function set_session_timeout() {
    // BASESESSIONDIR:  default session directory from db_name.php
    // path for cookies
    $cookie_path = "/";

    // timeout value for the cookie
    $cookie_timeout = 60 * 60; // in seconds

    // timeout value for the garbage collector
    //   we add 300 seconds, just in case the user's computer clock
    //   was synchronized meanwhile; 600 secs (10 minutes) should be
    //   enough - just to ensure there is session data until the
    //   cookie expires
    $garbage_timeout = $cookie_timeout + 600; // in seconds
 
    // set the PHP session id (PHPSESSID) cookie to a custom value
    session_set_cookie_params($cookie_timeout, $cookie_path);
    //error_log("Zambia--set_session_timeout: ".print_r(session_get_cookie_params(),true));
    // set the garbage collector - who will clean the session files -
    //   to our custom timeout
    ini_set('session.gc_maxlifetime', $garbage_timeout);

    // we need a distinct directory for the session files,
    //   otherwise another garbage collector with a lower gc_maxlifetime
    //   will clean our files aswell - but in an own directory, we only
    //   clean sessions with our "own" garbage collector (which has a
    //   custom timeout/maxlifetime set each time one of our scripts is
    //   executed)
    strstr(strtoupper(substr($_SERVER["OS"], 0, 3)), "WIN") ? 
	$sep = "\\" : $sep = "/";
    $oldsessdir = ini_get('session.save_path');
    $newsessdir = BASESESSIONDIR.$sep.'my_sessions';
    if ($oldsessdir != $newsessdir) {
        if (!is_dir($newsessdir)) { mkdir($newsessdir, 0700); }
        ini_set('session.save_path', $newsessdir);
        }
    error_log("Zambia--set_session_timeout: session.save_path: ".ini_get('session.save_path'));
   
    // now we're ready to start the session
    }
?>
