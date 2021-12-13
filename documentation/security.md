# Security recommendations

## Mailbox credentials

* Make sure to use modern password recommendations when it comes to complexity and length.

## Notifications

Be careful on how you set up e-mail notifications by the trigger "When updated by mail".  

Typically this is used for two sorts of notifications:
* to inform agents a new reply has been added (from a customer)
* to inform a caller and possibly related contacts that the ticket was updated because someone sent an e-mail with the same ticket reference number.

In the last case, you might assume this would either be the original caller or an already related contact.  
However, an attacker could theoretically guess the pattern and spoof an agent's reply - perhaps requesting the recipients to perform a malicious action.  
Another condition would of course be that this attacker managed to become a known contact in iTop (perhaps auto-creation of callers is enabled).  

It would be highly recommended to **NOT** include any info from the (usually public) log to which e-mails are added in the notifications.


