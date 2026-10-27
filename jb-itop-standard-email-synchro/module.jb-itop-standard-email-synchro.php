<?php
//
// iTop module definition file
//

SetupWebPage::AddModule(
	__FILE__, // Path to the current file, all other file names are relative to the directory containing this file
	'jb-itop-standard-email-synchro/3.2.261023',
	array(
		// Identification
		//
		'label' => 'Feature: Ticket Creation from E-mails',
		'category' => 'business',

		// Setup
		//
		'dependencies' => array(
			'itop-tickets/3.2.0',
			'jb-framework/3.2.260920',
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
			'src/JeffreyBostoenExtensions/MailToTicket/Steps/SenderAuthentication.php',
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
		 * @var string[] BOUNCE_POLICIES Settings prefixes of the policies which used to offer the "bounce" behaviors, with a subject and a message.
		 */
		public const BOUNCE_POLICIES = [
			'policy_attachment_forbidden_mimetype',
			'policy_mail_size_too_big',
			'policy_no_subject',
			'policy_other_email_caller_than_ticket_caller',
			'policy_other_recipients',
			'policy_sender_email_address',
			'policy_ticket_closed',
			'policy_ticket_resolved',
			'policy_ticket_unknown',
			'policy_undesired_pattern',
			'policy_unknown_caller',
		];

		/**
		 * Gets the name of the table in which the bounce settings are kept until the new objects were created.
		 *
		 * @param Config $oConfiguration Configuration.
		 *
		 * @return string
		 */
		protected static function GetBounceStagingTable(Config $oConfiguration) : string {

			return $oConfiguration->Get('db_subname').'mailinbox_bounce_migration';

		}

		/**
		 * Moves the bounce settings (subject, message) of the mailboxes to a staging table, and changes the behaviors to their non-bounce equivalent.
		 * This has to happen before the schema is updated: the bounce behaviors no longer exist as enum values.
		 * The staged settings are turned into triggers and actions by MigrateStagedBounceSettings().
		 *
		 * @param Config $oConfiguration Configuration.
		 * @param string $sPreviousVersion Previous version number of the module (empty string in case of first install).
		 *
		 * @return void
		 */
		protected static function StageBounceSettings(Config $oConfiguration, string $sPreviousVersion) : void {

			$sTable = $oConfiguration->Get('db_subname').'mailinbox_standard';

			if($sPreviousVersion === '' || !CMDBSource::IsTable($sTable)) {
				return;
			}

			$sStagingTable = static::GetBounceStagingTable($oConfiguration);

			CMDBSource::Query("
				CREATE TABLE IF NOT EXISTS `$sStagingTable` (
					id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
					mailbox_id INT NOT NULL,
					step VARCHAR(100) NOT NULL,
					subject TEXT NULL,
					notification LONGTEXT NULL,
					notify_from VARCHAR(255) NULL,
					UNIQUE KEY mailbox_step (mailbox_id, step)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
			");

			$bHasNotifyFrom = CMDBSource::IsField($sTable, 'notify_from');

			foreach(static::BOUNCE_POLICIES as $sStep) {

				$sBehaviorColumn = $sStep.'_behavior';
				if(!CMDBSource::IsField($sTable, $sBehaviorColumn)) {
					continue;
				}

				$sSubjectColumn = CMDBSource::IsField($sTable, $sStep.'_subject') ? "`{$sStep}_subject`" : 'NULL';
				$sNotificationColumn = CMDBSource::IsField($sTable, $sStep.'_notification') ? "`{$sStep}_notification`" : 'NULL';
				$sNotifyFromColumn = $bHasNotifyFrom ? '`notify_from`' : 'NULL';

				$iCount = (int)CMDBSource::QueryToArray("SELECT COUNT(*) AS cnt FROM `$sTable` WHERE `$sBehaviorColumn` IN ('bounce_delete', 'bounce_mark_as_undesired')")[0]['cnt'];
				if($iCount === 0) {
					continue;
				}

				SetupLog::Info("Feature: Ticket Creation from E-mails: Staging the bounce settings of $iCount mailbox(es) for '$sStep'.");

				CMDBSource::Query("
					INSERT IGNORE INTO `$sStagingTable` (mailbox_id, step, subject, notification, notify_from)
					SELECT id, ".CMDBSource::Quote($sStep).", $sSubjectColumn, $sNotificationColumn, $sNotifyFromColumn
					FROM `$sTable`
					WHERE `$sBehaviorColumn` IN ('bounce_delete', 'bounce_mark_as_undesired')
				");

				CMDBSource::Query("UPDATE `$sTable` SET `$sBehaviorColumn` = 'delete' WHERE `$sBehaviorColumn` = 'bounce_delete'");
				CMDBSource::Query("UPDATE `$sTable` SET `$sBehaviorColumn` = 'mark_as_undesired' WHERE `$sBehaviorColumn` = 'bounce_mark_as_undesired'");

			}

		}

		/**
		 * Creates the trigger and action that replace the bounce settings of a mailbox and a policy:
		 * a trigger (for that mailbox only) subscribed to the violation event of the policy, with an e-mail action to the sender of the message.
		 * The staged settings are removed afterwards. If something fails, they are kept so the next setup tries again.
		 *
		 * @param Config $oConfiguration Configuration.
		 *
		 * @return void
		 */
		protected static function MigrateStagedBounceSettings(Config $oConfiguration) : void {

			$sStagingTable = static::GetBounceStagingTable($oConfiguration);

			if(!CMDBSource::IsTable($sStagingTable)) {
				return;
			}

			SetupLog::Info('Feature: Ticket Creation from E-mails: Creating triggers and actions to replace the bounce settings.');

			$bAllMigrated = true;

			foreach(CMDBSource::QueryToArray("SELECT * FROM `$sStagingTable`", MYSQLI_ASSOC) as $aRow) {

				try {

					$oMailBox = MetaModel::GetObject('MailInboxStandard', (int)$aRow['mailbox_id'], false);
					if($oMailBox === null) {
						continue;
					}

					$sStep = $aRow['step'];
					$oEvent = MetaModel::GetObjectByColumn('MailProcessingEvent', 'code', $sStep.'_violation', false);
					if($oEvent === null) {
						throw new Exception("Event '{$sStep}_violation' not found.");
					}

					$sName = 'Bounce - '.$oMailBox->GetName().' - '.$sStep;

					// - The placeholders used in the (HTML) message must be HTML-escaped, as the values come from the received e-mail.
					$sSubject = trim((string)$aRow['subject']) !== '' ? $aRow['subject'] : 'Message bounced - not compliant with an enforced policy.';
					$sBody = preg_replace_callback('/\$mail->([a-z_]+)\$/', fn(array $aMatch) => '$mail->html_escape('.$aMatch[1].')$', (string)$aRow['notification']);
					$sFrom = trim((string)$aRow['notify_from']);

					/** @var ActionEmailByAddress $oAction */
					$oAction = MetaModel::NewObject('ActionEmailByAddress', [
						'name' => $sName,
						'description' => 'Created from the former bounce settings of the mailbox and policy.',
						'status' => ($sFrom === '') ? 'disabled' : 'enabled',
						'from' => $sFrom,
						'to_address' => '$mail->reply_address$',
						'subject' => $sSubject,
						'body' => $sBody,
					]);
					$oAction->DBInsert();

					/** @var TriggerMailToTicketProcessingOnEvent $oTrigger */
					$oTrigger = MetaModel::NewObject('TriggerMailToTicketProcessingOnEvent', [
						'description' => $sName,
						'mailbox_id' => $oMailBox->GetKey(),
						'include_original_message' => 'yes',
					]);

					$oActions = $oTrigger->Get('action_list');
					$oActions->AddItem(MetaModel::NewObject('lnkTriggerAction', ['action_id' => $oAction->GetKey(), 'order' => 1]));
					$oTrigger->Set('action_list', $oActions);

					$oEvents = $oTrigger->Get('events_list');
					$oEvents->AddItem(MetaModel::NewObject('lnkMailProcessingEventToTrigger', ['event_id' => $oEvent->GetKey()]));
					$oTrigger->Set('events_list', $oEvents);

					$oTrigger->DBInsert();

					CMDBSource::Query("DELETE FROM `$sStagingTable` WHERE id = ".(int)$aRow['id']);
					SetupLog::Info("Feature: Ticket Creation from E-mails: Created trigger #".$oTrigger->GetKey()." and action #".$oAction->GetKey()." for mailbox #".$oMailBox->GetKey()." ('$sStep').");

				}
				catch(Throwable $e) {
					$bAllMigrated = false;
					SetupLog::Error('Feature: Ticket Creation from E-mails: Unable to migrate the bounce settings of mailbox #'.$aRow['mailbox_id']." ('".$aRow['step']."'): ".$e->getMessage());
				}

			}

			if($bAllMigrated) {
				CMDBSource::Query("DROP TABLE IF EXISTS `$sStagingTable`");
			}

		}

		/**
		 * Handler called before creating or upgrading the database schema
		 * @param $oConfiguration Config The new configuration of the application
		 * @param $sPreviousVersion string Previous version number of the module (empty string in case of first install)
		 * @param $sCurrentVersion string Current version number of the module
		 *
		 * @since 20191123-2008
		 */
		public static function BeforeDatabaseCreation(Config $oConfiguration, $sPreviousVersion, $sCurrentVersion) {

			// - The bounce settings are replaced by triggers. Read them while the columns still have their old values.
				static::StageBounceSettings($oConfiguration, $sPreviousVersion);

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

			// - Create the triggers and actions that replace the bounce settings.
				static::MigrateStagedBounceSettings($oConfiguration);

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
