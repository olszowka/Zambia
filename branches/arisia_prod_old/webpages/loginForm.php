      <div class="span3">
  			<form id="loginForm" name="loginform" class="form-horizontal" method="post" action="doLogin.php">
  				<fieldset id="loginBox">
<?php
if (isset($message)) {
  echo "<b class=\"alert alert-error pull-right\">".$message."</b>\n";
}
?>
  				  <div class="control-group">
    					<label class="control-label" for="badgeid">Badge ID:</label>
  				    <div class="controls">
  						  <input type="text" name="badgeid" id="badgeid" class="input-small" placeholder="Badge ID" title="Enter your badge ID"/>
      				</div>
    					<label class="control-label" for="passwd">Password:</label>
  				    <div class="controls">
    						<input type="password" id="passwd" name="passwd" class="input-small" placeholder="Password" title="Enter your password"/>
              </div>
  				  </div>
  				  <div class="control-group">
  				    <div class="controls">
  					   <input type="submit" value="Login" class="btn btn-primary pull-right" title="Click to log in">
              </div>
  				  </div>
  				</fieldset>
  			</form>
			</div>
