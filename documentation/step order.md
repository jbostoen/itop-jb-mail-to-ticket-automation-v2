# Default step order

Every incoming e-mail runs through a series of steps (policies and core actions).  
Steps are executed by ascending `$iPrecedence` (lower = first). Steps sharing the same precedence run in order of their fully-qualified class name (so `Steps\Core\...` comes before `Steps\...`).  
As soon as a step changes the next action to anything other than "process message" (e.g. delete, mark as undesired), the remaining steps are skipped.

The namespace of all steps is `JeffreyBostoenExtensions\MailToTicket\Steps`.

| Precedence | Step | Purpose |
|-----------:|------|---------|
| -10 | `SenderAuthentication` | Evaluates the SPF/DKIM results (RFC 8601) reported by the receiving mail server. |
| 0 | `NonDeliveryReport` | Ignores delivery failure messages and can mark a person as inactive. |
| 9 | `MatchByInReplyToOrReferences` | Finds a related ticket based on the In-Reply-To or References headers. |
| 10 | `AttachmentForbiddenMimeType` | Enforces rules on attachment types. |
| 10 | `AutoReply` | Handles auto-reply e-mails. |
| 10 | `LimitMailSize` | Prevents large e-mails from being processed. |
| 10 | `NoSubject` | Enforces non-empty subjects. |
| 10 | `OtherEmailCallerThanTicketCaller` | Handles an e-mail caller that is not the ticket caller. |
| 10 | `SenderEmailAddress` | Accepts only specific sender addresses (patterns). |
| 10 | `UnknownTicketReference` | Handles unknown ticket references. |
| 20 | `Core\AttachmentCriteria` | Ignores attachments that do not meet the criteria (e.g. tiny signature images). |
| 20 | `OtherRecipients` | Ensures the mailbox is the sole recipient (To, CC). |
| 20 | `TicketClosed` | Handles replies to closed tickets. |
| 20 | `TicketResolved` | Handles replies to resolved tickets. |
| 20 | `UndesiredTitlePatterns` | Handles undesired title patterns. |
| 110 | `Core\FindCaller` | Finds the caller, or creates a Person if the caller is unknown. |
| 110 | `RemoveTitlePatterns` | Removes patterns from the subject (and later the ticket title). |
| 111 | `UpdateCallerAttributes` | Updates attributes on an already known caller (e.g. reactivates a Person). |
| 115 | `Core\FindAdditionalContacts` | Adds people in To: or CC: as related contacts. |
| 200 | `Core\CreateOrUpdateTicket` | Creates or updates the ticket. |
| 201 | `Core\SaveReferences` | Links the In-Reply-To or References headers to the ticket. |
| 9999 | `Core\FinalAction` | Keeps, moves or deletes the e-mail (only if processing was successful). |

Extensions can add their own steps with any precedence; see [customization](customization.md).

## Why the order matters

* Sender authentication runs first. Later steps (such as `NonDeliveryReport`, which can modify a Person) must not act on a message that failed authentication.
* Because it runs before auto-reply and non-delivery detection, a reply to the sender of a message that failed `SenderAuthentication` (a trigger on its event) should use `$mail->reply_address$`, which is empty for automatically generated messages (see [events and triggers](events%20and%20triggers.md#sender-authentication)). Replying to a message that turns out to be an auto-reply or a non-delivery report risks a mail loop, and a failed authentication often means a spoofed sender (backscatter).
* Policies (precedence 10 - 20) run before the caller is looked up (110), so a rejected e-mail does not create Persons or tickets.
* `MatchByInReplyToOrReferences` (9) collects the info that `SaveReferences` (201) stores after the ticket has been created or updated.

## Keeping this file up to date

Update the table whenever a step is added, removed, or its precedence changes.
