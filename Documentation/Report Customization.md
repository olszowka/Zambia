# Report Customization

The process for report customization was changed in Release 2.1 in August, 2026.

## Previous mechanism

Prior to this change, all files defining reports were located in the `webpages/reports/` directory and there was a
single file for each report.  If a con wanted to customize a report, it had to modify the report file and then
subsequently manage tracking changes to the original report definition file.  

## New mechanism

### Individual Convention Overrides

The new mechanism places all the core report definition files in the `webpages/reports/` directory, but there
is a new directory called `webpages/reportConOverrides` which will be left empty in the core repo.
Individual cons should place their customizations in this directory.  Furthermore, a report definition can
be split between the two locations.  The definition file in the override location can replace some or all
of the core report definition.

For example, the override file can just add a new value to the $report['categories'] array which will have the
effect of creating a new category and putting the report in it.  However, if the core report query is modified
because of a core schema change, the customization will work as is with no changes.

### System Overrides

The new mechanism provides for the core code to specify a set of report overrides which cons have the option
to apply.  These sets will be located in subdirectories of `webpages/reportSystemOverrides` and will be
triggered by the `REPORT_SYSTEM_OVERRIDE_SUBDIR` configuration constant.

## Examples and Suggestions

### Hiding a report

It is common for a convention to want to hide a report it doesn't use to simplify the report menus.
To implement this, just add the following code in a file of the report name in the `webpages/reportsConOverrides/` directory.
```
<?php
$report['categories'] = array();
```
### Putting a report in a new category

Some conventions make a new category to hold their commonly used reports to make finding them convenient.
To implement this, just add the following code in a file of the report name in the `webpages/reportsConOverrides/`
directory.  In this case, "Boskone Central" is the new report category to appear in the menus, and "220"
control the location of the report within that category.
```
<?php
$report['categories']['Boskone Central'] = 220;
```

### Modifying a report name or description

A report name and/or description can be modified to better describe its use for a particular convention.
```
<?php
$report['name'] = 'Participant Program Types';
$report['description'] = 'What Program Types is a participant willing to take?';
```

### Modifying a magic number used by a report query

When a report needs to filter data based on a value configured for a particular con, for example a room,
the entire query underlying the report must be overridden as is shown by the example below.
Unfortunately, such an override is not safe from edits to the core report implementation, unlike the
examples above.

```
<?php
$report['queries']['schedule'] =<<<'EOD'
SELECT
        DATE_FORMAT(S.duration,'%i') as durationmin, DATE_FORMAT(S.duration,'%k') as durationhrs,
        R.roomid, R.roomname, S.sessionid, S.title,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p') AS starttime
    FROM
             Sessions S
        JOIN Schedule SCH USING (sessionid)
        JOIN Rooms R USING (roomid)
    WHERE
            R.roomid = 7 /* Dragon's Lair */
        AND S.pubstatusid = 2 /* public */
    ORDER BY
        SCH.starttime, R.roomname;
EOD;
```

## Migration

If a convention has customized reports in the original `reports` directory, those customizations will still work
as before.  Still, moving those modified files to the `reportsConOverrides` directory and restoring the core file
to the `reports` directory will simplify change tracking while giving the same results.  If the customization is
to name, description, and/or category as shown above, putting only that customization in the file in
`reportsConOverrides` will allow the convention to take advantage of any edits to the implementation of the
report in the core.
