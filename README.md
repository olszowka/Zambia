# Zambia
Convention scheduling tool originally developed for Arisia. Now used by several other conventions.
Zambia tracks sessions (events, panels, and anything that needs to be scheduled),
participants, and rooms.

## Features
* Track sessions, rooms, and participants
* Comprehensive conflict checking
* Participants log in to enter availability, provide biography, etc.
* Reports for various departments such as technical services and hotel liaison
* Includes interface to KonOpas (https://github.com/eemeli/konopas), a free tool for publishing the schedule to mobile devices

## Requirements
* PHP 7.X (Tested on 7.0, 7.2 & 7.4)
  * XSLT library
  * Multibyte library
* Apache (Should be able to run on other web servers than can handle PHP and MySQL, but not tested)
* MySQL (Tested on 5.6 & 8.0)
* SMTP connection for use as mail relay (only if you want to send email from Zambia)
  * Note, many hosts limit use of their mail relays in ways not compatible with Zambia

## Built In Dependencies
These libraries are included in the repo and should just work if you leave as is
* Client Side
  * Bootstrap 2.3.2 and Bootstrap 4.5.0 (transitioning page by page)
  * Choices 9.0.0
  * DataTables 1.10.16
  * JQuery 1.7.2 and JQuery 3.5.1 (transitioning page by page)
  * JQueryUI 1.8.16
  * tinyMCE 5.4.2
* Server Side  
  * Swift mailer 5.4.8
  * Guzzle 6.5.3

## Integrations
Other software which can work with Zambia
* https://github.com/pselkirk/conguide, a tool for producing a printable pocket program in InDesign from Zambia, including a schedule grid
* KonOpas (https://github.com/eemeli/konopas), a free tool for publishing the schedule to mobile devices

## More Information
Check out the [wiki](https://github.com/olszowka/Zambia/wiki)

Join the Slack team. Create an issue in this repo if you have no other way to reach me to request an invitation.
