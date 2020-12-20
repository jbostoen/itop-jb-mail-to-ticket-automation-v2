# Mail to Ticket Automation
Copyright (C) 2019-2020 Jeffrey Bostoen

[![License](https://img.shields.io/github/license/jbostoen/iTop-custom-extensions)](https://github.com/jbostoen/iTop-custom-extensions/blob/master/license.md)
[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.me/jbostoen)
🍻 ☕


Need assistance with iTop or one of its extensions?  
Need custom development?  
Please get in touch to discuss the terms: **jbostoen.itop@outlook.com**


## What?

This **Mail to Ticket automation** is a **fork** from Combodo's Mail to Ticket Automation.  
It was originally based on their version 3.0.7 (28th of August 2017), but also includes the changes up to 3.1.2 so far.  
Some fixes in this version were accepted by Combodo back in August 2018 and are now part of the official version.

What is different? In a few cases, Combodo's implementation of Mail to Ticket Automation was not sufficient enough.  
This extension works in steps. Those steps are called **policies** and they **can** do two things: 

* **determine if further processing should be blocked**
  * Examples: bouncing emails without subjects, with other people as recipient, ...
* **perform an automated action**
  * Examples: determining and linking additional contacts, saving emails to a folder, ...
  * Info should only be set by one policy. That's why some of the default policies check whether some information (such as related contacts) hasn't been set yet.  


## Requirements

* iTop extensions
  * [jb-framework](https://github.com/jbostoen/itop-jb-framework) - a generic framework shared among some of my extensions

## Configuration
Info on settings and default policies
* See [configuration](documentation/configuration.md)

## Customization
Want to implement your own logic?
* See [customization](documentation/customization.md)

## History
Short term roadmap: this was my first PHP extension (fork) for iTop, somewhere in 2015.  
Initially it was only a minor improvement, but it grew over time. It worked, but the code was not "by the book".

At the end of 2019, a refactoring effort was made ("v2").

## Roadmap

* Expect an **optional** link to the **ContactMethod** class (jb-contactmethod), so a caller can have multiple email addresses.
* Password field will be reviewed.

Other new features can be proposed, but are currently not planned.

## Other improvements
In comparison to Combodo's official Mail to Ticket Automation extension:

### Minor code tweaks
Some code was simplified.

Also more flexible in title patterns (no regex group required).

### Lost IMAP connections
There's an attempt to fix issues with lost IMAP connections (to Office 365).
Contrary to the original extension, EmailReplicas don't immediately disappear when the mail can not be seen anymore.
The email record is still kept for 7 days after the email was last seen.

Benefit: if the email wasn't seen due to a lost IMAP connection, the EmailReplica got deleted with the original Combodo extension.
If in the next run the IMAP connection functions properly, the email would be reprocessed as 'new' - which led to new tickets being created.

### Invalid from/to/cc headers
In rare cases, these headers may include invalid data. This fork surpresses the notice which occurs while handling these kind of emails.

### IMAP processing order
This extension fetches e-mails through an IMAP connection.
Some providers (Google) return e-mails from oldest to newest; so this is processed correctly.
Other providers (Microsoft) return them from newest to oldest; sometimes mixing up chronology in iTop.
A setting has been introduced to "reverse" the processing order.

### Related Combodo tickets

These tickets are related to the official extension.
These issues are tackled in this fork, either by implementing a fix here or by backporting a fix by Combodo.

* #1402 Ticket from email: duplicating tickets (use_message_as_uid = true) (recommendation)
* #1489 Emoji might cause Ticket from eMail to hang (reported issue, fix by Combodo)
* #1628 Mail to Ticket: FindCaller (suggested fix, implemented by Combodo)
* #1781 Mail to Ticket Automation: Case log - link to user; caselog enhancement (no solution in Combodo version)
* #1793 Mail to Ticket Automation: set IMAP undelete  (no solution in Combodo version. Pull request made.)
* #1859 Mail to Ticket Automation - issue with long subjects (+ solution) (some feedback, no solution in Combodo version. Pull request made.)
* #1924 Mail to Ticket: processing order (no solution in Combodo version)
* #1925 Mail to Ticket: malformed headers (some feedback, no solution in Combodo version. Pull request made.)
* #1930 Mail to Ticket: improved parsing of from: address (no feedback yet, no solution in Combodo version. Pull request made.)

## Upgrade notes
* See [Upgrade notes](documentation/upgrade notes.md)

## Cookbook

PHP
* how to rename value enums by running queries during installation (ModuleInstallerAPI)
* how to columns value enums by running queries during installation (ModuleInstallerAPI)
* how to implement a background process (```iBackgroundProcess```)



