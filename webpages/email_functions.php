<?php
// function $email=get_email_from_post()
// reads post variable to populate email array
// returns email array or false if an error was encountered.
// A message describing the problem will be stored in global variable $message_error
function get_email_from_post() {
    global $message_error;
    $message_error="";
    $email['sendto']=$_POST['sendto'];
    $email['sendfrom']=$_POST['sendfrom'];
    $email['sendcc']=$_POST['sendcc'];
    $email['subject']=stripslashes($_POST['subject']);
    $email['body']=stripslashes($_POST['body']);
    return($email);
    }

// function $OK=validate_email($email)
// Checks if values in $email array are acceptible
function validate_email($email) {
    global $message;
    $message="";
    $OK=true;
    if (strlen($email['subject'])<6) {
        $message.="Please enter a more substantive subject.<BR>\n";
        $OK=false;
        }
    if (strlen($email['body'])<16) {
        $message.="Please enter a more substantive body.<BR>\n";
        $OK=false;
        }
    return($OK);
    }

// function $email=set_email_defaults()
// Sets values for $email array to be used as defaults for the email
// form when first entering page.
function set_email_defaults() {
    $email['sendto']=1; // default to all participants
    $email['sendfrom']=1; // default to Arisia Programming
    $email['sendcc']=1; // default to None
    $email['subject']="";
    $email['body']="";
    return($email);
    }

// function render_send_email($email,$message_warning)
// $email is an array with all values for the send email form:
//   sendto, sendfrom, sendcc, subject, body
// $message_warning will be displayed at the top, only if set
// This function will render the entire page.
// This page will next go to the StaffSendEmailCompose_POST page
function render_send_email($email,$message_warning) {
    $title="Send Email to Participants";
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    staff_header($title);

    if (strlen($message_warning)>0) {
        echo "<P class=\"message_warning\">$message_warning</P>\n";
    }
    echo "<H3>Step 1 -- Compose Email</H3>\n";
    echo "<FORM name=\"emailform\" method=POST action=\"StaffSendEmailCompose_POST.php\">\n";
    echo "<TABLE><TR>";
    echo "    <TD><LABEL for=\"sendto\">To: </LABEL></TD>\n";
    echo "    <TD><SELECT name=\"sendto\">\n";
    populate_select_from_table("EmailTo", $email['sendto'], "", false);
    echo "    </SELECT></TD></TR>";
    echo "<TR><TD><LABEL for=\"sendfrom\">From: </LABEL></TD>\n";
    echo "    <TD><SELECT name=\"sendfrom\">\n";
    populate_select_from_table("EmailFrom", $email['sendfrom'], "", false);
    echo "    </SELECT></TD></TR>";
    echo "<TR><TD><LABEL for=\"sendcc\">CC: </LABEL></TD>\n";
    echo "    <TD><SELECT name=\"sendcc\">\n";
    populate_select_from_table("EmailCC", $email['sendcc'], "", false);
    echo "    </SELECT></TD></TR>";
    echo "<TR><TD><LABEL for=\"subject\">Subject: </LABEL></TD>\n";
    echo "    <TD><INPUT name=\"subject\" type=\"text\" size=\"40\" value=\"";
        echo htmlspecialchars($email['subject'],ENT_NOQUOTES)."\">\n";
    echo "    </TD></TR></TABLE><BR>\n";
    echo "<TEXTAREA name=\"body\" cols=\"80\" rows=\"25\">";
        echo htmlspecialchars($email['body'],ENT_NOQUOTES)."</TEXTAREA><BR>\n";
    echo "<BUTTON class=\"ib\" type=\"reset\" value=\"reset\">Reset</BUTTON>\n";
    echo "<BUTTON class=\"ib\" type=\"submit\" value=\"seeit\">See it</BUTTON>\n";
    echo "</FORM><BR>\n";
    echo "<P>Available substitutions:</P>\n";
    echo "<TABLE><TR><TD>\$BADGEID\$</TD><TD>\$EMAILADDR\$</TD></TR>\n";
    echo "<TR><TD>\$FIRSTNAME\$</TD><TD>\$PUBNAME\$</TD></TR>\n";
    echo "<TR><TD>\$LASTNAME\$</TD><TD>\$BADGENAME\$</TD></TR></TABLE>\n";
    staff_footer();
    }

// function render_verify_email($email,$emailverify)
// $email is an array with all values for the send email form:
//   sendto, sendfrom, subject, body
// $emailverify is an array with all values for the verify form:
//   recipient_list, emailfrom, body
// This function will render the entire page.
// This page will next go to the StaffSendEmailResults_POST page
function render_verify_email($email,$email_verify,$message_warning) {
    $title="Send Email";
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    staff_header($title);

    if (strlen($message_warning)>0) {
        echo "<P class=\"message_warning\">$message_warning</P>\n";
    }
    echo "<H3>Step 2 -- Verify </H3>\n";
    echo "<FORM name=\"emailverifyform\" method=POST action=\"StaffSendEmailCompose.php\">\n";
    echo "<P>Recipient List:<BR>\n";
    echo "<TEXTAREA readonly rows=\"4\" cols=\"70\">";
    echo $email_verify['recipient_list']."</TEXTAREA></P>\n";
    echo "<P>Rendering of message body to first recipient:<BR>\n";
    echo "<TEXTAREA readonly rows=\"4\" cols=\"70\">";
    echo $email_verify['body']."</TEXTAREA></P>\n";
    echo "<INPUT type=\"hidden\" name=\"sendto\" value=\"".$email['sendto']."\">\n";
    echo "<INPUT type=\"hidden\" name=\"sendfrom\" value=\"".$email['sendfrom']."\">\n";
    echo "<INPUT type=\"hidden\" name=\"sendcc\" value=\"".$email['sendcc']."\">\n";
    echo "<INPUT type=\"hidden\" name=\"subject\" value=\"".htmlspecialchars($email['subject'])."\">\n";
    echo "<INPUT type=\"hidden\" name=\"body\" value=\"".htmlspecialchars($email['body'])."\">\n";
    echo "<BUTTON class=\"ib\" type=\"submit\" name=\"navigate\" value=\"goback\">Go Back</BUTTON>\n";
    echo "<BUTTON class=\"ib\" type=\"submit\" name=\"navigate\" value=\"send\">Send</BUTTON>\n";
    echo "</FORM><BR>\n";
    staff_footer();
    }

function render_send_email_engine($email,$message_warning) {
    $title="Pretend to actually send email.";
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    staff_header($title);

    if (strlen($message_warning)>0) {
        echo "<P class=\"message_warning\">$message_warning</P>\n";
    }
    echo "<H3>Step 3 -- Actually Send Email </H3>\n";
    staff_footer();
    }

?>
