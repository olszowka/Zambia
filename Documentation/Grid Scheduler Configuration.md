# Grid Scheduler Configuration

The _Grid Scheduler_ page allows staff users to edit the schedule simply by dragging and dropping sessions (schedule items) onto or off of a grid representation of the schedule.  There are various configuration constants which affect the rendering of this grid and its behavior.

While this page allows sessions to be scheduled at any time on a 5 minute boundary, typically conventions
use a regular periodic schedule for sessions and this page makes that convenient.  Before scheduling has
begun or where there is availability in the schedule, a grid showing the available time slots will be
displayed.  Much of the configuration regards the timing of those available slots.

## Related Constants

The following constants are not specific to the grid scheduler, but still affect it:
* `CON_NUM_DAYS`
: How many days long the convention is.
 
* `CON_START_DATIM`
: When the convention starts (Should be as `YYYY-MM-DD HH:MM:SS`; HH:MM:SS must be "00:00:00", i.e. midnight at the beginning of the first day.)

## Grid Scheduler Constants

### List of constants

1. `FIRST_DAY_START_TIME`
1. `OTHER_DAY_STOP_TIME`
1. `OTHER_DAY_START_TIME`
1. `LAST_DAY_STOP_TIME`
1. `STANDARD_BLOCK_LENGTH`
1. `GRID_DAY_SEGMENTS`
1. `GRID_SPACING_MINUTES`
1. `GRID_SESSION_BOX_FIELDS`

#### Simple repeating grid
The first five are used to describe a schedule which repeats regularly throughout the day and is the
same every day of the convention except that it begins at a special time on the first day and ends at
a special time on the last day.

#### Gridlines distinct from standard block length
By default, gridlines are spaced at the standard block length. Instead, one may define a value for
the constant `GRID_SPACING_MINUTES`.  Because the snap need not match this grid, any integer >= 5 and
<= 1440 may be used, but the recommendation is no less than 15 and a multiple of 5.

#### Grid with irregularities
Number 6 (`GRID_DAY_SEGMENTS`) may be used instead of the first four above (leave them undefined) to
specify a less regular schedule.  The value should be an array of arrays where the inner arrays
specify the "day", "start", and "end" of each segment.  "day" is an integer with 1 representing the first
day of the con.  "start" and "end" are hh:mm (24 hours time) for each segment.  The segment may end
after midnight by using hours greater than 23.  If more than one segment is specified for a day, the
period between is a gap in the grid which may be used for a meal or other break.  The gap still appears
on the grid's time axis, but has no grid lines within it; the pattern of grid lines restarts at the
start of the next segment.

For example:
```php
define("GRID_DAY_SEGMENTS", [
     ["day" => 1, "start" => "17:30", "end" => "25:00"],
     ["day" => 2, "start" => "9:00", "end" => "12:00"],
     ["day" => 2, "start" => "13:00", "end" => "25:00"],
     ["day" => 3, "start" => "8:30", "end" => "16:00"],
 ]);
```
means the grid runs from 5:30p - 1:00a on the first day; 9:00a - 12:00p with a gap, then 1:00p - 1:00a
on the second day; and 8:30a - 4:00p on the third day.  If this schedule is used with 90 minute grid
spacing, then the 60 minute gap on the 2nd is an interruption to the regular 90 minute
grid pattern.

#### Expansion of grid display to reflect current schedule
Whichever way the day times are configured, the grid is widened as needed to show every scheduled
session at its actual time.  A session which falls outside the configured times (for example, one
scheduled through the _Maintain Room Schedule_ page, or before the day times were changed) stretches
the nearest segment to cover it, rounded out to the half hour.  All rooms which can appear on the
grid are considered, not just the ones currently displayed, so the times shown don't change as rooms
are added to or removed from the display.  Consequently, even an empty room may show times earlier
or later than configured.

#### Customization of contents of session boxes
The grid scheduler displays both scheduled and unscheduled sessions in boxes with some information
regarding them. The default layout is used when the constant `GRID_SESSION_BOX_FIELDS` is undefined
and shows the following 3 lines of text: _title_; _session id_ and _type_; then _tags_, or _track_ if `TRACK_TAG_USAGE` is "TRACK_ONLY" or "TRACK_OVER_TAG".

![Example of session box rendering](Grid%20Scheduler%20Configuration%20image1.png)

The box fits 3 lines of text if it is unscheduled or if it is at
least 60 minutes long and scheduled. Because the height of the scheduled session boxes is proportional
to the duration, there is room for an additional line of text for each additional 18 minutes of duration.
To customize the display, set the constant `GRID_SESSION_BOX_FIELDS` to an array of arrays of strings.
For example,
```php
define("GRID_SESSION_BOX_FIELDS", [["title"], ["sessionid", "type"], ["tags"]]);
```
will set the 1st line to the title, the 2nd line to be the session id and then the type, and the 3rd
line to be the tags.  More or fewer lines may be specified. The fields you may choose are: `title,
sessionid, type, track, tags, division,` and `duration.` Regardless of configuration, the upper left
corner of the session box will always display the info icon which, when clicked, triggers the "Info" tab
to display a large amount of information regarding the session.

## Grid Simplification

When a room is mostly scheduled in step with the grid, the gap between a session's end and the next grid
line can make the grid harder to read. The _Grid Simplification_ option in the page's _Options_ menu (off
when the page loads) draws such a session as if it lasted until the next grid line. The part of the box past
the session's real end is shaded, and the schedule itself is unchanged. A session is drawn this way only when
all of the following hold:
* The room's `allow_grid_simplification` is 1 (Rooms table, editable in the _Configuration Table Editor_).
* The session's type's `allow_grid_simplification` is 1 (Types table, likewise).
* The session's duration is exactly `DEFAULT_DURATION`.
* `DEFAULT_DURATION` is no longer than the grid spacing. Otherwise the option isn't offered at all.
* The session starts on a grid line (not inside a gap between segments of a day).
* No other session in the room overlaps the session or the time after it, up to the next grid line.

Both `allow_grid_simplification` columns default to 0, so the option has no effect until an administrator turns it
on for some rooms and types (added by schema patch `77_grid_simplification.sql`).
