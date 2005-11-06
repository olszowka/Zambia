<?php 
require_once('db_functions.php');
require_once('ParticipantHeader.php');
require_once('ParticipantFooter.php');

$title="Login";

participant_header($title);

?>

<H1> Login </H1>
<?php
   if (isset($message)) {
      echo "<P class=\"errmsg\">".$message."</P>\n";
      }
   ?>
<FORM name="loginform" method="POST" action="doLogin.php">
  <table class="login">
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
