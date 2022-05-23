<?php
//
// iTop module definition file
//

SetupWebPage::AddModule(
	__FILE__, // Path to the current file, all other file names are relative to the directory containing this file
	'jb-email-synchro/2.7.220523',
	array(
		// Identification
		'label' => 'Mail to Tickets Automation (core)',
		'category' => 'business',
		// Setup
		'dependencies' => array(
		),
		'mandatory' => false,
		'visible' => true,
		'installer' => 'EmailSynchroInstaller',
		// Components
		'datamodel' => array(
			'classes/autoload.php',
			'model.jb-email-synchro.php',
		),
		'dictionary' => array(
		),
		'data.struct' => array(
		),
		'data.sample' => array(
		),
		
		// Documentation
		'doc.manual_setup' => '', // No manual installation required
		'doc.more_information' => '', // None
		
		// Default settings
		'settings' => array(
		
			// Note: some of these settings are still here from the original Combodo version.
			// Some of them no longer have any effect.
			
			'notify_errors_to' => '', // mandatory to track errors not handled by the email processing module
			'notify_errors_from' => '', // mandatory as well (can be set at the same value as notify_errors_to)
			'debug' => false, // Set to true to turn on debugging
			'periodicity' => 30, // interval at which to check for incoming emails (in s)
			'retention_period' => 24, // number of hours the email replica is kept when it is no longer seen in the mailbox
			'body_parts_order' => 'text/html,text/plain', // Order in which to read the parts of the incoming emails
			'big_files_dir' => '',

			// Some patterns which delimit the previous message in case of a Reply
			// The "new" part of the message is the text before the pattern
			// Add your own multi-line patterns (use \\R for a line break)
			// These patterns depend on the mail client/server used... feel free to add your own discoveries to the list
			'multiline_delimiter_patterns' => array(
				'/\\RFrom: .+\\RSent: .+\\R/m', // Outlook English
				'/\\R_+\\R/m', // A whole line made only of underscore characters
				'/\\RDe : .+\\R\\R?Envoyé : /m', // Outlook French, HTML and rich text
				'/\\RDe : .+\\RDate d\'envoi : .+\\R/m', // Outlook French, plain text
				'/\\R-----Message d\'origine-----\\R/m',
			),
			
			'use_message_id_as_uid' => true, // Do NOT change this unless you known what you are doing! Despite being 'false' in Combodo's Mail to Ticket Automation (3.0.5), it works better if set to true on IMAP connections.
			
			// These settings existed with a - instead of _ 
			// To make them more consistent:
			
			// "New part" of e-mail
			// Lines to be removed just above the 'new part' in a reply-to message... add your own patterns below
			'introductory_patterns' => array(
				// '/^le .+ a écrit :$/i', // Thunderbird French
				// '/^on .+ wrote:$/i', // Thunderbird English
				// '|^[0-9]{4}/[0-9]{1,2}/[0-9]{1,2} .+:$|', // Gmail style
			),
			
			// "New part" of e-mail
			// Default tags to remove: array of tag_name => array of class names.
			// In Combodo's version, this is enabled by default (as html-tags-to-remove).
			// However, then it also applies to forwarded messages, which is often an unwanted effect.
			'html_tags_to_remove' => array(
				// 'blockquote' => array(),
				// 'div' => array('gmail_quote', 'moz-cite-prefix'),
				// 'pre' => array('moz-signature'),
			),
		
			'delimiter_patterns' => array(
				'/^>.*$/' => false, // Old fashioned mail clients: continue processing the lines, each of them is preceded by >
			),
			
			'undesired_purge_delay' => 7, // Warning: Combodo's version had an inconsistent undesired-purge-delay setting. Renamed. Interval (in days) after which undesired emails are deleted in the mailbox
			
			// Deprecated settings:
			'images_minimum_size' => '100x20', // Images smaller than these dimensions will be ignored (signatures...)
			'images_maximum_size' => '', // Images bigger than these dimensions will be resized before uploading into iTop,
		),
	)
);

if (!class_exists('EmailSynchroInstaller')) {

	// Module installation handler
	//
	class EmailSynchroInstaller extends ModuleInstallerAPI {

		/**
		 * Handler called before the creation/update of the database schema
		 *
		 * @param \Config $oConfiguration The new configuration of the application
		 * @param \String $sPreviousVersion Previous version number of the module (empty string in case of first install)
		 * @param \String $sCurrentVersion Current version number of the module
		 *
		 * @returns \Config
		 */
		public static function BeforeWritingConfig(Config $oConfiguration) {
			
			return $oConfiguration;
			
		}
		
		/**
		 * Handler called after the creation/update of the database schema
		 * @param $oConfiguration Config The new configuration of the application
		 * @param $sPreviousVersion string Previous version number of the module (empty string in case of first install)
		 * @param $sCurrentVersion string Current version number of the module
		 */
		public static function AfterDatabaseCreation(Config $oConfiguration, $sPreviousVersion, $sCurrentVersion) {
			
			// For each email sources, update email replicas by setting mailbox_path to source.mailbox where mailbox_path is null

			// Preparing mailboxes search
			$oSearch = new DBObjectSearch('MailInboxBase');

			// Retrieving definition of attribute to update
			$sTableName = MetaModel::DBGetTable('EmailReplica');

			$UidlAttDef = MetaModel::GetAttributeDef('EmailReplica', 'uidl');
			$sUidlColName = $UidlAttDef->Get('sql');

			$oMailboxAttDef = MetaModel::GetAttributeDef('EmailReplica', 'mailbox_path');
			$sMailboxColName = $oMailboxAttDef->Get('sql');

			// 2020-10-29: IMAP options were moved to individual mailbox settings and will always be prefilled
			$bUpgradeOptionsIMAP = ($sPreviousVersion != '' && version_compare($sPreviousVersion, '2.6.201029', '<'));
			
			// Looping on inboxes to update
			$oSet = new DBObjectSet($oSearch);
			while($oInbox = $oSet->Fetch()) {
				$sUpdateQuery = "UPDATE $sTableName SET $sMailboxColName = " . CMDBSource::Quote($oInbox->Get('mailbox')) . " WHERE $sUidlColName LIKE " . CMDBSource::Quote($oInbox->Get('login') . '_%') . " AND $sMailboxColName IS NULL";
				
				$iRet = CMDBSource::Query($sUpdateQuery); // Throws an exception in case of error
				
				
				if($bUpgradeOptionsIMAP == true && trim($oInbox->Get('imap_options') == '')) {
					$aOptionsIMAP = MetaModel::GetModuleSetting('jb-email-synchro', 'imap_options', array('imap', 'ssl', 'novalidate-cert'));
					$oInbox->Set('imap_options', implode(PHP_EOL, $aOptionsIMAP));
					$oInbox->DBUpdate();
				}
				
			}
			
			// Workaround for BeforeWritingConfig() not having $sPreviousVersion
			if($sPreviousVersion != '' && version_compare($sPreviousVersion, '2.6.201219', '<=')) {
				
				// In previous versions, these parameters were not named in a consistent way. Rename.
				$aSettings = array(
					'html_tags_to_remove', 
					'introductory_patterns',
					'multiline_delimiter_patterns',
					'delimiter_patterns'
				);
				
				$sTargetEnvironment = 'production';
				$sConfigFile = APPCONF.$sTargetEnvironment.'/'.ITOP_CONFIG_FILE;
				$oExistingConfig = new Config($sConfigFile);
				
				foreach($aSettings as $sSetting) {
					
					$aDeprecatedSettingValue = $oExistingConfig->GetModuleSetting('jb-email-synchro', str_replace('_', '-', $sSetting), null);
					if($aDeprecatedSettingValue !== null) {
						$oExistingConfig->SetModuleSetting('jb-email-synchro', $sSetting, $aDeprecatedSettingValue);
					}
					
				}
				
				// Update existing configuration PRIOR to iTop installation actually processing this.
				$oExistingConfig->WriteToFile();
				
			}
			
		}
		
	}

}
