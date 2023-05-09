# Zambia
Convention scheduling tool originally developed for Arisia. Now used by several other conventions.
Zambia tracks sessions (events, panels, and anything that needs to be scheduled),
participants, and rooms.

## Features
* Track sessions, rooms, and participants
* Comprehensive conflict checking
* Participants log in to enter availability, provide biography, etc.
* Reports for various departments such as technical services and hotel liaison
* Includes interface to KonOpas/ConClár, free tools for publishing the schedule

## Requirements
* PHP 8.0 or greater (Tested on 8.0 and 8.1)
  * XSLT library
  * Multibyte library
* Apache (Should be able to run on other web servers than can handle PHP and MySQL, but not tested)
* MySQL (Tested on 5.7 & 8.0)
* SMTP connection for use as mail relay (only if you want to send email from Zambia)
  * Note, many hosts limit use of their mail relays in ways not compatible with Zambia
* Google reCAPTCHA account (If you want to allow users to reset their own passwords via email)

## Built In Dependencies
These libraries are included in the repo and should just work if you leave as is
* Client Side
  * Bootstrap 2.3.2 / 4.5.0
  * Choices 9.0.0
  * DataTables 1.10.16
  * JQuery 1.7.2 / 3.5.1
  * JQueryUI 1.8.16
  * Tabulator 4.9.1
  * TinyMCE 5.6.2
* Server Side  
  * Swift mailer 5.4.8
  * Guzzle 6.5.3

## Integrations
Other software which can work with Zambia
* ConClár [(repo)](https://github.com/lostcarpark/conclar) a free tool for publishing the schedule
* conguide [(repo)](https://github.com/pselkirk/conguide), a tool for producing a printable pocket program in InDesign from Zambia, including a schedule grid
* KonOpas [(repo)](https://github.com/dpmott/konopas) a free tool for publishing the schedule

## Installation
See [Installation](Install/INSTALL.md)

## More Information
Check out the [wiki](https://github.com/olszowka/Zambia/wiki)

Join the Slack team. Create an issue in this repo if you have no other way to reach me to request an invitation.
