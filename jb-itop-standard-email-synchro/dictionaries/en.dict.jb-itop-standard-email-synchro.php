<?php
/**
 * Localized data
 *
 * @copyright Copyright (c) 2010-2025 Combodo SARL
 * @license	http://opensource.org/licenses/AGPL-3.0
 *
 * This file is part of iTop.
 *
 * iTop is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * iTop is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with iTop. If not, see <http://www.gnu.org/licenses/>
 */

Dict::Add('EN US', 'English', 'English', array(

	// Dictionary entries go here
	'Class:MailInboxStandard' => 'IMAP Mail Inbox',
	'Class:MailInboxStandard+' => 'Source of incoming e-mails',
	'Class:MailInboxStandard/Attribute:behavior' => 'Behavior',
	'Class:MailInboxStandard/Attribute:behavior/Value:create_only' => 'Create new Tickets',
	'Class:MailInboxStandard/Attribute:behavior/Value:update_only' => 'Update existing Tickets',
	'Class:MailInboxStandard/Attribute:behavior/Value:both' => 'Create or Update Tickets',

	'Class:MailInboxStandard/Attribute:email_storage' => 'After processing the e-mail',
	'Class:MailInboxStandard/Attribute:email_storage+' => 'Action to take after processing the e-mail. For best performance: if archiving is desired, it is recommended to move successfully processed e-mails to another folder.',
	'Class:MailInboxStandard/Attribute:email_storage/Value:keep' => 'Keep it in the same folder',
	'Class:MailInboxStandard/Attribute:email_storage/Value:delete' => 'Delete it immediately',
	'Class:MailInboxStandard/Attribute:email_storage/Value:move' => 'Move to another folder',

	'Class:MailInboxStandard/Attribute:target_class' => 'Ticket Class',
	'Class:MailInboxStandard/Attribute:target_class/Value:Incident' => 'Incident',
	'Class:MailInboxStandard/Attribute:target_class/Value:UserRequest' => 'User Request',
	'Class:MailInboxStandard/Attribute:target_class/Value:Change' => 'Change',
	'Class:MailInboxStandard/Attribute:target_class/Value:Change+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:RoutineChange' => 'RoutineChange',
	'Class:MailInboxStandard/Attribute:target_class/Value:RoutineChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:NormalChange' => 'NormalChange',
	'Class:MailInboxStandard/Attribute:target_class/Value:NormalChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:EmergencyChange' => 'EmergencyChange',
	'Class:MailInboxStandard/Attribute:target_class/Value:EmergencyChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:Problem' => 'Problem',
	'Class:MailInboxStandard/Attribute:target_class/Value:Problem+' => '',
	'Class:MailInboxStandard/Attribute:debug_trace' => 'Debug trace',
	'Class:MailInboxStandard/Attribute:debug_trace+' => '',
	'Class:MailInboxStandard/Attribute:target_folder' => 'Target folder',
	'Class:MailInboxStandard/Attribute:target_folder+' => 'The e-mail will be moved (IMAP protocol) to this target folder after being processed. Mind to update the setting for "After processing the e-mail" to "Move to another folder".',
	
	'Class:MailInboxStandard/Attribute:ticket_default_values' => 'Default values for new Ticket',
	'Class:MailInboxStandard/Attribute:ticket_default_title' => 'Default title (if subject is empty)',
	'Class:MailInboxStandard/Attribute:title_pattern+' => 'Pattern to match in the subject',
	'Class:MailInboxStandard/Attribute:title_pattern' => 'Title Pattern',
	'Class:MailInboxStandard/Attribute:title_pattern?' => 'Use PCRE syntax, including starting and ending delimiters to specify what the ticket reference (pattern) looks like so e-mails can be linked to tickets.',

	'Class:MailInboxStandard/Attribute:title_pattern_ignore_patterns' => 'Ignore patterns in subject (regex patterns, one per line)', 
	
	'Class:MailInboxStandard/Attribute:stimuli' => 'Stimuli to apply',
	'Class:MailInboxStandard/Attribute:stimuli+' => 'Apply a stimulus when the ticket is in a given state',
	'Class:MailInboxStandard/Attribute:stimuli?' => 'A list of state_code:stimulus_code (one per line) to define the stimulus to apply (only after updating an existing ticket), for the given state of the ticket. This is useful for example to automatically reassign a ticket which is in the state “pending”. Use the format <state_code>:<stimulus_code>',


	'Class:MailInboxStandard/Attribute:trace' => 'Debug trace',
	'Class:MailInboxStandard/Attribute:trace/Value:yes' => 'Yes',
	'Class:MailInboxStandard/Attribute:trace/Value:no' => 'No',
	  
	   
	'Class:MailInboxStandard/Attribute:debug_log' => 'Debug Log',
	
	'Class:MailInboxStandard/Attribute:error_behavior' => 'Behavior',
	'Class:MailInboxStandard/Attribute:error_behavior/Value:delete' => 'Delete the message from the mailbox',
	'Class:MailInboxStandard/Attribute:error_behavior/Value:mark_as_error' => 'Mark as Error', 
	'Class:MailInboxStandard/Attribute:notify_errors_to' => 'Forward e-mails (in error) To Address',
	'Class:MailInboxStandard/Attribute:notify_from' => 'Mail From Address',
	
	'Class:MailInboxStandard/Attribute:mail_aliases' => 'Mail Aliases',
	'Class:MailInboxStandard/Attribute:mail_aliases+' => 'Mail Aliases: one per line. Regex patterns are allowed.',

	'Class:MailInboxStandard/Attribute:oauth_provider' => 'Oauth provider',
	'Class:MailInboxStandard/Attribute:oauth_provider+' => '',
	'Class:MailInboxStandard/Attribute:oauth_client_id' => 'OAuth client',
	'Class:MailInboxStandard/Attribute:oauth_client_id+' => '',
	
	// Policy: Attachments - Criteria
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_width' => 'Min. width (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_width+' => 'Minimum image width (px). Must be at least 1. Images which are too small, will not be processed.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_width' => 'Max. width (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_width+' => 'Maximum image width (px). Set to 0 to accept any width. If the php-gd extension is installed, larger images will be resized. Otherwise, they will not be processed.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_height' => 'Min. height (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_height+' => 'Minimum height (px). Must be at least 1.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_height' => 'Max. height (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_height+' => 'Maximum height (px). Set to 0 to accept any height. If the php-gd extension is installed, larger images will be resized. Otherwise, they will not be processed.',

	'Class:MailInboxStandard/Attribute:step_attachment_criteria_exclude_mimetypes' => 'Ignore MIME types',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_exclude_mimetypes+' => 'Attachments of these MIME types will not be processed. Specify one per line.',
	
	// Policy: mail size too large
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior' => 'Behavior on violation',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:bounce_delete' => 'Bounce to sender and delete',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:bounce_mark_as_undesired' => 'Bounce to sender and mark as undesired',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:delete' => 'Delete the message from the mailbox',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:do_nothing' => 'Do nothing',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:inactive' => 'Inactive',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:mark_as_undesired' => 'Mark as Undesired / Temporarily keep the e-mail',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_subject' => 'Bounce subject',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_notification' => 'Bounce message',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_max_size_MB' => 'Max size (MB)',
	'Error:MailInboxStandard/Attribute:policy_mail_size_too_big_max_size_MB+' => 'Maximum size of e-mail and its attachments. Larger e-mails will not be processed. Set to 0 to disable.',
	
	// Policy: attachment - forbidden MIME type
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior' => 'Behavior on violation',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:fallback_ignore_forbidden_attachments' => 'Fallback: ignore forbidden attachments',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:bounce_delete' => 'Bounce to sender and delete',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:bounce_mark_as_undesired' => 'Bounce to sender and mark as undesired',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:delete' => 'Delete the message from the mailbox',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:do_nothing' => 'Do nothing',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:inactive' => 'Inactive',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:mark_as_undesired' => 'Mark as Undesired / Temporarily keep the e-mail',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_subject' => 'Bounce subject',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_notification' => 'Bounce message',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_mimetypes' => 'MIME Types (one per line)',
	
	// Policy: no subject
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior' => 'Behavior on violation',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:fallback_default_subject' => 'Fallback: use default subject',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:bounce_delete' => 'Bounce to sender and delete',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:bounce_mark_as_undesired' => 'Bounce to sender and mark as undesired',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:delete' => 'Delete the message from the mailbox',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:do_nothing' => 'Do nothing',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:inactive' => 'Inactive',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:mark_as_undesired' => 'Mark as Undesired / Temporarily keep the e-mail',
	'Class:MailInboxStandard/Attribute:policy_no_subject_subject' => 'Bounce subject',
	'Class:MailInboxStandard/Attribute:policy_no_subject_notification' => 'Bounce message',
	'Class:MailInboxStandard/Attribute:policy_no_subject_default_value' => 'Default subject',
	
	// Policy: unknown caller
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior' => 'Behavior on violation',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:fallback_create_person' => 'Fallback: create person',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:bounce_delete' => 'Bounce to sender and delete',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:bounce_mark_as_undesired' => 'Bounce to sender and mark as undesired',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:delete' => 'Delete the message from the mailbox',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:mark_as_undesired' =>  'Mark as Undesired / Temporarily keep the e-mail',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_subject' => 'Bounce subject',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_notification' => 'Bounce message',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_default_values' => 'Default values for new person (one per line, example: org_id:1)',
	
	// Policy: other recipients
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior' => 'Behavior on violation',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_add_existing_other_contacts' => 'Fallback: link only existing contacts',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_add_other_contacts' => 'Fallback: always link contact, create contact if necessary',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_ignore_other_contacts' => 'Fallback: ignore all other contacts',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:bounce_delete' => 'Bounce to sender and delete',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:bounce_mark_as_undesired' => 'Bounce to sender and mark as undesired',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:delete' => 'Delete the message from the mailbox',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:do_nothing' => 'Do nothing',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:inactive' => 'Inactive',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:mark_as_undesired' => 'Mark as Undesired / Temporarily keep the e-mail',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_subject' => 'Bounce subject',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_notification' => 'Bounce message',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_default_values' => 'Default values for new person (one per line, example: org_id:1)',
	
	// Policy: closed ticket
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior' => 'Behavior on violation',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior+' => 'Hint: by default closed tickets can not be reopened. This requires datamodel changes.',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:fallback_reopen' => 'Fallback: reopen ticket',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:bounce_delete' => 'Bounce to sender and delete',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:bounce_mark_as_undesired' => 'Bounce to sender and mark as undesired',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:delete' => 'Delete the message from the mailbox',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:do_nothing' => 'Do nothing',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:inactive' => 'Inactive',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:mark_as_undesired' => 'Mark as Undesired / Temporarily keep the e-mail',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_subject' => 'Bounce subject',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_notification' => 'Bounce message', 
	
	// Policy: resolved ticket
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior' => 'Behavior on violation',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:fallback_reopen' => 'Fallback: reopen ticket',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:bounce_delete' => 'Bounce to sender and delete',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:bounce_mark_as_undesired' => 'Bounce to sender and mark as undesired',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:delete' => 'Delete the message from the mailbox',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:do_nothing' => 'Do nothing',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:inactive' => 'Inactive',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:mark_as_undesired' => 'Mark as Undesired / Temporarily keep the e-mail',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_subject' => 'Bounce subject',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_notification' => 'Bounce message', 
	 
	// Policy: unknown ticket
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior' => 'Behavior on violation', 
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:bounce_delete' => 'Bounce to sender and delete',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:bounce_mark_as_undesired' => 'Bounce to sender and mark as undesired',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:delete' => 'Delete the message from the mailbox',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:do_nothing' => 'Do nothing',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:inactive' => 'Inactive',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:mark_as_undesired' => 'Mark as Undesired / Temporarily keep the e-mail',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_subject' => 'Bounce subject',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_notification' => 'Bounce message', 
	
	// Policy: undesired title patterns
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior' => 'Behavior on violation', 
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:delete' => 'Delete the message from the mailbox',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:bounce_delete' => 'Bounce to sender and delete',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:bounce_mark_as_undesired' => 'Bounce to sender and mark as undesired',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:do_nothing' => 'Do nothing',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:inactive' => 'Inactive',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:mark_as_undesired' => 'Mark as Undesired / Temporarily keep the e-mail',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_subject' => 'Bounce subject',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_notification' => 'Bounce message', 
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_patterns' => 'Undesired patterns in subject (regex patterns, one per line)', 
	
	
	// Policy: remove parts of title
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior' => 'Remove patterns from subject', 
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior/Value:do_nothing' => 'Do nothing',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior/Value:inactive' => 'Inactive',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior/Value:fallback_remove' => 'Remove part(s) from subject',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_patterns' => 'Patterns to remove from subject (regex patterns, one per line)', 
	
	// Policy: Caller must be same as original ticket caller
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior' => 'Behavior on violation',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:delete' => 'Delete the message from the mailbox',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:bounce_delete' => 'Bounce to sender and delete',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:bounce_mark_as_undesired' => 'Bounce to sender and mark as undesired',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:do_nothing' => 'Do nothing',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:inactive' => 'Inactive',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:mark_as_undesired' => 'Mark as Undesired / Temporarily keep the e-mail',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_subject' => 'Bounce subject',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_notification' => 'Bounce message', 
	
	// Policy: Auto reply
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior' => 'Behavior on violation',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:delete' => 'Delete the message from the mailbox',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:do_nothing' => 'Do nothing',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:inactive' => 'Inactive',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:mark_as_undesired' => 'Mark as Undesired / Temporarily keep the e-mail',
	
	// Policy: Non Delivery Report
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior' => 'Behavior on violation',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:delete' => 'Delete the message from the mailbox',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:do_nothing' => 'Do nothing',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:inactive' => 'Inactive',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:mark_as_undesired' => 'Mark as undesired',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive' => 'Mark caller as inactive',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive+' => 'The caller will be marked as inactive, if the mail delivery failure seems to be permanent and there is high confidence the recipient is no longer reachable through this e-mail address.',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive/Value:yes' => 'Yes',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive/Value:no' => 'No',

	// Policy: Sender Email Address
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior' => 'Behavior on violation',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:bounce_delete' => 'Bounce to sender and delete',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:bounce_mark_as_undesired' => 'Bounce to sender and mark as undesired',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:delete' => 'Delete the message from the mailbox',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:do_nothing' => 'Do nothing',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:inactive' => 'Inactive',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:mark_as_undesired' => 'Mark as undesired',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior_subject' => 'Bounce subject',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior_notification' => 'Bounce message', 
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_patterns' => 'Patterns',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_patterns+' => 'Any e-mail where the sender\'s e-mail address matches one of the defined regex patterns (one per line), will be considered a violation. Ignore patterns in subject.',

	
	
	// Headers
	'MailInbox:Server' => 'Mailbox Configuration',
	'MailInbox:Behavior' => 'Behavior on Incoming e-mails',
	'MailInbox:Errors' => 'E-mails in error', 
	'MailInbox:Settings' => 'Settings', 
	
	// Steps
	'MailInbox:StepAttachmentCriteria' => 'Embedded e-mail images',
	'MailInbox:PolicyMailSize' => 'Mail Size',
	'MailInbox:PolicyAttachmentForbiddenMimeType' => 'Attachment - Forbidden MIME type',
	'MailInbox:PolicyAttachmentIgnoredMimeType' => 'Attachment - Ignored MIME type',
	'MailInbox:PolicyUndesiredPattern' => 'Undesired title patterns',
	'MailInbox:PolicyRemovePattern' => 'Remove unwanted title patterns',
	'MailInbox:PolicyIgnorePattern' => 'Ignore unwanted title patterns',
	'MailInbox:PolicyResolvedTicket' => 'Resolved tickets',
	'MailInbox:PolicyClosedTicket' => 'Closed tickets',
	'MailInbox:PolicyUnknownTicket' => 'Unknown tickets',
	'MailInbox:PolicyNoSubject' => 'No subject',
	'MailInbox:PolicyUnknownCaller' => 'Unknown caller',
	'MailInbox:PolicyOtherRecipients' => 'Other recipients specified in To: or CC:',
	'MailInbox:PolicyBounceOtherEmailCallerThanTicketCaller' => 'Limit accepted e-mail replies to original ticket caller\'s e-mail address',
	'MailInbox:PolicyAutoReply' => 'Auto reply',
	'MailInbox:PolicyNonDeliveryReport' => 'Non Delivery Reports',
	'MailInbox:PolicySenderEmailAddress' => 'Block senders using e-mail address patterns',
	
	'Menu:MailInboxes' => 'Incoming E-mail Inboxes',
	'Menu:MailInboxes+' => 'Configuration of Inboxes to scan for incoming e-mails',
	 
	'MailInboxStandard:DebugTrace' => 'Debug Trace',
	'MailInboxStandard:DebugTraceNotActive' => 'Activate the debug trace on this Inbox to see a detailed log of what happens.',
	
	'MailPolicy:CreateOrUpdateTicket:NoDescriptionProvided' => 'No description provided',
	
	// OAuth2
	'UI:OAuthEmailSynchro:Wizard:ResultConf:Panel:Title' => 'Create a Mailbox',
	'UI:OAuthEmailSynchro:Wizard:ResultConf:Panel:Description' => 'Create a new Mailbox to fetch e-mails from a remote mail provider using this OAuth connection as authentication method',
	'UI:OAuthEmailSynchro:Wizard:ResultConf:Panel:CreateNewMailbox' => 'Create a new mailbox',
	'UI:OAuthEmailSynchro:Error:UnknownVendor' => 'OAuth provider %1$s does not exist',
	
	// lnkEmailUidToTicket
	'Class:lnkEmailUidToTicket' => 'Link Email UID / Ticket',
	'Class:lnkEmailUidToTicket/Attribute:message_uid' => 'Message UID',
	'Class:lnkEmailUidToTicket/Attribute:ticket_id' => 'Ticket ID',
	'Class:lnkEmailUidToTicket/Attribute:mailbox_id' => 'Mailbox ID',
	'Class:lnkEmailUidToTicket/UniquenessRule:unique_message_uid_and_mailbox_id_and_ticket_id' => 'The combination of mailbox ID, message UID and ticket ID must be unique.',
	
	
));
