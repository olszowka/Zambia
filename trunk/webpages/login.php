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
<form name="loginform" method="post" action="doLogin.php">
	<table class="login" style="text-align:center">
		<tr>
			 <td>Badge ID:</td>
			 <td><input type="text" name="badgeid" maxlength="40" /></td>
		</tr>
		<tr>
			 <td>Password:</td>
			 <td><input type="password" name="passwd" maxlength="50" /></td>
		</tr>
		<tr>
			 <td colspan="2" style="text-align:center"><input type="submit" name="submit" value="Login" /></td>
		</tr>
	</table>
	<p id="brainstorm-login-hint"><span style="font-weight:bold">Brainstorm</span> users: if you want to submit ideas for panels, please enter "brainstorm" for your Badge ID
	and use the last name of the author of the Foundation series as your password (in all lowercase). </p>
</form>
<?php participant_footer(); ?>
