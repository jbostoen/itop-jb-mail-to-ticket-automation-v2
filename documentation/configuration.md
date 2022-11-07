# Configuration

This document describes most of the settings.
It also contains additional info about the available default policies.

A policy should be interpreted as a 'mail processing step'.
It can be used to check a condition; but it can also actually perform a step in the ticket creation/update process (for example: matching a caller).

Mailbox settings are mostly similar to [Combodo's original version](https://www.itophub.io/wiki/page?id=extensions%3Aticket-from-email).

One thing is important here: it's actually recommended to set **use_message_id_as_uid** to 'true' in the config file in a lot of cases to avoid duplicates 
(Combodo sets it to 'false' by default but this could be very undesired for IMAP connections!). 
Also make sure the **PHP IMAP extension is enabled**.

Mind that especially when processing lots of new e-mails, it may be important to increase your PHP memory limit!


## Mailbox Configuration

* **Mail Server** 
* **Login**
* **Password** - Warning: just like Combodo's Mail to Ticket Automation, the password is still saved unencrypted!
* **OAuth client** - Only if OAuth is needed. Details: please see [Combodo's documentation on OAuth clients](https://www.itophub.io/wiki/page?id=2_7_0%253Aadmin%253Aoauth).
* **Protocol** - IMAP
* **Port (993 for IMAP)** - often 993 for IMAP.
* **Mailbox folder (for IMAP)** - the folder, for example InboxTest/SomeSubFolder.
* **Mail From Address** - The e-mail address shown as "Sender" for any error/bounce e-mails that are sent
* **Active** - Check mailbox.
* **Debug trace** - Debug log.
* **Mail Aliases** - One per line. Regex patterns allowed. List each email address (minimum 1)
* **IMAP options** - One per line. Warning: overrides global (configuration file) IMAP options completely! Currently not available when using OAuth2.


## Hints


### IMAP options

By default, it will usually look like this:

```
imap
ssl
novalidate-cert
```

### Office 365 / OAuth2

* [Combodo's documentation: Configure OAuth2 in iTop](https://www.itophub.io/wiki/page?id=2_7_0%3Aadmin%3Aoauth)
* [Microsoft documentation: Configuration on Azure / Exchange Online](https://docs.microsoft.com/en-us/exchange/client-developer/legacy-protocols/how-to-authenticate-an-imap-pop-smtp-application-by-using-oauth#use-client-credentials-grant-flow-to-authenticate-imap-and-pop-connections)


### Office 365 with shared mailbox

* Option 1: Enable the account and set a password.
 
* Option 2: Use these IMAP options:

```
imap
ssl
novalidate-cert
authuser=some@mailbox.org
user=shared@mailbox.org
```

### GMail - enabling IMAP

* You need to explicitly enable IMAP access for your GMail account.
* Often Google blocks initial requests. You'll receive an e-mail notification from Google in the inbox to address this. 
* It will involve enabling 'less secure access'. It takes some time on Google's end before this is active.

### OAuth2

* Nothing special here, just check .



# Policies 

## Basics about policies

Common options are:
* Behavior (on conflict/not compliant with policy)
  * Bounce and delete (inform the user the message has been rejected, provide some information why)
  * Delete
  * Do nothing (can be used for tests and might result in some output, without taking further action)
  * Inactive (contrary to "Do nothing", the policy is not even processed)
  * Mark as undesired (keeps the email, but will ignore it in future processing)
  * Mark as error (keeps the email)
* Bouncing (sending message to the user telling their email is rejected)
  * Subject
  * Message

In the bounce message, some placeholders (variables) can be used in the subject and in the message.  
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

* **Policy violation behavior** - Only create new tickets, only update existing tickets; or do both.
* **After processing the email** - Delete the e-mail right away, keep the e-mail on the mail server in the same folder or move to a different folder.
* **Ticket Class** - Which ticket class (see iTop data model, usually UserRequest)
* **Ticket Default Values** - Default values for tickets (see iTop data model, example below).
* **Title Pattern** - Pattern which will be used to match tickets based on a reference. Example: /R-([0-9]{6})/
* **Ignore patterns in subject** - Regex patterns, one per line. To make other patterns ignored while processing/finding related ticket (e.g. another ticket system with IR-123456 numbering).
* **Stimuli to apply** - Example: reopen a ticket which was in a pending state (pending:ev_reopen)
* **Target folder**  
  When enabled (see "After processing the email") and a target folder is specified, processed e-mail messages will be moved to this folder.  
  Use case: useful when the e-mail should be kept (for archive purposes), but the inbox should contain as few e-mails as possible (for performance).
  


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

In addition, for each recipient, you can use these:
```
$recipient->email$
$recipient->name$
```


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

Run these commands in PowerShell:
``` 
Set-CASMailbox -Identity 'mailboxusername' -ImapUseProtocolDefaults $false
Set-CASMailbox -Identity 'mailboxusername' -ImapMessagesRetrievalMimeFormat HtmlOnly
 ```
 

