<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260521
 */
 

namespace JeffreyBostoenExtensions\MailToTicket\Steps\Core;

use JeffreyBostoenExtensions\MailToTicket\Steps\{
	Base,
	PolicyBehavior
};
use JeffreyBostoenExtensions\MailToTicket\{
	eNextAction,
	ProcessingHelper,
};


/**
 * Class FinalAction. Final action: keep, move, delete, ...
 */
abstract class FinalAction extends Base {
	
	/**
	 * @inheritDoc
	 * @details This should really be the final step. After successful processing, take the final action (keep, move, delete, ...).
	 */
	public static int $iPrecedence = 9999;
	
	/**
	 * @inheritDoc
	 */
	public static function Execute() : void {
		
		$oMailBox = ProcessingHelper::GetMailBox();
		$iNextAction = eNextAction::PROCESS_MESSAGE;
		
		// Delete the source email immediately?
		if($oMailBox->Get('email_storage') === PolicyBehavior::DELETE->value) {
			
			// Remove the processed message from the mailbox.
			$iNextAction = eNextAction::DELETE_MESSAGE;
			
		}
		elseif($oMailBox->Get('email_storage') === PolicyBehavior::MOVE->value && $oMailBox->Get('target_folder') != '') {
			
			// Move the processed message to another folder.
			$iNextAction = eNextAction::MOVE_MESSAGE;
			
		}
		
		ProcessingHelper::SetNextAction($iNextAction);
		
	}
	
}
