# Mail to Ticket Automation

Copyright (c) 2019-2022 Jeffrey Bostoen

[![License](https://img.shields.io/github/license/jbostoen/iTop-custom-extensions)](https://github.com/jbostoen/iTop-custom-extensions/blob/master/license.md)
[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.me/jbostoen)
🍻 ☕

Need assistance with iTop or one of its extensions?  
Need custom development?  
Please get in touch to discuss the terms: **info@jeffreybostoen.be** / https://jeffreybostoen.be


## What?

⚠ Only use [releases](https://github.com/jbostoen/itop-jb-mail-to-ticket-automation-v2/releases) from this repository in iTop environments.  
⚠ POP3 has been deprecated in this fork. Currently only IMAP is supported.  

This **Mail to Ticket automation** is a **fork** from Combodo's Mail to Ticket Automation.  
It was originally based on their version 3.0.7 (28th of August 2017), but also includes the changes up to 3.5.1 so far.  
Some fixes in this version were accepted by Combodo back over time and are now part of the official version.

What is different? In a few cases, Combodo's implementation of Mail to Ticket Automation was not sufficient enough.  
This extension works in steps. Those steps are called **policies** and they **can** do two things: 

* **determine if further processing should be blocked**
  * Examples: bouncing emails without subjects, with other people as recipient, ...
* **perform an automated action**
  * Examples: determining and linking additional contacts, saving emails to a folder, ...
  * Ticket fields should only be filled by one policy. That's why some of the default policies check whether some information (such as related contacts) hasn't been set yet.  

Furthermore, it includes some fixes which were also provided back to Combodo where it was implemented at a later point or not at all.



## Requirements

* iTop extensions
  * [jb-framework](https://github.com/jbostoen/itop-jb-framework) - a generic framework shared among some of my extensions

## Optional
* [jb-mail-to-ticket-automation-v2-contactmethod](https://github.com/jbostoen/itop-jb-mail-to-ticket-automation-v2-contactmethod) - provides a possibility to have multiple contact methods per person and process e-mails from those accounts


## Configuration
Info on settings and default policies
* See [configuration](documentation/configuration.md)

## Security recommendations
Highly recommended to read the [security recommendations](documentation/security.md)

## Customization
Want to implement your own logic?
* See [customization](documentation/customization.md)

## Roadmap

Sponsor to specify priority of these features:

- [ ] Auto responder or auto dispatcher. Terms can be discussed (rules based on contact, organization, subject, ...)
- [ ] Support for different protocols
- [ ] Support for IMAP OAuth
- [ ] Support to strip "original message" part from e-mails ( https://github.com/jbostoen/itop-jb-mail-to-ticket-automation-v2/issues/15 )


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
Some providers (Google) return e-mails in a random order.
Other providers (Microsoft) return them from newest to oldest.
This may mix up processing steps to link "new" info to "older" tickets.
A setting has been introduced to "reverse" the processing order.

### Related Combodo tickets

These tickets are related to the official extension.
These issues are tackled in this fork, either by implementing a fix here or by backporting a fix by Combodo.

SourceForge tickets:
* #1402 Ticket from email: duplicating tickets (use_message_as_uid = true) (recommendation)
* #1489 Emoji might cause Ticket from eMail to hang (reported issue, fix by Combodo)
* #1628 Mail to Ticket: FindCaller (suggested fix, implemented by Combodo)
* #1781 Mail to Ticket Automation: Case log - link to user; caselog enhancement (no solution in Combodo version)
* #1793 Mail to Ticket Automation: set IMAP undelete  (no solution in Combodo version. Pull request made, but not accepted because of worries about conflicts.)
* #1859 Mail to Ticket Automation - issue with long subjects (+ solution) (some feedback, pull request accepted by Combodo)
* #1924 Mail to Ticket: processing order of e-mails is not chronologically on Microsoft Exchange/O365 and Google GMail (no solution in Combodo version)
* #1925 Mail to Ticket: malformed headers (some feedback, no solution in Combodo version, implemented by Combodo)
* #1930 Mail to Ticket: improved parsing of from: address (suggested fix, implemented by Combodo)
* #2039 Return code missing in combodo-email-synchro - GetActionFromCode() (suggested fix, implemented by Combodo)

Internal Combodo tickets:
* N° 2181 Mail to Ticket: Sometimes duplicate ticket from mail-to-ticket (suggested fix, implemented by Combodo)

## Upgrade notes

* See [Upgrade notes](documentation/upgrade%20notes.md)

## Cookbook

PHP
* how to rename value enums by running queries during installation (ModuleInstallerAPI)
* how to columns value enums by running queries during installation (ModuleInstallerAPI)
* how to implement a background process (```iBackgroundProcess```)

## Need more?

Some organizations have even more specific needs.
Some of the customizations based on this generic Mail to Ticket Automation extension are:

* match with open ticket based on subject (strip RE:/FW:/FWD:) and original caller in To: or CC:
* match with open ticket based on subject (same event ID being used in every email sent to iTop)
* match with open ticket based on Message-ID of very first e-mail for which a ticket was created and same Message-ID still in the 'References' or 'In-Reply-To' e-mail headers
* move to specific target folder (dedicated to customer)
* ...



## History
Short term roadmap: this was my first PHP extension (fork) for iTop, somewhere in 2015.  
Initially it was only a minor improvement, but it grew over time. It worked, but the code was not "by the book".

At the end of 2019, a refactoring effort was made ("v2").
This version makes it easier to implement additional steps ("policies") on how to handle the incoming e-mails.  
It also contains several improvements and bug fixes compared to the original version, 
although some work has since been implemented in the original extension.  


