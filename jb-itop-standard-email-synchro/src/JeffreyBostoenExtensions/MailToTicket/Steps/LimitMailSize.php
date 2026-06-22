<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260521
 */
 

namespace JeffreyBostoenExtensions\MailToTicket\Steps;

use JeffreyBostoenExtensions\MailToTicket\{
	ProcessingHelper
};

use utils;

/**
 * Class LimitMailSize.  
 * A policy to prevent large e-mail messages from being processed.  
 * It considers the size of the raw email message, which includes all attachments and is the actual size of the email as it was received by the mail server.
 */
abstract class LimitMailSize extends Base {
	
	/**
	 * @inheritDoc
	 */
	public static int $iPrecedence = 10;
	
	/**
	 * @inheritDoc
	 */
	public static string $sXMLSettingsPrefix = 'policy_mail_size_too_big';
		
	/**
	 * @inheritDoc
	 */
	public static function Execute() : void {
		
		$oRawEmail = ProcessingHelper::GetRawMail();
		
		// Checking if mail size is not too big
		$iMaxSizeMB = static::GetStepSetting('max_size_MB');
		
		if($iMaxSizeMB > 0) {
		
			$iMailSize = $oRawEmail->GetSize();
			$iLimitMailSize = utils::ConvertToBytes($iMaxSizeMB.'M');
			
			if($iMailSize > $iLimitMailSize) {
				
				// Mail size too big
				static::Trace('Undesired: mail size too big (%1$s bytes), limit is %2$s MB.', $iMailSize, $iMaxSizeMB);
				static::HandleViolation();
				
				// No fallback
				
				// Stop processing any further!
				return;
				
			}
			
		}
		
	}
	
}
