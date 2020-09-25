# Warning
Currently working on version 2.
Work in progress; for now: use version 1 which is included in the ZIP file.


## Special note
This extension was complex to develop and is now very feature rich, yet it remains a free extension.
If you want to use this extension and get support or custom development, get in touch please to discuss terms: **jbostoen.itop@outlook.com**


# What?

This **Mail to Ticket automation** is a **fork** from Combodo's Mail to Ticket Automation. 
It was originally based on their version 3.0.7 (28th of August 2017), but also includes the changes up to 3.1.0 so far.
Some fixes in this version were accepted by Combodo back in August 2018 and are now part of the official version.

What is different? In a few cases, Combodo's implementation of Mail to Ticket Automation was not sufficient enough. 
This extension works in steps. Those steps are called **policies** and they **can** do two things: 

* **determine if further processing should be blocked**
  * Examples: bouncing emails without subjects, with other people as recipient, ...
* **perform an automated action**
  * Examples: determining and linking additional contacts, saving emails to a folder, ...
  * Info should only be set by one policy. That's why some of the default policies check whether some information (such as related contacts) hasn't been set yet.  


# Configuration

Configuration settings are mostly similar to https://www.itophub.io/wiki/page?id=extensions%3Aticket-from-email

One thing is important here: it's actually recommended to set **use_message_id_as_uid** to 'true' in the config file in a lot of cases to avoid duplicates 
(Combodo sets it to 'false' by default but this could be very undesired for IMAP connections!). 

For IMAP, here's a quick example on the configuration options (config-itop.php).
Also make sure the PHP IMAP extension is enabled.

```
	'imap_options' => array (
	  0 => 'imap',
	  1 => 'ssl',
	  2 => 'novalidate-cert',
	),
```


# Roadmap and history
Short term roadmap: this was my first PHP extension (fork) for iTop, somewhere in 2015.
Initially for a minor improvement only, but it grew over time. It works, but the code was not "by the book".

At the end of 2019, a refactoring attempt is being made. 
It will keep the current options and datamodel.

Also expect an **optional** link to the **ContactMethod** class you find in this repository, so a caller can have multiple e-mail addresses.

Other new features may be proposed, but are currently not planned.

Password field will be reviewed.


# Basics about policies
Common options are:
* behavior (on conflict/not compliant with policy)
  * bounce and delete (inform the user the message has been rejected, provide some information why)
  * delete
  * do nothing (can be used for tests, without taking further action: does it detect policy violations?)
  * mark as undesired (keeps the e-mail, but will ignore it in future processing)
  * mark as error (keeps the e-mail)
* bouncing (sending message to the user telling their e-mail is rejected)
  * subject
  * message

In the bounce message, some placeholders (variables) can be used. In fact, most (all?) strings from the EmailMessage class are supported.
So in the bounce subject/message, it's possible to use **$mail->subject$** etc. (list below)

```
body_format
body_text
body_text_plain (not a property of Email Message, but gives a version with HTML tags stripped)
caller_email
caller_name
date
message_id
recipient
subject
uidl
```

# Configuration
## Mailbox Configuration
* **Mail Server** 
* **Login**
* **Password** - warning: just like Combodo's Mail to Ticket Automation, the password is still saved unencrypted!
* **Protocol** - POP or IMAP
* **Port (993 for IMAP)** - often 993 for IMAP
* **Mailbox folder (for IMAP)** - the folder, for example InboxTest/SomeSubFolder
* **Mail From Address** - errors/bounce messages are sent 'from'
* **Active** - check mailbox
* **Debug trace** - debug log
* **Mail Aliases** - one per line. Regex patterns allowed. List each email address (minimum 1)
	 
# Behavior on Incoming eMails
* **Policy violation behavior** - create only, update only or both
* **After processing the eMail** - delete it right away or keep it on the mail server
* **Ticket Class** - which ticket class (see iTop data model, usually UserRequest)
* **Ticket Default Values** - default values for tickets (see iTop data model, example below).
* **Title Pattern** - example: /R-([0-9]{6})/
* **Ignore patterns in subject** - regex patterns, one per line. To make other patterns ignored while processing/finding related ticket (e.g. another ticket system with IR-123456 numbering).
* **Stimuli to apply** - example: reopen ticket?

```
    service_id:1
    impact:3
    agent_id:395
    team_id:2
    status:assigned
```	 
	 
# Emails in Error
This handles technical issues with e-mails; not policy violations.
* **Policy violation behavior**
  * Delete
  * Mark as error
* **Forward eMails (in error) To Address**


***

# Available policies
A list of included policies which have settings that can be edited in the MailBox configuration.
With some programming skills, it's easy to implement your own policy (see further in this document).
If it's a common use case, make a pull request to include it.

## E-mail Size
* **Use case:** e-mail size is too big (often related to PHP or MySQL limits)
* **Policy violation behavior**
  * Bounce to sender and delete
  * Bounce to sender and mark as undesired
  * Delete
  * Do nothing
  * Mark as undesired
* **Bounce subject**
* **Bounce message**
* **Max size (MB)** - default is 10 MB
 
## Attachment - forbidden mime types
* Use case: you might not want .exe attachments
* **Policy violation behavior**
  * Bounce to sender and delete
  * Bounce to sender and mark as undesired
  * Delete
  * Do nothing
  * Fallback: ignore forbidden attachments
  * Mark as undesired
* **Bounce subject**
* **Bounce message**
* **MIME Types** - one per line. Example: application/exe
	
## Attachment - image dimensions
* Use case: ignoring images which are too small (likely part of e-mail signatures) or resize images which are too big.
* Requires php-gd
* **Min width**
* **Max width**
* **Min height**
* **Max height**
 
## No subject
* Use case: you want to enforce people to at least supply a subject.
* **Policy violation behavior**
  * Bounce to sender and delete
  * Bounce to sender and mark as undesired
  * Delete
  * Do nothing
  * Fallback: default subject
* **Bounce subject**	 
* **Bounce message**
* **Default subject** - specify a default title. Example: (no subject)
 
## Closed tickets
* Use case: despite very clear warnings a ticket has been closed, user still replies.
* **Policy violation behavior**
  * Bounce to sender and delete
  * Bounce to sender and mark as undesired
  * Delete
  * Do nothing
  * Fallback: reopen ticket
  * Mark as undesired
* **Bounce subject**
* **Bounce message**

## Resolved tickets
* Use case: despite very clear warnings a ticket has been resolved, user still replies.
* **Policy violation behavior**
  * Bounce to sender and delete
  * Bounce to sender and mark as undesired
  * Delete
  * Do nothing
  * Fallback: reopen ticket
  * Mark as undesired
* **Bounce subject**
* **Bounce message**
	 
## Unknown tickets
* Use case: if the extension (mis)interprets a pattern similar to the ticket reference pattern in the email subject or header and can't find the ticket.
* **Policy violation behavior**
  * Bounce to sender and delete
  * Bounce to sender and mark as undesired
  * Delete
  * Do nothing
  * Mark as undesired
* **Bounce subject**
* **Bounce message**

## Unknown caller
* Use case: first time caller
* **Policy violation behavior**
  * Bounce to sender and delete
  * Bounce to sender and mark as undesired
  * Delete
  * Do nothing
  * Fallback: create person (with specified default values)
  * Mark as undesired
* **Bounce subject**
* **Bounce message**
* **Default values for new contact** - see example for minimal configuration

```
	org_id:1 
	first_name:Unknown 
	name:Caller
```

( creates a person named 'Unknown Caller', belonging to first organization in iTop)
	 
## Other recipients
* Use case:
  * If other recipients (To: or CC:) to processed inboxes are allowed, it's likely people will reply to the initial email from the caller. 
  * This would lead to multiple new tickets, since there is no ticket reference in the e-mail header or subject.
* **Policy violation behavior**
  * Bounce to sender and delete
  * Bounce to sender and mark as undesired
  * Delete
  * Do nothing
  * Fallback: ignore other contacts
  * Fallback: add other contacts, create if necessary
  * Fallback: add other existing contacts
  * Mark as undesired 
* **Bounce subject**
* **Bounce message**
	 
## Undesired patterns in title
* Use case: out-of-office, e-mail should NOT be processed
* **Policy violation behavior**
  * Bounce to sender and delete
  * Bounce to sender and mark as undesired
  * Delete
  * Do nothing
  * Mark as undesired
* **Bounce subject**
* **Bounce message**
* **Undesired patterns in subject** - (regex patterns, one per line)

## Patterns to remove from title
* Use case: getting rid of unwanted content in subjects/titles
  * Limitation: it will still be problematic if the ticket reference pattern is exactly the same!
* **Policy violation behavior**
  * Fallback - Remove: it's removed completely in the title, even when viewing in iTop.
  * Do nothing
* **Bounce subject**
* **Bounce message**
* **Patterns to remove from subject** - (regex patterns, one per line)


# Other improvements


## Minor code tweaks
Some code was simplified.

Also more flexible in title patterns (no regex group required).

## Lost IMAP connections
There's an attempt to fix issues with lost IMAP connections (to Office 365).
Contrary to the original extension, EmailReplicas don't immediately disappear when the mail can not be seen anymore.
It's stored for 7 more days after it's last seen.

Benefit: if the e-mail wasn't seen due to a lost IMAP connection, the EmailReplica got deleted with the original Combodo extension.
If in the next run the IMAP connection functions properly, the e-mail would be reprocessed as 'new' - which led to new tickets being created.

# Cookbook

PHP
* how to rename value enums by running queries during installation (ModuleInstallerAPI)
* how to columns value enums by running queries during installation (ModuleInstallerAPI)

# Creating new additional policies
Enforcing certain rules or simply adding your own basic logic to set Ticket info or derive a caller (Person) can be done by writing your own class implementing the **iPolicy** interface.

The most important things about the interface:
* implement the ```Init()``` method
* specify a ```$iPrecedence``` method.
  * This is the order in which policies are executed. Lower = first, higher  = later. 
  * Not necessary to make this unique.
  * $iPrecedence = 200 is used for ticket creation/update.
* implement the ```IsCompliant()``` method. true = continue processing; false = stop processing this email (marked as undesired)


