# Security recommendations

## Mailbox credentials

* Make sure to use modern password recommendations when it comes to complexity and length.

## Notifications

Be careful on how you set up e-mail notifications linked to the trigger "When updated by mail".  

Typically this is used for two types of notifications:
* To inform agents a new reply has been added (from a customer).
* To inform a caller and possibly related contacts that the ticket was updated because someone sent an e-mail with the same ticket reference number.

In the last case, you might assume this would either be the original caller or an already related contact.  
However, an attacker could theoretically guess the pattern and spoof an agent's reply - perhaps requesting the recipients to perform a malicious action.  
Another condition would of course be that this attacker managed to become a known contact in iTop (perhaps auto-creation of callers is enabled).  

It may be highly recommended to **NOT** include any info from the (usually public) log in the notifications sent to the customer.  
This however implies that the customers would need to use the portal to view the answer (where they still might be tricked).  

In this fork, an additional policy is by included to mitigate this risk.  
If enabled, an incoming e-mail will only be appended in the ticket log if it originates from the same caller.  
It is however not enabled by default since this is a new behavior.  
Mind that it also rejects replies from anyone else than the ticket caller, even from contacts linked to the ticket.  
That includes people who received the original message in BCC or were never added as a contact.

## Matching replies to tickets

An incoming e-mail is linked to a ticket by the reference in the subject (the title pattern of the mailbox), or by the `In-Reply-To` and `References` headers.  
Nothing verifies who sent it: anyone who can send an e-mail to the mailbox and knows or guesses a reference, can add a message to that ticket.

* Only rely on a title pattern that matches a **random, unguessable** identifier. A sequential reference such as `R-123456` is predictable, and visible in every notification.
* The Message-IDs in the headers are not guessable, but they leak through CC lists, forwards and shared headers.
* The sender is taken from the `From` header, which is not authenticated by SMTP. See the sender authentication below.

## Sender authentication (best practice)

The `From` header of an e-mail is not authenticated by SMTP, so it can be forged.  
Verifying the sender belongs on the mail server or mail gateway that receives the e-mail: it sees the SMTP session (connecting IP address, envelope, DKIM signature) which this extension never sees, since it only fetches the stored message over IMAP afterwards.

* **On the mail server or gateway (primary):** enforce SPF, DKIM and DMARC, and reject or quarantine messages that fail. DMARC is the check that ties the result to the `From` header (alignment).
* **In this extension (second layer):** the policy "Sender authentication (SPF/DKIM)" reads the verdict that the mail server wrote in the `Authentication-Results` header (RFC 8601). It does not verify anything itself.

To use the policy:

* Make sure the receiving mail server adds an `Authentication-Results` header with the SPF and DKIM results.
* Set "Trusted Authentication-Results server ID" to the hostname (authserv-id) of that server. When empty, the first occurrence of the header is trusted, which a sender can forge by adding its own.
* Set the behavior of the policy to anything other than "Inactive" (for example "Mark as Undesired / Temporarily keep the e-mail"). Review the results that are treated as a failure.
* See [events and triggers](events%20and%20triggers.md#sender-authentication) to be notified instead of replying to the sender.

Limitations of the policy:

* Only the configured failure results are rejected. A message without an `Authentication-Results` header, or with the result `none`, is accepted.
* An SPF or DKIM `pass` is not tied to the `From` header (no alignment). A sender can pass with its own domain while forging the `From` header of another one. This is why DMARC should be enforced on the mail server.
