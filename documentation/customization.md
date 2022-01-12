# Customization


## Creating new additional policies
Enforcing certain rules or simply adding your own basic logic to set Ticket info or derive a caller (Person) can be done 
by writing your own class implementing the **iPolicy** interface.

The most important things about the interface:
* implement the ```Init()``` method
* specify a ```$iPrecedence``` method.
  * This is the order in which policies are executed. Lower = first, higher  = later. 
  * Not necessary to make this unique.
  * ```$iPrecedence = 200``` is used for ticket creation/update.
* implement the ```IsCompliant()``` method.
  * true = continue processing
  * false = stop processing this email (marked as undesired), make sure to handle the next action for the email processing (do nothing, mark as error/undesired, move, ...)
  
  
The most interesting thing to know about custom attributes: if there's an attribute added to the mailbox class with the name "policyname_behavior" set to "do_nothing", the policy will not be used/inactive.  
"policyname" is an example here; it is what is defined in the policy class as ```$sPolicyId```


## Examples
As for examples, check:
* [policy.class.inc.php](../jb-itop-standard-email-synchro/core/policy.class.inc.php) - actually implemented policies 
* [unusedpolicy.class.inc.php](../jb-itop-standard-email-synchro/core/unusedpolicy.class.inc.php) - an very basic unused example and some ideas
