<?php
  $title="Staff - View Programme";
  require_once('db_functions.php');
  require_once('StaffHeader.php');
  require_once('StaffFooter.php');
  require_once('StaffCommonCode.php');
  staff_header($title, 'javascript_for_ops_grid');

  $id = $_GET["id"];
?>

<hr>
<?php if(may_I('create_participant')) { ?>
<div id="gridview" style="font-size: 10pt;">
<span id="rsperror" style="color:red"></span>

<div>  Title/Participant: <input type="text" id="name_cd" onkeydown="doSearch(arguments[0]||event)" /> Room: <input type="text" id="room_cd" onkeydown="doSearch(arguments[0]||event)" />
<button onclick="gridReload()" id="submitButton" style="margin-left:30px;">Search</button> </div>


<br />
<table id="programmegrid" class="scroll" cellpadding="0" cellspacing="0"></table>
<div id="pager" class="scroll" style="text-align:center;"></div>

<div id="progtabs" >
    <ul>
        <li><a href="programmefeaturestab.php"><span>Features</span></a></li>
        <li><a href="programmeservicestab.php"><span>Services</span></a></li>
        <li><a href="programmehistorytab.php"><span>Edit History</span></a></li>
    </ul>
</div>

</div>

<?php } ?>

<?php staff_footer(); ?>
