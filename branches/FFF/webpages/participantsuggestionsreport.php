<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="participantsuggestionsreport.php";
    $title="Participant Suggestions";
    $description="<P>What is did each participant suggest?</P>\n";
    $additionalinfo="<P>This form originally had a index of \"DELETEME\" should it be removed?</P>";
    $indicies="PROGWANTS=1";

    $query = <<<EOD
SELECT
    P.badgeid,
    P.pubsname,
    paneltopics,
    otherideas,
    suggestedguests 
  FROM
      ParticipantSuggestions as PS, 
      Participants as P
  WHERE
    P.badgeid=PS.badgeid
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$class_array);
