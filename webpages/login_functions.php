<?php

/*
 * is_email_login_supported:
 *  If the installation has specified the options for login via email address in the installation's, 
 *  db_name.php file then return true; else return false (which means that only login by badgeid
 *  is supported).
 */
function is_email_login_supported() {
    return defined('EMAIL_LOGIN_SUPPORTED') && EMAIL_LOGIN_SUPPORTED === true;
}

/*
 * get_user_id_prompt:
 *  Returns The name of the field that contains the user's login id (often "User ID" or "Badge ID").
 *  If the Zambia installation supports login with email address, we'll tweak the field name,
 *  but generally we should favour the text that the support team put in the db_name.php file.
 */
function get_user_id_prompt() {
    if (defined('LOGIN_PAGE_USER_ID_PROMPT') && LOGIN_PAGE_USER_ID_PROMPT !== '') {
        return LOGIN_PAGE_USER_ID_PROMPT;
    } else if (!is_email_login_supported()) {
        return USER_ID_PROMPT;
    } else {
        return USER_ID_PROMPT." or Email address";
    }
}
?>