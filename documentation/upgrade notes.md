# Upgrade notes
Backward compatibility is something that is important.
However, some upgrades might break things because technology or use cases change.

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

