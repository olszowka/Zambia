<?php
require_once('VendorCommonCode.php');

// Localisms
$_SESSION['return_to_page']='VendorWelcome.php';
$title="Submit Vendor Application";

// Description/additional info should vary if they are logged in or not.
if (may_I("Vendor")) {
  $mybadgeid=$_SESSION['badgeid'];
  $badgeid_string='';
  $description="<P>Please click anywhere on your names to update your information, and click on the \"Apply\" tab above, to see your current application state.</P>\n";
} else {
  $mybadgeid=100;
  $badgeid_string='concat("<A HREF=\"login.php?login=",badgeid,"\">",badgeid,"</A>") AS "Login Number",';
  $description="<P>If you already have a profile on our system, find your name, and click on your Login Number.</P>\n";
  $additionalinfo="<P>If you have forgotten your password, please get in touch with the Vendor Liaison at <A HREF=mailto:".VENDOR_EMAIL.">".VENDOR_EMAIL."</A> so you might be helped.</P>\n";
}
// Get the list of all the vendors in our system
// NEED to get the APPLY/Pending,et al back in the list.
//    if ((interested is NULL OR interested="0" OR interestedname="Not Applied"),if (badgeid="$mybadgeid","<A HREF=\"VendorApply.php\">Apply</A>","Not Applied"),interestedname))) AS "Status"
$query= <<<EOD
SELECT
    $badgeid_string
    if (badgeid="$mybadgeid",concat("<A HREF=\"VendorSubmitVendor.php\">",pubsname,"</A>"),pubsname) AS "Business Name",
    if (badgeid="$mybadgeid",concat("<A HREF=\"VendorSubmitVendor.php\">",firstname,"</A>"),"") AS "Contact First Name",
    if (badgeid="$mybadgeid",concat("<A HREF=\"VendorSubmitVendor.php\">",lastname,"</A>"),"") AS "Contact Last Name"
  FROM
      CongoDump
    JOIN Participants USING (badgeid)
    JOIN UserHasPermissionRole USING (badgeid)
    JOIN PermissionRoles USING (permroleid)
  WHERE
    permrolename='Vendor'
  ORDER BY
    pubsname
EOD;

list($rows,$header_array,$vendor_array)=queryreport($query,$link,$title,$description,0);
topofpagereport($title,$description,$additionalinfo);
echo renderhtmlreport(1,$rows,$header_array,$vendor_array);
correct_footer();
?>