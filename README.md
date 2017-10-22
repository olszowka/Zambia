# Zambia
Convention scheduling tool originally developed for Arisia. Currently being generalized to handle other
conferences/conventions. Zambia tracks sessions (events, panels, and anything that needs to be scheduled),
participants, and rooms.

## Features
* Track sessions, rooms, and participants
* Comprehensive conflict checking
* Participants log in to enter availability, provide biography, etc.
* Reports for various departments such as technical services and hotel liaison
* Includes interface to KonOpas (https://github.com/eemeli/konopas), a free tool for publishing the schedule to mobile devices

## Requirements
* PHP 5.6 (Probably runs on 5.3 - 5.5)
  * XSLT library
  * Multibyte library
* Apache
* MySQL
* SMTP connection for use as mail relay (only if you want to send email from Zambia)
  * Note, many hosts limit use of their mail relays in ways not compatible with Zambia
