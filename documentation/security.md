# Security recommendations

## Mailbox credentials

* Make sure to use modern password recommendations when it comes to complexity and length.

## Notifications

Be careful on how you set up e-mail notifications linked to the trigger "When updated by mail".  

Typically this is used for two sorts of notifications:
* to inform agents a new reply has been added (from a customer)
* to inform a caller and possibly related contacts that the ticket was updated because someone sent an e-mail with the same ticket reference number.

In the last case, you might assume this would either be the original caller or an already related contact.  
However, an attacker could theoretically guess the pattern and spoof an agent's reply - perhaps requesting the recipients to perform a malicious action.  
Another condition would of course be that this attacker managed to become a known contact in iTop (perhaps auto-creation of callers is enabled).  

It may be highly recommended to **NOT** include any info from the (usually public) log in the notifications sent to the customer.  
This however implies that the customers would need to use the portal to view the answer (where they still might be tricked).  

In this fork, an additional policy is by included to mitigate this risk.  
If enabled, an incoming e-mail will only be appended in the ticket log if it originates from the same caller.  
It is however not enabled by default since this is a new behavior. 

