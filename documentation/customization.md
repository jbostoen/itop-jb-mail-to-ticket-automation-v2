# Customization

## Creating new processing steps

Adding your own logic to can be done by extending the **Step** class.


* Specify a ```$iPrecedence``` value.
  * This is the order in which policies are executed. Lower = first, higher  = later. 
  * Not necessary to make this unique. 
  * Some default precedences of importance:
    * ```$iPrecedence = 110``` is used for finding the caller (if not found prior)
    * ```$iPrecedence = 200``` is used for ticket creation/update.
    * ```$iPrecedence = 9999``` is used for the final step (only if processing was successful). Here an action such as keeping the e-mail, moving or deleting it is determined.
	
* Implement the ```Execute()``` method.


**Other pointers**

Use the methods to set/get info (for instance: ```static::GetMail()```, rather than directly manipulating the static properties.



Enforcing certain rules means that at some point you might want to interrupt the processing of this particular e-mail.  
If there's an attribute added to the mailbox class with the name ```step_prefix_behavior``` set to ```do_nothing```, the policy will be processed but is not supposed to perform any actions.  
It's meant for debugging and showing verbose output of what would happen, without doing it.  
```inactive``` means it will not be processed at all.

```step_prefix``` is a placeholder in the example above; it is the prefix defined in the step class (```$sXMLSettingsPrefix``` property).


## Examples

Check:
* [steps.php](../jb-itop-standard-email-synchro/core/steps.php) - Actually implemented steps. 
* [custom_step_example.php](../jb-itop-standard-email-synchro/core/custom_step_example.php) - An very basic unused example and some ideas.

