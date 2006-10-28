<?php
// function $OK=validate_email($email)
// Checks if values in $email array are acceptible
function validate_email($email) {
    global $message;
    $message="";
    $OK=true;
    if (strlen($email['subject'])<6) {
        $message.="Please enter a more substantive subject.<BR>\"";
        $OK=false;
        }
    if (strlen($email['body'])<16) {
        $message.="Please enter a more substantive body.<BR>\"";
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
    $email['subject']="";
    $email['body']="";
    return($email);
    }

// function render_send_email($email)
// $email is an array with all values for the send email form:
//   sendto, sendfrom, subject, body
// This function will render the entire page.
// This page will next go to the StaffSendEmailCompose_POST page
function render_send_email($email) {
$title="Send Email to Participants";
require_once('StaffHeader.php');
require_once('StaffFooter.php');
staff_header($title);

echo "<H3>Step 1 -- Compose Email</H3>\n";
echo "<P>Sorry.  This is a non-functional stubb.</P>\n";
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
echo "<TR><TD><LABEL for=\"subject\">Subject: </LABEL></TD>\n";
echo "    <TD><INPUT name=\"subject\" type=\"text\" size=\"40\" value=\"";
    echo htmlspecialchars($email['subject'],ENT_NOQUOTES)."\">\n";
echo "    </TD></TR></TABLE><BR>\n";
echo "<TEXTAREA name=\"body\" cols=\"80\" rows=\"25\">";
    echo htmlspecialchars($email['body'],ENT_NOQUOTES)."</TEXTAREA><BR>\n";
echo "<BUTTON class=\"ib\" type=\"reset\" value=\"reset\">Reset</BUTTON>\n";
echo "<BUTTON class=\"ib\" type=\"submit\" value=\"See it\">Save</BUTTON>\n";
echo "</FORM><BR>\n";
echo "<P>Available substitutions:</P>\n";
echo "<TABLE><TR><TD>\$BADGEID\$</TD><TD>\$EMAILADDR\$</TD></TR>\n";
echo "<TR><TD>\$FIRSTNAME\$</TD><TD>\$PUBNAME\$</TD></TR>\n";
echo "<TR><TD>\$LASTNAME\$</TD><TD>\$BADGENAME\$</TD></TR></TABLE>\n";
staff_footer();
} ?>
