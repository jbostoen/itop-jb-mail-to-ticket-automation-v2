<?php

/**
 * @copyright   Copyright (c) 2019-2026 Jeffrey Bostoen
 * @license     https://www.gnu.org/licenses/gpl-3.0.en.html
 * @version     3.2.260711
 *
 * A demo of a class which could store e-mails automatically.
 */
 
namespace JeffreyBostoenExtensions\MailToTicket\Steps\UnusedExample;

use JeffreyBostoenExtensions\MailToTicket\Steps\Base;
use JeffreyBostoenExtensions\MailToTicket\ProcessingHelper;

use RawEmailMessage;

/**
 * Class SaveEmailsToPath.  
 * Saves incoming emails as .EML file to a (hardcoded) directory.
 *
 * Note: this is NOT in use by default.
 */
abstract class SaveEmailsToPath extends Base {
	
	/**
	 * @inheritDoc
	 *
	 * @details Depending on the use case, set a low number (always export the email) or high number (export only if certain policies have been processed and email was compliant)
	 */
	public static int $iPrecedence = 1;
	
	/**
	 * @inheritDoc
	 */
	public static string $sXMLSettingsPrefix = 'policy_example_save_emails_to_path';
	
	/**
	 * @inheritDoc
	 */
	public static function Execute() : void {
		
		/** @var RawEmailMessage $oEmail */
		$oRawEmail = ProcessingHelper::GetRawMail();
		
		// Add some logic for file name. Mind time zones!
		$sDateTime = strtotime($oRawEmail->GetHeader('date'));
		if($sDateTime === false) {
			// Missing or malformed "Date" header: fall back to the current time rather than the epoch.
			$sDateTime = time();
		}
		// Better expose this for configuration somewhere.
		$sFolder = 'C:/temp/'.date('Ymd', $sDateTime);
		$sMessageId = $oRawEmail->GetMessageId();
		if($sMessageId === '') {
			// Missing "Message-ID" header: fall back to a unique identifier to avoid filename collisions.
			$sMessageId = uniqid('no-message-id-', true);
		}
		
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

