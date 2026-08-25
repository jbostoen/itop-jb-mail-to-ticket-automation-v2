<?php
//
// iTop module definition file
//

SetupWebPage::AddModule(
	__FILE__, // Path to the current file, all other file names are relative to the directory containing this file
	'jb-itop-standard-email-synchro/3.2.260711',
	array(
		// Identification
		//
		'label' => 'Feature: Ticket Creation from E-mails',
		'category' => 'business',

		// Setup
		//
		'dependencies' => array(
			'itop-tickets/3.2.0',
			'jb-framework/2.6.191216',
			'jb-email-synchro/2.6.190110',
			'jb-news/3.2.0',
			// no other dependency is listed, for backward 1.x compatibility... though this module uses implicitely the Ticket's derived classes...
		),
		'installer' => 'StandardEmailSynchroInstaller',
		'mandatory' => false,
		'visible' => true,

		// Components
		//
		'datamodel' => array(
			'model.jb-itop-standard-email-synchro.php',
			'src/JeffreyBostoenExtensions/MailToTicket/EventListener.php',
			// Core processing.
			'src/JeffreyBostoenExtensions/MailToTicket/Steps/Base.php',
			'src/JeffreyBostoenExtensions/MailToTicket/Steps/Core/AttachmentCriteria.php',
			'src/JeffreyBostoenExtensions/MailToTicket/Steps/Core/CreateOrUpdateTicket.php',
			'src/JeffreyBostoenExtensions/MailToTicket/Steps/Core/FinalAction.php',
			'src/JeffreyBostoenExtensions/MailToTicket/Steps/Core/FindCaller.php',
			'src/JeffreyBostoenExtensions/MailToTicket/Steps/Core/FindAdditionalContacts.php',
			'src/JeffreyBostoenExtensions/MailToTicket/Steps/Core/SaveReferences.php',
			'src/JeffreyBostoenExtensions/MailToTicket/Steps/Core/EntryProcessor/Base.php',
			'src/JeffreyBostoenExtensions/MailToTicket/Steps/Core/EntryProcessor/DefaultHTML.php',
			'src/JeffreyBostoenExtensions/MailToTicket/Steps/Core/EntryProcessor/DefaultText.php',
			// Extra.
			'src/JeffreyBostoenExtensions/MailToTicket/Steps/AttachmentForbiddenMimeType.php',
			'src/JeffreyBostoenExtensions/MailToTicket/Steps/AutoReply.php',
			'src/JeffreyBostoenExtensions/MailToTicket/Steps/LimitMailSize.php',
			'src/JeffreyBostoenExtensions/MailToTicket/Steps/MatchByInReplyToOrReferences.php',
			'src/JeffreyBostoenExtensions/MailToTicket/Steps/NonDeliveryReport.php',
			'src/JeffreyBostoenExtensions/MailToTicket/Steps/NoSubject.php',
			'src/JeffreyBostoenExtensions/MailToTicket/Steps/OtherEmailCallerThanTicketCaller.php',
			'src/JeffreyBostoenExtensions/MailToTicket/Steps/OtherRecipients.php',
			'src/JeffreyBostoenExtensions/MailToTicket/Steps/RemoveTitlePatterns.php',
			'src/JeffreyBostoenExtensions/MailToTicket/Steps/SenderEmailAddress.php',
			'src/JeffreyBostoenExtensions/MailToTicket/Steps/TicketClosed.php',
			'src/JeffreyBostoenExtensions/MailToTicket/Steps/TicketResolved.php',
			'src/JeffreyBostoenExtensions/MailToTicket/Steps/UndesiredTitlePatterns.php',
			'src/JeffreyBostoenExtensions/MailToTicket/Steps/UnknownTicketReference.php',
			'src/JeffreyBostoenExtensions/MailToTicket/Steps/UpdateCallerAttributes.php',
		),
		'webservice' => array(
			
		),
		'data.struct' => array(
			// add your 'structure' definition XML files here,
		),
		'data.sample' => array(
			// add your sample data XML files here,
		),
		
		// Documentation
		//
		'doc.manual_setup' => '', // hyperlink to manual setup documentation, if any
		'doc.more_information' => '', // hyperlink to more information, if any 

		// Default settings
		//
		'settings' => array(
			// Module specific settings go here, if any
			'inline_image_max_width' => 500, // Maximum width (in px) for displaying inline images
		),
	)
);


if (!class_exists('StandardEmailSynchroInstaller')) {

	// Module installation handler
	//
	class StandardEmailSynchroInstaller extends ModuleInstallerAPI {

		/**
		 * Handler called before creating or upgrading the database schema
		 * @param $oConfiguration Config The new configuration of the application
		 * @param $sPreviousVersion string Previous version number of the module (empty string in case of first install)
		 * @param $sCurrentVersion string Current version number of the module
		 *
		 * @since 20191123-2008
		 */
		public static function BeforeDatabaseCreation(Config $oConfiguration, $sPreviousVersion, $sCurrentVersion) {

			if($sPreviousVersion == '' || version_compare($sPreviousVersion, '2.7.231208', '>')) {
				return;
			}

			
			if($sPreviousVersion != '' && version_compare($sPreviousVersion, '2.7.231208', '<')) {
			
				// 20191229-1549: renamed policy
				$sTableName = 'mailinbox_standard';
				self::MoveColumnInDB($sTableName, 'policy_attachment_image_dimensions_min_width', $sTableName, 'step_attachment_criteria_image_min_width');
				self::MoveColumnInDB($sTableName, 'policy_attachment_image_dimensions_max_width', $sTableName, 'step_attachment_criteria_image_max_width');
				self::MoveColumnInDB($sTableName, 'policy_attachment_image_dimensions_min_height', $sTableName, 'step_attachment_criteria_image_min_height');
				self::MoveColumnInDB($sTableName, 'policy_attachment_image_dimensions_max_height', $sTableName, 'step_attachment_criteria_image_max_height');

				
			}
			
			if($sPreviousVersion != '' && version_compare($sPreviousVersion, '2.6.191123', '<')) {
			
				// 20191123-2011: renamed enum values, indicating they're fallbacks and doing a specific action; even if there's only one fallback.
				self::RenameEnumValueInDB('MailInboxStandard', 'policy_forbidden_attachments_behavior', 'fallback', 'fallback_ignore_forbidden_attachments');
				self::RenameEnumValueInDB('MailInboxStandard', 'policy_no_subject_behavior', 'fallback', 'fallback_default_subject');
				self::RenameEnumValueInDB('MailInboxStandard', 'policy_ticket_resolved_behavior', 'fallback', 'fallback_reopen');
				self::RenameEnumValueInDB('MailInboxStandard', 'policy_ticket_closed_behavior', 'fallback', 'fallback_reopen');
				self::RenameEnumValueInDB('MailInboxStandard', 'policy_unknown_caller_behavior', 'fallback', 'fallback_create_person');
				self::RenameEnumValueInDB('MailInboxStandard', 'policy_other_recipients_behavior', 'ignore_all_contacts', 'fallback_ignore_other_contacts');
				self::RenameEnumValueInDB('MailInboxStandard', 'policy_other_recipients_behavior', 'add_all_contacts', 'fallback_add_other_contacts');
				self::RenameEnumValueInDB('MailInboxStandard', 'policy_other_recipients_behavior', 'add_existing_contacts', 'fallback_add_existing_other_contacts');
				self::RenameEnumValueInDB('MailInboxStandard', 'policy_remove_pattern_behavior', 'remove', 'fallback_remove');
				self::RenameEnumValueInDB('MailInboxStandard', 'policy_remove_pattern_behavior', 'ignore', 'do_nothing'); // Should actually be translated to policy_ignore_pattern
				
				// 20191229-1549: renamed policy
				self::MoveColumnInDB('MailInboxStandard', 'policy_forbidden_attachments_behavior', 'MailInboxStandard', 'policy_attachment_forbidden_mimetype_behavior');
				self::MoveColumnInDB('MailInboxStandard', 'policy_forbidden_attachments_subject', 'MailInboxStandard', 'policy_attachment_forbidden_mimetype_subject');
				self::MoveColumnInDB('MailInboxStandard', 'policy_forbidden_attachments_notification', 'MailInboxStandard', 'policy_attachment_forbidden_mimetype_notification');
				self::MoveColumnInDB('MailInboxStandard', 'policy_forbidden_attachments_mimetypes', 'MailInboxStandard', 'policy_attachment_forbidden_mimetype_mimetypes');
				
			}
			
			if($sPreviousVersion != '' && version_compare($sPreviousVersion, '2.6.210219', '<')) {
				CMDBSource::Query('
					UPDATE mailinbox_standard
					SET policy_unknown_caller_behavior = \'mark_as_undesired\'
					WHERE policy_unknown_caller_behavior = \'do_nothing\'
				');
			}


		}

		/**
		 * Handler called after creating or upgrading the database schema
		 * @param $oConfiguration Config The new configuration of the application
		 * @param $sPreviousVersion string Previous version number of the module (empty string in case of first install)
		 * @param $sCurrentVersion string Current version number of the module
		 */
		public static function AfterDatabaseCreation(Config $oConfiguration, $sPreviousVersion, $sCurrentVersion) {

			if($sPreviousVersion == '' || version_compare($sPreviousVersion, '3.2.260711', '>')) {

				SetupLog::Info('Feature: Ticket Creation from E-mails: No database changes needed.');
				return;

			}

			if($sPreviousVersion != '' && version_compare($sPreviousVersion, '3.2.260711', '<=')) {

				// 3.2.260711: notify_errors_to changed from an AttributeEmailAddress (free-text address) to an
				// AttributeOQL (query returning Person objects). At this point, the schema was already migrated,
				// so the raw (old) e-mail address value has to be read directly via SQL, not through the ORM.
				SetupLog::Info('Feature: Ticket Creation from E-mails: Migrating notify_errors_to (e-mail address) to a Person-matching OQL query.');

				$sTableName = 'mailinbox_standard';
				$sMigrationOrgName = 'Mail to Ticket Migration';
				$iMigrationOrgId = null; // Resolved lazily, only if a new Person actually needs to be created.

				$aRows = CMDBSource::QueryToArray("SELECT id, notify_errors_to FROM $sTableName WHERE notify_errors_to != ''");

				foreach($aRows as $aRow) {

					$iMailboxId = $aRow['id'];
					$sEmail = trim($aRow['notify_errors_to']);

					if($sEmail === '') {
						continue;
					}

					// Skip values which already look like an OQL query (in case this migration runs more than once).
					if(preg_match('/^\s*SELECT\s/i', $sEmail) === 1) {
						continue;
					}

					SetupLog::Info("Feature: Ticket Creation from E-mails: Migrating notify_errors_to for mailbox #$iMailboxId ('$sEmail').");

					// Look for an existing Person with this e-mail address.
					$oSearch = DBObjectSearch::FromOQL_AllData('SELECT Person WHERE email = :email');
					$oSet = new DBObjectSet($oSearch, [], ['email' => $sEmail]);
					$oPerson = $oSet->Fetch();

					if($oPerson !== false) {

						$iPersonId = $oPerson->GetKey();
						SetupLog::Info("Feature: Ticket Creation from E-mails: Found existing Person #$iPersonId for '$sEmail'.");

					}
					else {

						// No existing Person with this e-mail address: create one, under a dedicated
						// Organization so migrated contacts are easy to find and re-assign afterwards.
						if($iMigrationOrgId === null) {

							$oOrgSearch = DBObjectSearch::FromOQL_AllData('SELECT Organization WHERE name = :name');
							$oOrgSet = new DBObjectSet($oOrgSearch, [], ['name' => $sMigrationOrgName]);
							$oOrg = $oOrgSet->Fetch();

							if($oOrg !== false) {

								$iMigrationOrgId = $oOrg->GetKey();
								SetupLog::Info("Feature: Ticket Creation from E-mails: Using existing Organization #$iMigrationOrgId ('$sMigrationOrgName') for migrated Persons.");

							}
							else {

								$oOrg = new Organization();
								$oOrg->Set('name', $sMigrationOrgName);
								$oOrg->DBInsert();

								$iMigrationOrgId = $oOrg->GetKey();
								SetupLog::Info("Feature: Ticket Creation from E-mails: Created Organization #$iMigrationOrgId ('$sMigrationOrgName') for migrated Persons.");

							}

						}

						$oPerson = new Person();
						$oPerson->Set('org_id', $iMigrationOrgId);
						$oPerson->Set('first_name', '-');
						$oPerson->Set('name', $sEmail);
						$oPerson->Set('email', $sEmail);
						$oPerson->DBInsert();

						$iPersonId = $oPerson->GetKey();
						SetupLog::Info("Feature: Ticket Creation from E-mails: Created new Person #$iPersonId for '$sEmail'.");

					}

					$sOQL = "SELECT Person WHERE id = $iPersonId";

					$sUpdateQuery = "UPDATE $sTableName SET notify_errors_to = " . CMDBSource::Quote($sOQL) . " WHERE id = $iMailboxId";
					SetupLog::Info($sUpdateQuery);
					CMDBSource::Query($sUpdateQuery);

				}

			}

		}

	}

}
