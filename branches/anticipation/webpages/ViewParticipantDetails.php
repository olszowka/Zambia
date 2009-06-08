<?php
  $title="Staff - View Participant(s) Information";
  require_once('db_functions.php');
  require_once('StaffHeader.php');
  require_once('StaffFooter.php');
  require_once('StaffCommonCode.php');
  staff_header($title, 'javascript_for_view_participant');
?>

<p>
On this page you will find the online tools for viewing Participant information.</p>
<hr>
<?php if(may_I('create_participant')) { ?>
<div id="particpantview" style="font-size: 10pt;">
<span id="rsperror" style="color:red"></span>

<div>  Name: <input type="text" id="name_cd" onkeydown="doSearch(arguments[0]||event)" />  
<button onclick="gridReload()" id="submitButton" style="margin-left:30px;">Search</button> </div> 

<br /> 
<table id="particpantgrid" class="scroll" cellpadding="0" cellspacing="0"></table> 
<div id="pager" class="scroll" style="text-align:center;"></div> 

<div id="particpanttabs" >
    <ul>
        <li><a href="particpantoverview.php"><span>Overview</span></a></li>
        <li><a href="participantbiotab.php"><span>Bio</span></a></li>
        <li><a href="participantcontacttab.php"><span>Contact Info</span></a></li>
    </ul>
    <div id="Overview">
    </div>
    <div id="Bio">
    </div>
</div>	
</div>

<?php } ?>

<?php staff_footer(); ?>