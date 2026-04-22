<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260421
 */
 

namespace JeffreyBostoenExtensions\MailToTicket\Steps\Core;

use JeffreyBostoenExtensions\MailToTicket\Steps\{
	Base,
	PolicyBehavior
};
use JeffreyBostoenExtensions\MailToTicket\{
	ProcessingHelper
};

// iTop Mail.
use EmailProcessor;


/**
 * Class FinalAction. Final action: keep, move, delete, ...
 */
abstract class FinalAction extends Base {
	
	/**
	 * @inheritDoc
	 * @details This should really be the final step. After successful processing, take the final action (keep, move, delete, ...).
	 */
	public static $iPrecedence = 9999;
	
	/**
	 * @inheritDoc
	 */
	public static function Execute() : void {
		
		$oMailBox = ProcessingHelper::GetMailBox();
		$iNextAction = EmailProcessor::PROCESS_MESSAGE;
		
		// Delete the source email immediately?
		if($oMailBox->Get('email_storage') == PolicyBehavior::DELETE->value) {
			
			// Remove the processed message from the mailbox.
			$iNextAction = EmailProcessor::DELETE_MESSAGE;
			
		}
		elseif($oMailBox->Get('email_storage') == PolicyBehavior::MOVE->value && $oMailBox->Get('target_folder') != '') {
			
			// Move the processed message to another folder.
			$iNextAction = EmailProcessor::MOVE_MESSAGE;
			
		}
		
		$oMailBox->SetNextAction($iNextAction);
		
	}
	
}
