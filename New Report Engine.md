# New Report Engine

## Primary Benefit

This change moves the reports from the db to distinct files.  This change allows report
customization to be tracked using git while other db changes can be easily taken from master.

## Quick Overview

* Move reports from db to file for each report in reports/ directory
* Old (CSV output) reports which were not specified in the db were modified to new
format and moved manually
* Add mechanism to rebuild menus when reports change
* New permission role: "senior staff"
* Sending email moved to "senior staff" role
* Reports that output CSV are included in same mechanism
* Adding Data Tables support to a report greatly simplified

## Migration Instructions

### No Report Customization

Follow these instructions if you haven't customized any reports

1) Pull from current code from Master.
1) Apply db script `45_drop_report_tables.sql` to remove old report tables.
1) Apply db script `46_new_report_permissions.sql` to create new permission-related db entries.
1) Log into Zambia with user who has "admin" role.
1) Select Admin -> Build Report Menus and confirm.
1) In db, update table UserHasPermissionRole to revoke Admin/1 from all users who will not be
editing report code
1) In db, update table UserHasPermissionRole to add Senior Staff/12 to all users who
should be allowed to send email from Zambia

### Report Customization

Follow these instructions if you have customized reports in the report tables

1) In the table ReportTypes, ensure every report with oldmechanism=0 has a unique file name
ending with .php
1) Pull from current code from Master.
1) Switch to a branch specific to your con.
1) Log into Zambia with user who has "admin" role.
1) In same browser, browse to generateReportFiles.php.
1) If there are any reports you deleted as part of customization, you will have to
delete that file from the reports/ directory.
1) Commit the changes to the reports/ directory.
1) In Zambia, select Admin -> Build Report Menus and confirm.
1) Apply db script `45_drop_report_tables.sql` to remove old report tables.
1) Apply db script `46_new_report_permissions.sql` to create new permission-related db entries.
1) In db, update table UserHasPermissionRole to revoke Admin/1 from all users who will not be
editing report code
1) In db, update table UserHasPermissionRole to add Senior Staff/12 to all users who
should be allowed to send email from Zambia

## Report Customization After Migration

* Each report is in a separate file in reports/ directory.
* Each file specifies the $report associative array.  All data necessary to specify
the report is in this array.
* If you change just the queries or output format of a report, there is nothing additional to do.
* If you add or delete a report, rerun Build Report Menus to change the menus.
* If you change the name, description, or categories of a report, rerun Build Report Menus to change the menus.
* If you want a report not to appear under any categories, set $report\['categories'] = array();


