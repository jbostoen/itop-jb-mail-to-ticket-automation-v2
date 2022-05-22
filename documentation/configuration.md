# Configuration

This document describes most of the settings.
It also contains additional info about the available default policies.

A policy should be interpreted as a 'mail processing step'.
It can be used to check a condition; but it can also actually perform a step in the ticket creation/update process (for example: matching a caller).

Mailbox settings are mostly similar to https://www.itophub.io/wiki/page?id=extensions%3Aticket-from-email

One thing is important here: it's actually recommended to set **use_message_id_as_uid** to 'true' in the config file in a lot of cases to avoid duplicates 
(Combodo sets it to 'false' by default but this could be very undesired for IMAP connections!). 
Also make sure the **PHP IMAP extension is enabled**.

Mind that especially when processing lots of new e-mails, it may be important to increase your PHP memory limit!


### Mailbox Configuration

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
* **IMAP options** - one per line. Warning: overrides global (configuration file) IMAP options completely!


#### Hints on IMAP options

By default, it will usually look like this:

```
imap
ssl
novalidate-cert
```


#### Hints on Office 365 with shared mailbox

* option 1: enable the account and set a password
 
* option 2: use these IMAP options:  

```
imap
ssl
novalidate-cert
authuser=some@mailbox.org
user=shared@mailbox.org
```

#### Hints on GMail

* You need to explicitly enable IMAP access for your GMail account
* Often Google blocks initial requests. You'll receive an e-mail notification from Google in the inbox to address this. 
* It will involve enabling 'less secure access'. It takes some time on Google's end before this is active.


# Policies 

## Basics about policies

Common options are:
* behavior (on conflict/not compliant with policy)
  * bounce and delete (inform the user the message has been rejected, provide some information why)
  * delete
  * do nothing (can be used for tests, without taking further action: does it detect policy violations?)
  * mark as undesired (keeps the email, but will ignore it in future processing)
  * mark as error (keeps the email)
* bouncing (sending message to the user telling their email is rejected)
  * subject
  * message

In the bounce message, some placeholders (variables) can be used.  
In fact, most (all?) strings from the EmailMessage class are supported.  
So in the bounce subject/message, it's possible to use `$mail->subject$` etc. (list below)


## Available placeholders

The place holders can be used like this: `$mail->some_property_from_the_list_below$` , e.g. `$mail->subject$`

Available properties for the mail:

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

## Behavior on Incoming emails

* **Policy violation behavior** - create only, update only or both
* **After processing the email** - delete it right away or keep it on the mail server
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
	 
## Emails in Error

This handles technical issues with emails; not policy violations.
* **Policy violation behavior**
  * Delete
  * Mark as error
* **Forward emails (in error) To Address**



## Available policies

A list of included policies which have settings that can be edited in the MailBox configuration.  
With some programming skills, it's easy to implement your own policy (see further in this document).  
If it's a common use case, make a pull request to include it.

### Email Size

* **Use case:** email size is too big (often related to PHP or MySQL limits)
* **Policy violation behavior**
  * Bounce to sender and delete
  * Bounce to sender and mark as undesired
  * Delete
  * Do nothing
  * Inactive
  * Mark as undesired
* **Bounce subject**
* **Bounce message**
* **Max size (MB)** - default is 10 MB
 
### Attachment - forbidden mime types

* Use case: you might not want .exe attachments
* **Policy violation behavior**
  * Bounce to sender and delete
  * Bounce to sender and mark as undesired
  * Delete
  * Do nothing
  * Inactive
  * Fallback: ignore forbidden attachments
  * Mark as undesired
* **Bounce subject**
* **Bounce message**
* **MIME Types** - one per line. Example: application/exe
	
### Attachment - image dimensions

* Use case: ignoring images which are too small (likely part of email signatures) or resize images which are too big.
* Requires php-gd
* **Min width**
* **Max width**
* **Min height**
* **Max height**
 
### No subject

* Use case: you want to enforce people to at least supply a subject.
* **Policy violation behavior**
  * Bounce to sender and delete
  * Bounce to sender and mark as undesired
  * Delete
  * Do nothing
  * Inactive
  * Fallback: default subject
* **Bounce subject**	 
* **Bounce message**
* **Default subject** - specify a default title. Example: (no subject)
 
### Other e-mail caller than original ticket caller

Recommendation: enable this for security reasons.  
Anyone replying (even by guessing or just going over ticket numbers) to a ticket,  
may trigger a notification with the new log entry to the original ticket caller.
  
* Use case: block others from replying to a ticket.
* **Policy violation behavior**
  * Bounce to sender and delete
  * Bounce to sender and mark as undesired
  * Delete
  * Do nothing
  * Inactive
  * Fallback: default subject
* **Bounce subject**	 
* **Bounce message**
* **Enabled** - yes/no.
 

### Closed tickets

* Use case: despite very clear warnings a ticket has been closed, user still replies.
* **Policy violation behavior**
  * Bounce to sender and delete
  * Bounce to sender and mark as undesired
  * Delete
  * Do nothing
  * Inactive
  * Fallback: reopen ticket
  * Mark as undesired
* **Bounce subject**
* **Bounce message**

### Resolved tickets

* Use case: despite very clear warnings a ticket has been resolved, user still replies.
* **Policy violation behavior**
  * Bounce to sender and delete
  * Bounce to sender and mark as undesired
  * Delete
  * Do nothing
  * Inactive
  * Fallback: reopen ticket
  * Mark as undesired
* **Bounce subject**
* **Bounce message**
	 
### Unknown tickets

* Use case: if the extension (mis)interprets a pattern similar to the ticket reference pattern in the email subject or header and can't find the ticket.
* **Policy violation behavior**
  * Bounce to sender and delete
  * Bounce to sender and mark as undesired
  * Delete
  * Do nothing
  * Inactive
  * Mark as undesired
* **Bounce subject**
* **Bounce message**

### Unknown caller

* Use case: first time caller
* **Policy violation behavior**
  * Bounce to sender and delete
  * Bounce to sender and mark as undesired
  * Delete
  * Do nothing
  * Inactive
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

⚠ You can also use the placeholders such as the caller's name and e-mail in values! (See above: Available placeholders)


	 
### Other recipients specified in To: or CC:

* Use case:
  * If other recipients (To: or CC:) to processed inboxes are allowed, it's likely people will reply to the initial email from the caller. 
  * This would lead to multiple new tickets, since there is no ticket reference in the email header or subject.
* **Policy violation behavior**
  * Bounce to sender and delete
  * Bounce to sender and mark as undesired
  * Delete
  * Do nothing
  * Inactive
  * Fallback: ignore other contacts
  * Fallback: add other contacts, create if necessary
  * Fallback: add other existing contacts
  * Mark as undesired 
* **Bounce subject**
* **Bounce message**

The default values work the same as for unknown callers.

### Undesired patterns in title

* Use case: out-of-office or automatic replies such as 'new ticket created' (in another service desk tool), email should NOT be processed
* **Policy violation behavior**
  * Bounce to sender and delete
  * Bounce to sender and mark as undesired
  * Delete
  * Do nothing
  * Inactive
  * Mark as undesired
* **Bounce subject**
* **Bounce message**
* **Undesired patterns in subject** - (regex patterns, one per line)

### Patterns to remove from title

* Use case: getting rid of unwanted content in subjects/titles
  * Limitation: it will still be problematic if the ticket reference pattern is exactly the same!
* **Policy violation behavior**
  * Fallback - Remove: it's removed completely in the title, even when viewing in iTop.
  * Do nothing
  * Inactive
* **Bounce subject**
* **Bounce message**
* **Patterns to remove from subject** - (regex patterns, one per line)

### Limit accepted e-mail replies to original ticket caller's e-mail address

* Use case: security measure. Only accept e-mail replies to a ticket if it's from the same e-mail address as the original caller.
  * Limitation: it will still be problematic if the ticket reference pattern is exactly the same!
* **Policy violation behavior**
  * Bounce to sender and delete
  * Bounce to sender and mark as undesired
  * Delete
  * Do nothing
  * Inactive
  * Mark as undesired
* **Bounce subject**
* **Bounce message**

# Troubleshoot

## HTML e-mails are processed as plain text (Exchange servers - on premises)

Solution provided by Dejan Skubic for Exchange (on premises) editions:
``` 
[PS] C:\Windows\system32>set-CASMailbox -Identity 'mailboxusername' -ImapUseProtocolDefaults $false
[PS] C:\Windows\system32>set-CASMailbox -Identity 'mailboxusername' -ImapMessagesRetrievalMimeFormat HtmlOnly
 ```
 

