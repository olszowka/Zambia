<?php
  global $participant,$message_error,$message2,$congoinfo;
  $title="Staff Top Level View";
  require_once('db_functions.php');
  require_once('StaffHeader.php');
  require_once('StaffFooter.php');
  require_once('StaffCommonCode.php');
  staff_header($title);
?>

<P>This is the main staff page. </p>
<p> Please note the tabs above.   One of them will take you to your participant view.  Another will allow you to manage Sessions.  Note that Sessions is the generic term we are using for all Events, Films, Panels, Anime, Video, etc. 
<p> As Zambia continues to develop, we'll add more text here. 


<?php staff_footer(); ?>
