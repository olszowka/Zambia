<?php
//	Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
function brainstorm_header($title) {
    global $header_section;
    $header_section = HEADER_BRAINSTORM;
    require_once ("javascript_functions.php");
?>
<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Zambia &ndash; <?php echo $title ?></title>
    <link rel="stylesheet" href="css/Common.css" type="text/css">
    <link rel="stylesheet" href="css/zambia.css" type="text/css">
    <link rel="stylesheet" href="css/BrainstormSection.css" type="text/css">
    <meta name="keywords" content="Questionnaire">
</head>
<body>
    <div class="brainstorm-header-container">
        <h1 class="head">Zambia&ndash;The <?php echo CON_NAME; ?> Scheduling Tool</h1>
    </div>
    <hr class="brainstorm-header-hr">
<?php if (isset($_SESSION['badgeid'])) { ?>
  <table class="tabhead">
    <col width=10%><col width=10%><col width=10%><col width=10%><col width=10%>
    <col width=10%><col width=10%><col width=10%><col width=10%><col width=10%>
    <tr class="tabrows">
      <td class="tabblocks border0020" colspan=2><?php maketab("Welcome",1,"BrainstormWelcome.php"); ?></td>
      <td class="tabblocks border0020" colspan=2>
           <?php maketab("Suggest a Session",may_I('BrainstormSubmit'),"BrainstormCreateSession.php"); ?></td>
      <td class="tabblocks border0020" colspan=2><?php maketab("Search Sessions",1,"BrainstormSearchSession.php"); ?></td>

      <td class="tabblocks border0020" colspan=2>
         <?php if(may_I('Participant')) { 
                  maketab("Participants View",may_I('Participant'),"welcome.php"); 
               }?></td>
      <td class="tabblocks border0020" colspan=2>
         <?php if(may_I('Staff')) { 
                   maketab("Staff View",may_I('Staff'),"StaffPage.php");
               }?></td>
    </tr>
    <tr class="tabrows">
      <td class="tabblocks border0020" colspan=10>
         View sessions proposed to date:
    </tr>
    <tr class="tabrows">
      <td class="tabblocks border0020" colspan=2>
         <?php maketab("All Proposals",1,"BrainstormReportAll.php"); ?></td>
      <td class="tabblocks border0020" colspan=2>
         <?php maketab("New (Unseen)",1,"BrainstormReportUnseen.php"); ?></td>
      <td class="tabblocks border0020" colspan=2>
         <?php maketab("Reviewed",1,"BrainstormReportReviewed.php"); ?></td>
      <td class="tabblocks border0020" colspan=2>
         <?php maketab("Likely to Occur",1,"BrainstormReportLikely.php");?></td>
      <td class="tabblocks border0020" colspan=2>
         <?php maketab("Scheduled",1,"BrainstormReportScheduled.php"); ?></td>
    </tr>
  </table>
<table class="header">
  <tr>
    <td style="height:5px">
      </td>
    </tr>
  <tr>
    <td>
      <table width="100%">
        <tr>
          <td width="425">&nbsp;
            </td>
          <td class="Welcome">Welcome <?php echo $_SESSION['badgename']; ?>
            </td>
          <td><A class="logout" HREF="brainstormLogout.php">&nbsp;Logout&nbsp;</A>
            </td>
          <td width="25">&nbsp;
            </td>
          </tr>
        </table>
      </td>
    </tr>
<?php } ?>
  </table>
<h2 class="head"><?php echo $title ?></h2>
<?php } ?>
