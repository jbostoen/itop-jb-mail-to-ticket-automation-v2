<?php

/**
 * @copyright   Copyright (c) 2019-2024 Jeffrey Bostoen
 * @license     https://www.gnu.org/licenses/gpl-3.0.en.html
 * @version     2.7.240524
 *
 * A demo of a class which could store e-mails automatically.
 */
 
namespace jb_itop_extensions\mail_to_ticket;

/**
 * Class StepSaveEmailsToPath. Step to save incoming emails as .EML file to a (hardcoded) directory.
 *
 * Note: this is NOT in use by default.
 */
abstract class StepSaveEmailsToPath extends Step {
	
	/**
	 * @inheritDoc
	 *
	 * @details Depending on the use case, set a low number (always export the email) or high number (export only if certain policies have been processed and email was compliant)
	 */
	public static $iPrecedence = 1;
	
	/**
	 * @inheritDoc
	 */
	public static $sXMLSettingsPrefix = 'policy_example_save_emails_to_path';
	
	/**
	 * @inheritDoc
	 */
	public static function Execute() {
		
		/** @var \RawEmailMessage $oEmail */
		$oRawEmail = static::GetRawMail();
		
		// Add some logic for file name. Mind time zones!
		$sDateTime = strtotime($oRawEmail->GetHeader('date'));
		$sFolder = 'C:/temp/'.date('Ymd', $sDateTime);
		$sMessageId = $oRawEmail->GetMessageId();
		
		// Forbidden on Windows
		$aForbiddenChars = array_merge(
			array_map('chr', range(0,31)),
			array('<', '>', ':', '\'', '/', '\\', '|', '?', '*')
		);
		
		$sSanitizedMessageId = str_replace($aForbiddenChars, '', $sMessageId);
		$sSanitizedMessageId = str_replace('.', '_', $sSanitizedMessageId);
		
		if(file_exists('C:/temp/') == true) {
			if(file_exists($sFolder) == false) {
				static::Trace('.. Create folder: '.$sFolder);
				mkdir($sFolder);
			}
			$sFilePath = $sFolder.'/'.$sSanitizedMessageId.'.eml';
			static::Trace('.. Save e-mail to '.$sFilePath);
			$oRawEmail->SaveToFile($sFilePath);
		}
		else {
			static::Trace('.. Unable to export: folder C:/temp does not exist');
		}
		
		
	}

}


/*

abstract class PolicyAttachmentVirusCheck extends Step {
	// could be an example implementing ClamAv, similar to what's mentioned in MailInboxBase
}

abstract class PolicyStatistics extends Step {
	// could be an example of keeping track of statistics. Number of processed e-mails per inbox etc.
}

*/

