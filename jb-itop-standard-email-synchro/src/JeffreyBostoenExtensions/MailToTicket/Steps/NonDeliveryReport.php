<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260421
 */
 

namespace JeffreyBostoenExtensions\MailToTicket\Steps;

use JeffreyBostoenExtensions\MailToTicket\{
	ProcessingHelper
};


// iTop internals.
use DBObjectSearch;
use DBOBjectSet;



/**
 * Class NonDeliveryReport.  
 * A policy to ignore delivery failure messages, and mark a person as inactive upon mail delivery failure.
 */
abstract class NonDeliveryReport extends Base {
	
	/**
	 * @inheritDoc
	 */
	public static $iPrecedence = 0;
	
	/**
	 * @inheritDoc
	 */
	public static $sXMLSettingsPrefix = 'policy_non_delivery_report';
	
	/**
	 * @inheritDoc
	 */
	public static function Execute() : void {
		
		$oEmail = ProcessingHelper::GetMail();
		
		$bMarkAsInactive = (static::GetStepSetting('mark_caller_as_inactive') == 'yes');
		$sBehavior = static::GetStepSetting('behavior');
		
		foreach($oEmail->aAttachments as $aAttachment) {
			
			if($aAttachment['mimeType'] == 'message/delivery-status') {
				
				$sContent = $aAttachment['content'];
				
				// Status code: https://www.rfc-editor.org/rfc/rfc3463
				preg_match('/Status: ([\d]{1,})\.([\d]{1,})\.([\d]{1,})/', $sContent, $aStatus);
				$sCode = $aStatus[1].'.'.$aStatus[2].'.'.$aStatus[3];
			
				// Check if this is a permanent failure.
				if($aStatus[1] == '5') {
					
					static::Trace('.. Found a permanent Non-Delivery Report - Status code '.$sCode);
					
					// Log any permantent failure. This may also show cases in the logs that aren't handled yet; or that should not be handled.
					static::Trace('.. '.preg_replace(static::NEWLINE_REGEX, '$0.. ', $sContent));
					
					// Try to be more certain.
					// This logic may need to be improved in the future.
					// Key phrases include: unknown recipient, unknown user.
					// Another that may be more doubtful: mailbox unavailable
					// Do NOT simply rely on 550, it's too generic and doesn't always mean the recipient's mailbox no longer exists.
					if(
						preg_match('/(unknown recipient|unknown user|mailbox unavailable)/', $sContent, $aKeywords) ||
						
						// 5.1.1 = Bad destination mailbox address
						// 5.1.2 = Bad destination system address
						// 5.1.3 = Bad destination mailbox address syntax
						preg_match('/5\.1\.(1|2|3)/', $sCode)
					) {
						
						// List phrase
						static::Trace('.. Keywords: '.$aKeywords[1]);
 
						// Mark as inactive?
						if($bMarkAsInactive == true) {
						
							// Build a list of e-mail addresses of recipients to whom delivery failed.
							preg_match('/Final-recipient: RFC822; (.*?@.*)/', $sContent, $aRecipient);
							$sRecipient = $aRecipient[1];
							
							static::Trace('.. Recipient: '.$sRecipient);
							
							// Lookup if there is a person whose e-mail is in the list.
							
								// email field of Person object
								$oSetPerson = new DBObjectSet(DBObjectSearch::FromOQL('SELECT Person WHERE email LIKE :email'), [], [
									'email' => $sRecipient
								]);
								
								if($oSetPerson->Count() == 0) {
									
									static::Trace('.. No Person found.');
									
								}
								
								/** @var Person $oPerson Person(s) in iTop with email set to that of the recipient. */
								while($oPerson = $oSetPerson->Fetch()) {
									
									if($sBehavior == PolicyBehavior::DO_NOTHING->value) {
									
										static::Trace('.. Simulation (Do nothing). Would mark Person::'.$oPerson->GetKey().' as "inactive" based on attribute value.');
										
									}
									else {
											
										static::Trace('.. Marking Person::'.$oPerson->GetKey().' as "inactive" based on attribute value.');
										$oPerson->Set('status', 'inactive');
										$oPerson->DBUpdate();
										
									}
									
								}
								
						}
								
						// Handle behavior.
						static::HandleViolation();
					
					}
					
				}
				
			}
			
		}
		
		
		
	}

}


