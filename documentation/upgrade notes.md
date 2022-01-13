# Upgrade notes
Backward compatibility is something that is important.
However, some upgrades might break things because technology or use cases change.

## Upgrading from before 2.7.220123?
A new behavior has been added to lots of policies: "Inactive".  
This behavior is now the default for a lot of new policies when configuring a new inbox.  
The policies with a behavior set to "Inactive", are not processed.  
The policies with a behavior set to "Do nothing", keep their original purpose: they are processed and might give warnings, but they should not change anything to the ticket or to the e-mail in the inbox.  


## Upgrading from before 2.7.211110?
A new policy has been added which makes it possible to force that the sender from an e-mail must have the same e-mail address 
as the e-mail address of the ticket caller.

Make sure to evaluate whether you want to enable this policy (by default it's set to "Do nothing", which means it is inactive).  
You can disable it by modifying the properties of each e-mail inbox that has been configured.

This policy is a security measure.


## Upgrading from before 2.6.201219?
For consistency, some settings have been renamed.
Values will be copied; but old settings are not cleaned up automatically!

* 'introductory-patterns' is now 'introductory_patterns'. Default settings are now empty (contains some examples).
* 'html-tags-to-remove' is now 'html_tags_to_remove'. Default settings are now empty (contains some examples).
* 'multiline-delimiter-patterns' is now 'multiline_delimiter_patterns'. Default settings are now empty (contains some examples).
* 'delimiter-patterns' is now 'delimiter_patterns'. Default settings are now empty (contains some examples).

## Upgrading from before 2.6.201029?
* IMAP options are now specified per inbox. They are copied from the configuration file if they exist during the upgrade process.
* The 'imap_options' setting can be removed from the configuration file after upgrading.
* The 'maximum_email_size' setting can be removed from the configuration file after upgrading.

## Upgrading from before 2.6.191229? (a.k.a from 'version 1')

**Automatically taken care of**
* Some enum values have changed. Generic 'fallback' values have been renamed to more specific ones, even if it's the only fallback option.
* Some fields have been renamed. (policy_forbidden_attachments_* -> policy_attachment_forbidden_mimetypes_* )

**Manual check required:**
* placeholders
  * the 'mail->*' placeholders have changed and now end with $ (more in line with iTop's other placeholders)
* remove/ignore title patterns (where email is processed, so do not confuse it with undesired title patterns)
  * split into two separate policies for more flexibility
  
* configuration
  * settings related to image sizes should no longer be in the configuration, but in the mailbox settings.
  * settings with a '-' in their name: replace '-' with '_' (done for consistency)

**Settings (iTop Configuration) that are deprecated (but working):**
This extension was forked from Combodo's Mail to Ticket Automation. 
Some features are implemented in a different way. 
Hence some settings no longer make sense and are being deprecated.

* exclude_attachment_types

