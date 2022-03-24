# Customization


## Creating new additional policies
Enforcing certain rules or simply adding your own basic logic to set Ticket info or derive a caller (Person) can be done 
by writing your own class implementing the **iPolicy** interface or even better: by extending the **Policy** class.

The most important things about implementing the interface:

* Add the necessary methods defined in the interface (if they're not inherited from ```Policy```)
* Specify a ```$iPrecedence``` value.
  * This is the order in which policies are executed. Lower = first, higher  = later. 
  * Not necessary to make this unique. 
  * Some default precedences of importance:
    * ```$iPrecedence = 110``` is used for finding the caller (if not found prior)
    * ```$iPrecedence = 200``` is used for ticket creation/update.
* Implement the ```IsCompliant()``` method.
  * Return boolean
    * True = continue processing
    * False = stop processing this email, make sure to handle the next action for the email processing (do nothing, mark as error/undesired, move, ...)
  
  
The most interesting thing to know about custom attributes:  
If there's an attribute added to the mailbox class with the name ```policyname_behavior``` set to ```do_nothing```, the policy will be processed but is not supposed to perform any actions.  
It's meant for debugging and showing verbose output of what would happen, without doing it.  
```inactive``` means it will not be processed at all.

```policyname``` is an example here; it is what is defined in the policy class as ```$sPolicyId```

Use the methods to set/get info (for instance: ```static::GetMail()```, rather than directly manipulating the static properties.


## Examples

Check:
* [policy.class.inc.php](../jb-itop-standard-email-synchro/core/policy.class.inc.php) - actually implemented policies 
* [unusedpolicy.class.inc.php](../jb-itop-standard-email-synchro/core/unusedpolicy.class.inc.php) - an very basic unused example and some ideas
