<?php
// Copyright (c) 2011-2017 Peter Olszowka. All rights reserved. See copyright document for more details.
    global $title;
    $title = "GRIDS Reports";
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    staff_header($title);
    $_SESSION['return_to_page'] = "REPORT_LINK";
?>
<dl>
    <dt><a href="eventgridstaticreport.php">Published Event Grid</a></dt>
    <dd>Display published event schedule with rooms on horizontal axis and
        time on vertical. This excludes any item marked "Do Not Print" or
        "Staff Only".
    </dd>

    <dt><a href="eventgridfullstaticreport.php">Unabridged Event Grid</a></dt>
    <dd>Display event schedule with rooms on horizontal axis and time on
        vertical. This includes all items regardless of publication
        status.
    </dd>

    <dt><a href="programgridstaticreport.php">Published Programming Grid</a></dt>
    <dd>Display published schedule of programming rooms with rooms on
        horizontal axis and time on vertical. This excludes any item marked
        "Do Not Print" or "Staff Only".
    </dd>

    <dt><a href="fasttrackgridstaticreport.php">Published Fast Track Grid</a></dt>
    <dd>Display published fast track schedule with rooms on horizontal
        axis and time on vertical. This excludes any item marked "Do Not
        Print" or "Staff Only".
    </dd>

    <dt><a href="staffpubgridstaticreport.php">Published Grid</a></dt>
    <dd>Display published schedule with rooms on horizontal axis and
        time on vertical. This excludes any item marked "Do Not Print" or
        "Staff Only".
    </dd>

    <dt><a href="staffallgridstaticreport.php">Everything Grid</a></dt>
    <dd>Display the entire schedule with rooms on horizontal axis and
        time on vertical. This includes all items regardless of publication
        status.
    </dd>

    <dt><a href="staffonlygridstaticreport.php">Staff-Only Grid</a></dt>
    <dd>Display the only the items that are Staff-Only or Do-Not-Publish
        with rooms on horizontal axis and time on vertical.
    </dd>

</dl>
<?php staff_footer(); ?>
