# Zambia Version 2.3 Release Notes

## Changes since release 2.2

### Bug Fixes
* Edit Session no longer logs "Undefined array key" PHP warnings for `panelistlink` and `captionlink`, and now correctly
  displays existing Panelist Link and Caption Link values. Previously, those fields always appeared blank, so saving the
  session with `PANELIST_LINK` or `CAPTION_LINK` enabled would erase any stored links.
* Create/Edit Session now checks the Duration field and shows a clear message for invalid input instead of failing
  with a database error (e.g. "Incorrect time value: ':30'"). When durations are entered as h:mm, a value with the
  hour left blank such as ":30" is now accepted as 0:30.
* Fix small issues not visible to the user with `EditCustomText.php`.
* Fix mangled variable name in `email_functions.php`.

### New Features
* On Panel Interests page, add session portion moved to bottom of page
* On Panel Interests page, the ranking-scale instructions can now be customized with Custom Text (page
  "Panel Interests", tag `ranking_scale`)
 
### Invisible code cleanup
* Remove redundant calls to fetch custom text and change query to prepared

## Application Notes

1) 

### db_name.php

### Schema Patches

#### 79_panel_interests_custom_text.sql

Just apply this patch as normal. It adds an inactive Custom Text entry (page "Panel Interests", tag
`ranking_scale`) holding the default ranking-scale instructions shown at the top of the Panel Interests page. While
it is inactive (or missing), the page shows the built-in default text. To customize the instructions, edit the entry
and make it active. 
