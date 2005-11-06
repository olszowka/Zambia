<?php 
require_once('db_functions.php');
require_once('ParticipantHeader.php');
require_once('ParticipantFooter.php');

$title="Login";

participant_header($title);

?>

<?php
   if (isset($message)) {
      echo "<P class=\"errmsg\">".$message."</P>\n";
      }
   ?>
<p> We emailed all participants on 10/24.  If you have not received email from us, we have the wrong address for you.  Please drop us a note at <a href="mailto: program@arisia.org">program@arisia.org</a>.  </p>
<FORM name="loginform" method="POST" action="doLogin.php">
  <table align="center" border="1" cellspacing="0" cellpadding="3">
    <tr>
      <td>Badge ID:</td>
      <td><input type="text" name="badgeid" maxlength="40"> </td>
    </tr>
    <tr>
      <td>Password:</td>
      <td><input type="password" name="passwd" maxlength="50"></td>
    </tr>
    <tr>
      <td colspan="2" align="center"> <input type="submit" name="submit" value="Login"> </td>
    </tr>
  </table>
</form>
<?php participant_footer(); ?>
