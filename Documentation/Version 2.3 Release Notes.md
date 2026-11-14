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
 
### Invisible code cleanup
* 

## Application Notes

1) 

### db_name.php

### Schema Patches

#### 
