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
| Body | `The ticket for your message "$mail->html_escape(subject)$" is closed. Please create a new request.` |
| Trigger events | Ticket closed |
| Trigger | Include original message: yes |

The policy itself keeps its **Policy violation behavior** (e.g. delete or mark as undesired); the event is fired in addition to it.

## Events

The category is the first part of the code.  
The extra values of an event are available as `$details->...$`, on top of the [general ones](#available-placeholders). Example: `$details->error_message$`.  
The values that belong to a step are in the groups `$step->...$` and `$step_details->...$` instead, see [the step](#the-step).

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
| `message_deleted` | A message was deleted from the mailbox. | `reason` (sometimes) |
| `message_moved` | A message was moved to another folder. | `target_folder` |
| `message_marked_undesired` | A message was marked as undesired. | |
| `message_marked_error` | A message was marked as error. | `reason` (sometimes) |
| `message_no_ticket` | The processing ended without a ticket being created or linked. | `reason` (the cause, see also `$outcome->summary$`) |

### Step

Fired for every step that is executed for a message (steps that are **Inactive** or not applicable do not run, so they fire nothing).  
Because of the number of steps, these events are only collected when a trigger is subscribed to them.

| Code | Fires when | Extra values |
|------|------------|--------------|
| `step_started` | A step is about to be executed. | `$step->...$` |
| `step_finished` | A step was executed. `$outcome->next_action$` shows whether further processing is stopped. | `$step->...$` |

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

Values for all of them: `$step->...$` (the step that detected the case), and `$step_details->behavior$` (the configured behavior).  
Sender authentication adds `$step_details->authentication_states$` (e.g. `spf=softfail, dkim=pass`).

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
The others are **plain values**: a fixed list of text values. They only offer the properties listed below, and a small set of verbs (see [verbs for plain values](#verbs-for-plain-values)).

| Placeholder | Kind | iTop class | Notes |
|-------------|------|------------|-------|
| `$event->...$` | iTop object | Mail processing event (`MailProcessingEvent`) | The event that was fired. |
| `$mailbox->...$` | iTop object | The mailbox (a subclass of `MailInboxBase`, e.g. `MailInboxStandard`) | Not available for events without a mailbox (cron task). |
| `$ticket->...$` | iTop object | The ticket (a subclass of `Ticket`, e.g. `UserRequest`) | Only when there is one. |
| `$sender->...$` | iTop object | `Person` | Only when the sender is known. |
| `$caller->...$` | iTop object | `Person` | Only for the events *caller created* and *caller inactivated*. |
| `$mail->...$` | plain values | - | The received e-mail. See [the e-mail](#the-e-mail). |
| `$message->...$` | plain values | - | The internal message handler. See [the message](#the-message). Prefer `$mail->...$` where it offers the same. |
| `$step->...$` | plain values | - | What applies to every step. See [the step](#the-step). |
| `$step_details->...$` | plain values | - | What is specific to a step, or a kind of step. See [the step](#the-step). |
| `$outcome->...$` | plain values | - | The outcome of the processing so far. See [the outcome](#the-outcome). |
| `$details->...$` | plain values | - | The extra values of the event itself. See [details](#details). |
| `$current_date$`, `$current_time$`, `$current_datetime$` | plain value | - | Date and time of processing (in the internal formats of iTop). |

The e-mail itself (`$mail->...$`) is **not** an iTop object: only the properties listed below exist, and verbs such as `label()` or `hyperlink()` do not apply.

### Verbs

A verb changes the representation of a value: `$<name>->verb(<property>)$`, e.g. `$ticket->html(title)$`. Without a verb, the value is used as it is.

#### Verbs for iTop objects

The verbs are handled by the attribute of the object, so what they return depends on the type of the attribute:

* `html()` generates the **HTML representation** of the attribute, as shown in iTop. It is not just an escape function:
  * Text (string): the text, HTML-escaped.
  * Long text: the text, escaped, with wiki formatting rendered and line breaks converted, in a `div`.
  * HTML: the markup, in a `div`.
  * E-mail address: a `mailto:` link.
  * Date, enumeration, external key: the formatted representation (e.g. the label of an enumeration value).
  * Case log: the entries, as in an e-mail notification.
  * Link set: a list (`ul`).
* Without a verb (or `text()`): the plain text value.
* `label()`: the value as shown in a form (e.g. the label of an enumeration value).
* Other verbs exist for some types, e.g. `hyperlink()` and `url()` for the object itself.

Use `html()` in an HTML body for attributes of an object that can hold text from the e-mail, and be aware that it can generate markup.

#### Verbs for plain values

| Verb | Result |
|------|--------|
| `html_escape(x)` | The value, escaped for use in an HTML body: `$mail->html_escape(subject)$` instead of `$mail->subject$`. |
| `date_only(x)` | The date part of a value that holds a date and time (Y-m-d): `$mail->date_only(date)$`. |
| `time_only(x)` | The time part of a value that holds a date and time (H:i:s): `$mail->time_only(date)$`. |

`date_only()` and `time_only()` only change a value that is a valid date and time (Y-m-d H:i:s). Any other value (a text, a time for `date_only()`, a date for `time_only()`) is returned as it is.  
The verbs work for the plain-value groups only (`$mail->...$`, `$message->...$`, `$step->...$`, `$step_details->...$`, `$outcome->...$`, `$details->...$`). They do not apply to iTop objects (`$mailbox->...$`, `$ticket->...$`, ...), nor to `$current_date$`, `$current_time$` and `$current_datetime$`.  
The name is `html_escape` on purpose: for iTop objects, `html()` generates HTML, which is not the same as escaping a text value.

### Escaping for HTML

The values of a received e-mail may contain anything, including markup.  
In an HTML body, use `html_escape()` for plain values (`$mail->html_escape(subject)$`) and `html()` for attributes of iTop objects that can hold text from the e-mail (`$ticket->html(title)$`).  
In a subject or a recipient, use the value as it is.

Where the placeholders are used for ticket values (default values of a mailbox), the values are raw as well. Use `html_escape()` there only for an HTML attribute.

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
| `$mail->date$`, `$mail->timestamp$` | The Date header of the message (set by the sender): as Y-m-d H:i:s, and as a Unix timestamp. When the header is missing or can not be read, the time of processing is used. For the parts, use `$mail->date_only(date)$` and `$mail->time_only(date)$`. |
| `$mail->body_text$`, `$mail->body_text_plain$`, `$mail->body_format$` | The body. `body_text` is HTML when `body_format` says so; `html_escape()` then shows the markup as text. For an HTML body, use `$mail->html_escape(body_text_plain)$`. |
| `$mail->uidl$`, `$mail->message_id$` | Identifiers of the message. |
| `$mail->eml_base64$` | The original message, base64 encoded (only when **Include original message** is set). |

The sender as an iTop object is `$sender->...$` (a person): `$sender->first_name$`, `$sender->name$`, `$sender->email$`, `$sender->org_name$`, ...  
It does not exist for an unknown sender: use `$mail->caller_name$` and `$mail->caller_email$` then.

### The message

`$message->...$` is the internal message handler of the extension. What it holds depends on the configuration of the mailbox (e.g. how the identifier is built), and it is available earlier than the e-mail: it is also there when the message could not be decoded.  
Use `$mail->...$` where it offers the same information (e.g. `$mail->message_id$`).

| Placeholder | Description |
|-------------|-------------|
| `$message->index$` | Position of the message in the listing of the mailbox. |
| `$message->uid$` | The IMAP UID. |
| `$message->internal_identifier$` | The identifier used by the extension. |
| `$message->message_id$` | The Message-ID. |

The mailbox the message was read from is `$mailbox->...$`.

### The step

For the events of a step, and the policy events (a step detected the case).

| Placeholder | Description |
|-------------|-------------|
| `$step->id$`, `$step->class$`, `$step->precedence$` | The step: identifier, class and precedence. Applies to every step. |
| `$step_details->behavior$` | The configured behavior. Only for policy events. |
| `$step_details->authentication_states$` | The SPF/DKIM results (e.g. `spf=softfail, dkim=pass`). Only for the event *sender authentication violation*. |

### The outcome

The outcome of the processing, at the moment the event is fired.  
`$outcome->next_action$` is updated after every executed step: for a *step finished* event it shows whether further processing is stopped, for the final events the end result.

| Placeholder | Description |
|-------------|-------------|
| `$outcome->next_action$` | What will happen to the message (e.g. `DELETE_MESSAGE`). |
| `$outcome->summary$` | A summary of why the processing ended as it did: notes of the steps and the list of executed steps. |
| `$outcome->executed_steps$` | The steps that were executed, comma-separated. |

### Details

The extra values of the [event](#events) itself, as listed with the event: `$details->reason$`, `$details->error_message$`, `$details->message_count$`, ...  
What is specific to a step is in `$step_details->...$`.

Values that hold several items are one comma-separated text. There are no lists.

## Replying to the sender

To reply to the sender, use `$mail->reply_address$` as recipient, rather than `$mail->caller_email$`.  
It is empty when the headers show that the message was generated automatically (e.g. an out-of-office reply), as a reply could start a mail loop.  
An e-mail action without a "To" recipient is not sent (Cc and Bcc are ignored then as well).

## Sender authentication

The sender of a message that fails the SPF/DKIM check may be spoofed, so a reply can reach someone who sent nothing (backscatter).  
The event `policy_sender_authentication_violation` has `$step_details->authentication_states$` (e.g. `spf=softfail, dkim=pass`).  
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
