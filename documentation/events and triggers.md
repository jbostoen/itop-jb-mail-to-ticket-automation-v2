# Events and triggers

While e-mails are processed, the extension fires **events**: the start of a cron task, a policy violation, a new ticket, a message that was deleted, ...  
A trigger (**Trigger (on mail processing event)**) subscribes to one or more events and activates the actions linked to it: an e-mail, a webhook, ...

This replaces the "bounce" settings (subject and message) of the policies, and goes further: any action, for any event, with the details of the e-mail available as placeholders.

| Part | Class | Purpose |
|------|-------|---------|
| Event | Mail processing event | A fixed list of events. Created automatically; not to be modified. |
| Trigger | Trigger (on mail processing event) | Subscribes to events. Optionally restricted to the mailboxes returned by a query (default: all mailboxes). |
| Action | Any action, such as "Send an e-mail" | What happens when the trigger is activated. |

## Setting it up

1. Create an action. For an e-mail to the sender of a message, use **Send an e-mail (by address)**; the regular "Send an e-mail" can only reach people that exist in iTop.
2. Create a trigger **Trigger (on mail processing event)**.
   * **Events** - the events to subscribe to.
   * **Mailbox** - optional. Leave empty to react to all mailboxes.
   * **Include original message** - attaches the received e-mail (.eml) to the actions, and exposes it as `$mail->eml_base64$` (e.g. for a webhook).
   * **Actions** - link the action(s).
3. Use the placeholders below in the subject, the body, the recipients, ...

Example: reply to the sender when a message is refused because the ticket is closed.

| Setting | Value |
|---------|-------|
| Action | Send an e-mail (by address) |
| From | `helpdesk@example.com` |
| To (address) | `$mail->reply_address$` |
| Subject | `Re: $mail->subject$` |
| Body | `The ticket for your message "$mail->html(subject)$" is closed. Please create a new request.` |
| Trigger events | Ticket closed |
| Trigger | Include original message: yes |

The policy itself keeps its **Policy violation behavior** (e.g. delete or mark as undesired); the event is fired in addition to it.

## Events

The category is the first part of the code.  
The extra values are available as `$details->...$`, on top of the [general ones](#available-placeholders). Example: `$details->error_message$`.

### Cron task and mailbox

| Code | Fires when | Extra values |
|------|------------|--------------|
| `cron_task_started` | A run of the background process starts. | |
| `cron_task_finished` | A run of the background process ends. | `message_count`, `processed_count`, `deleted_count`, `moved_count`, `marked_as_error_count`, `undesired_count`, `skipped_count`, `duration_seconds` |
| `mailbox_started` | The processing of a mailbox starts. | `message_count` |
| `mailbox_finished` | The processing of a mailbox ends. | |
| `mailbox_connection_failed` | The connection to a mailbox, or the listing of its messages, failed. | `error_message` |

### Message

| Code | Fires when | Extra values |
|------|------------|--------------|
| `message_started` | The processing of a message starts (only for messages that are actually handled, not for old messages that are skipped each run). The message is not decoded yet: the `mail` placeholders are not available. | |
| `message_finished` | The processing of such a message ends. | |
| `message_decode_failed` | A message could not be decoded. | `invalid_reasons` |
| `message_deleted` | A message was deleted from the mailbox. | `reason` (sometimes), `next_action` |
| `message_moved` | A message was moved to another folder. | `target_folder` |
| `message_marked_undesired` | A message was marked as undesired. | `next_action` |
| `message_marked_error` | A message was marked as error. | `reason` (sometimes), `next_action` |
| `message_no_ticket` | The processing ended without a ticket being created or linked. | `reason` (the cause, see also `outcome_summary`) |

### Ticket and caller

| Code | Fires when | Extra values |
|------|------------|--------------|
| `ticket_created` | A ticket was created. | |
| `ticket_updated` | A ticket was updated. | |
| `caller_created` | A person was created for an unknown caller. | `caller` object |
| `caller_inactivated` | A person was marked as inactive after a delivery failure. | `caller` object |

### Policies

Violations are fired regardless of the configured behavior (delete, mark as undesired, do nothing, ...).  
Policies that are **Inactive** do not run, so they fire nothing.

| Code | Fires when |
|------|------------|
| `policy_attachment_forbidden_mimetype_violation` | An attachment has a forbidden type. |
| `policy_mail_size_too_big_violation` | The message is too big. |
| `policy_no_subject_violation` | The message has no subject. |
| `policy_other_email_caller_than_ticket_caller_violation` | The sender is not the caller of the ticket. |
| `policy_other_recipients_violation` | The message has other recipients than the mailbox. |
| `policy_sender_authentication_violation` | SPF or DKIM reported a failure. See [sender authentication](#sender-authentication). |
| `policy_sender_email_address_violation` | The sender address is not accepted. |
| `policy_ticket_closed_violation` | The message refers to a closed ticket. |
| `policy_ticket_resolved_violation` | The message refers to a resolved ticket. |
| `policy_ticket_unknown_violation` | The message refers to a ticket that does not exist. |
| `policy_undesired_pattern_violation` | The subject matches an undesired pattern. |
| `policy_unknown_caller_violation` | The sender is unknown. |
| `policy_autoreply_detected` | The message is an auto-reply. |
| `policy_non_delivery_report_detected` | The message is a non-delivery report. |

Extra values for all of them: `step_id`, `step_class`, `step_precedence`, `behavior` (the configured behavior).  
Sender authentication adds `authentication_states` (as `$details->authentication_states$`, e.g. `spf=softfail, dkim=pass`).

### Errors

| Code | Fires when | Extra values |
|------|------------|--------------|
| `error_processing` | An error occurred while processing a message. | `error_code`, `error_message` |

## Available placeholders

The same reference is shown in iTop, collapsed above the form:  
* On a **trigger**: the general placeholders, and the extra values of the events it is subscribed to. In edit mode, and without events, the complete reference.
* On an **action** that is linked to such a trigger (and always on *Send an e-mail (by address)*): the placeholders per event of the linked triggers. In edit mode, the complete reference.

The values are filled in as far as they are known at that point.  
A cron task event has no message, so there are no `mail` or `message` values.

### iTop objects and plain values

Some placeholders are **iTop objects**: every attribute of the object can be used, as in any iTop notification, and the usual verbs work (`$ticket->label(status)$`, `$ticket->html(title)$`, `$ticket->hyperlink()$`, ...).  
The others are **plain values**: a fixed list of text values. They only offer the properties listed below.

| Placeholder | Kind | iTop class | Notes |
|-------------|------|------------|-------|
| `$event->...$` | iTop object | Mail processing event (`MailProcessingEvent`) | The event that was fired. |
| `$mailbox->...$` | iTop object | The mailbox (a subclass of `MailInboxBase`, e.g. `MailInboxStandard`) | Not available for events without a mailbox (cron task). |
| `$ticket->...$` | iTop object | The ticket (a subclass of `Ticket`, e.g. `UserRequest`) | Only when there is one. |
| `$sender->...$` | iTop object | `Person` | Only when the sender is known. |
| `$caller->...$` | iTop object | `Person` | Only for the events *caller created* and *caller inactivated*. |
| `$mail->...$` | plain values | - | The received e-mail. See [the e-mail](#the-e-mail). |
| `$message->...$` | plain values | - | The message in the mailbox. See [the message and the mailbox source](#the-message-and-the-mailbox-source). |
| `$source->...$` | plain values | - | The mailbox source. |
| `$details->...$` | plain values | - | The outcome of the processing, and the extra values of the event. See [details](#details). |
| `$current_date$`, `$current_time$`, `$current_datetime$` | plain value | - | Date and time of processing (in the internal formats of iTop). |

The e-mail itself (`$mail->...$`) is **not** an iTop object: only the properties listed below exist, and verbs such as `label()` or `hyperlink()` do not apply. The one exception is `html()`, see below.

### Escaping for HTML

The values of a received e-mail may contain anything, including markup.  
For **plain values**, use the `html()` verb in an HTML body: `$mail->html(subject)$` instead of `$mail->subject$`. It works for every plain value (`$mail->...$`, `$message->...$`, `$source->...$`, `$details->...$`).  
This is the same verb iTop offers for its objects: `$ticket->html(title)$` also escapes. Use it for any attribute of an object that can hold text from the e-mail.  
In a subject or a recipient, use the value as it is.

An unknown name or verb is not replaced: the placeholder stays visible in the e-mail and iTop logs it.

### The event

| Placeholder | Description |
|-------------|-------------|
| `$event->code$` | The code of the event, as it is (e.g. `ticket_created`). |
| `$event->label(code)$` | The name of the event, translated in the language of the action. |
| `$event->category$`, `$event->label(category)$` | The category, as it is and translated. |

### The e-mail

| Placeholder | Description |
|-------------|-------------|
| `$mail->subject$` | Subject. |
| `$mail->caller_email$`, `$mail->caller_name$`, `$mail->caller_email_suffix$` | The sender. |
| `$mail->reply_address$` | The address to reply to: the sender, or **empty** when the message was generated automatically (e.g. an out-of-office reply). |
| `$mail->recipient$` | The recipient. |
| `$mail->to_addresses$`, `$mail->cc_addresses$` | All addresses in To and Cc, comma-separated. |
| `$mail->date$`, `$mail->date_only$`, `$mail->time_only$` | The Date header of the message (set by the sender). |
| `$mail->body_text$`, `$mail->body_text_plain$`, `$mail->body_format$` | The body. `body_text` is HTML when `body_format` says so; `html()` then shows the markup as text. For an HTML body, use `$mail->html(body_text_plain)$`. |
| `$mail->uidl$`, `$mail->message_id$` | Identifiers of the message. |
| `$mail->eml_base64$` | The original message, base64 encoded (only when **Include original message** is set). |

The sender as an iTop object is `$sender->...$` (a person): `$sender->first_name$`, `$sender->name$`, `$sender->email$`, `$sender->org_name$`, ...  
It does not exist for an unknown sender: use `$mail->caller_name$` and `$mail->caller_email$` then.

### The message and the mailbox source

| Placeholder | Description |
|-------------|-------------|
| `$message->index$` | Position of the message in the listing of the mailbox. |
| `$message->uid$` | The IMAP UID. |
| `$message->internal_identifier$` | The identifier used by the extension. |
| `$message->message_id$` | The Message-ID. |
| `$message->timestamp$`, `$message->timestamp_full$` | When the message was received (Unix timestamp, and Y-m-d H:i:s). |
| `$source->name$`, `$source->mailbox_path$` | The mailbox source and its folder. |

### Details

| Placeholder | Description |
|-------------|-------------|
| `$details->next_action$` | What will happen to the message (e.g. `DELETE_MESSAGE`). |
| `$details->outcome_summary$` | A summary of why the processing ended as it did: notes of the steps and the list of executed steps. |
| `$details->executed_steps$` | The steps that were executed, comma-separated. |
| `$details->...$` | The extra values of the [event](#events), e.g. `$details->reason$`, `$details->error_message$`, `$details->message_count$`. |

Values that hold several items are one comma-separated text. There are no lists.

## Replying to the sender

To reply to the sender, use `$mail->reply_address$` as recipient, rather than `$mail->caller_email$`.  
It is empty when the headers show that the message was generated automatically (e.g. an out-of-office reply), as a reply could start a mail loop.  
An e-mail action without a "To" recipient is not sent (Cc and Bcc are ignored then as well).

## Sender authentication

The sender of a message that fails the SPF/DKIM check may be spoofed, so a reply can reach someone who sent nothing (backscatter).  
The event `policy_sender_authentication_violation` has `$details->authentication_states$` (e.g. `spf=softfail, dkim=pass`).  
An internal alert is usually a better choice than a reply to the sender.

The policy runs before the auto-reply and non-delivery detection. When replying anyway, use `$mail->reply_address$` to avoid mail loops.

## Events of another extension

An extension that adds its own policy (a subclass of the policy base class) can add its own events, and triggers can subscribe to them.

* Implement `JeffreyBostoenExtensions\MailToTicket\iMailProcessingEventProvider` in a class of the extension. `GetMailProcessingEvents()` returns the category by event code.
* Add the codes to the values of `code` of the class `MailProcessingEvent` in the datamodel of the extension (and the category to `category`, when it is a new one). Add dictionary entries for them: the name, and the description (`+`).
* The objects of the events are created during setup, so triggers can subscribe to them.
* Fire the event with `Events::FireEvent('policy_my_check_violation', ['some_value' => '...'])`. The extra values become `$details->some_value$`.
* A policy fires its violation event by itself when the code is `<settings prefix>_violation` (or `<settings prefix>_detected`), where the settings prefix is the one of the policy.

## Good to know

* A trigger without a mailbox also reacts to events that do not belong to a mailbox (`cron_task_started`, `cron_task_finished`).
* An action that fails does not stop the processing of e-mails; the error is logged.
* If no trigger is subscribed to an event, nothing is collected for it.
* New events are created automatically during an upgrade. Existing events and their links to triggers are never modified.
* Triggers and actions created by the upgrade from the former bounce settings are named `Bounce - <mailbox> - <policy>`. See the [upgrade notes](upgrade%20notes.md).
