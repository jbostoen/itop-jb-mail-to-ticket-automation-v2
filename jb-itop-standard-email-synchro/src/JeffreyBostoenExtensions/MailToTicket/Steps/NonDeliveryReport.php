<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260711
 */
 

namespace JeffreyBostoenExtensions\MailToTicket\Steps;

use JeffreyBostoenExtensions\MailToTicket\{
	Helper,
	ProcessingHelper
};


// iTop internals.
use DBObjectSearch;
use DBOBjectSet;
use EmailMessage;
use EmailReplica;
use RawEmailMessage;
use Exception;



/**
 * Class NonDeliveryReport.  
 * A policy to ignore delivery failure messages, and mark a person as inactive upon mail delivery failure.
 */
abstract class NonDeliveryReport extends Base {
	
	/**
	 * @inheritDoc
	 */
	public static int $iPrecedence = 0;
	
	/**
	 * @inheritDoc
	 */
	public static string $sXMLSettingsPrefix = 'policy_non_delivery_report';
	
	/**
	 * @inheritDoc
	 */
	public static function Execute() : void {

		$oEmail = ProcessingHelper::GetMail();
		$oRawEmail = ProcessingHelper::GetRawMail();
		
		$bMarkAsInactive = (static::GetStepSetting('mark_caller_as_inactive') == 'yes');
		$sBehavior = static::GetStepSetting('behavior');
		
		foreach($oEmail->aAttachments as $aAttachment) {
			
			// MIME type tokens are case-insensitive (RFC 2045); compare accordingly, like
			// AttachmentForbiddenMimeType::GetEffectiveMimeTypes() does for the same reason.
			if(strtolower(trim($aAttachment['mimeType'])) === 'message/delivery-status') {
				
				$sContent = $aAttachment['content'];
				
				// Status code: https://www.rfc-editor.org/rfc/rfc3463
				if(preg_match('/Status: ([\d]{1,})\.([\d]{1,})\.([\d]{1,})/', $sContent, $aStatus) !== 1) {
					// No (recognizable) status code in this delivery-status part; nothing more to do with it.
					continue;
				}
				$sCode = $aStatus[1].'.'.$aStatus[2].'.'.$aStatus[3];

				// Check if this is a permanent failure.
				if($aStatus[1] == '5') {
					
					static::Trace('.. Found a permanent Non-Delivery Report - Status code '.$sCode);
					
					// Log any permantent failure. This may also show cases in the logs that aren't handled yet; or that should not be handled.
					static::Trace('.. '.preg_replace(Helper::NEWLINE_REGEX, '$0.. ', $sContent));
					
					// Try to be more certain.
					// This logic may need to be improved in the future.
					// Key phrases include: unknown recipient, unknown user.
					// Another that may be more doubtful: mailbox unavailable
					// Do NOT simply rely on 550, it's too generic and doesn't always mean the recipient's mailbox no longer exists.
					if(
						preg_match('/(unknown recipient|unknown user|mailbox unavailable)/i', $sContent, $aKeywords) ||

						// 5.1.1 = Bad destination mailbox address
						// 5.1.2 = Bad destination system address
						// 5.1.3 = Bad destination mailbox address syntax
						preg_match('/5\.1\.(1|2|3)/', $sCode)
					) {

						// List phrase, if the keyword pattern is what matched (it may not be: the status code alone can also satisfy this condition).
						if(array_key_exists(1, $aKeywords)) {
							static::Trace('.. Keywords: '.$aKeywords[1]);
						}
 
						if($bMarkAsInactive) {

							if(preg_match('/Final-Recipient: RFC822; (.*?@.*)/i', $sContent, $aRecipient) !== 1) {

								static::Trace('.. Could not determine the "Final-Recipient" from the Non-Delivery Report.');

							}
							else {

								$sRecipient = $aRecipient[1];
								static::Trace('.. Recipient: '.$sRecipient);

								$oTicket = ProcessingHelper::GetTicket();

								if($oTicket !== null) {

									// A matched ticket only proves this message references a Message-ID this
									// mailbox itself issued for that ticket; it says nothing about whether the
									// "Final-Recipient" claim below is genuine. Require the outer bounce to have
									// passed SPF/DKIM too, consistent with the no-ticket branch below.
									$bTrusted = !$oRawEmail->HasFailedAuthentication()
										&& (strcasecmp($sRecipient, (string) $oTicket->Get('caller_id->email')) === 0);

								}
								else {
									$bTrusted = static::EmbeddedMessageIsGenuineBounce($oEmail, $oRawEmail);
								}

								if(!$bTrusted) {

									static::Trace('.. Could not verify this Non-Delivery Report relates to a notification this mailbox actually sent; not marking anyone as inactive based on it.');

								}
								else {

									// email field of Person object. Exact match: $sRecipient comes from an
									// attacker-controlled "Final-Recipient" header, so a LIKE pattern here
									// would let unescaped SQL wildcards (%, _) match unintended Persons.
									$oSetPerson = new DBObjectSet(DBObjectSearch::FromOQL('SELECT Person WHERE email = :email'), [], [
										'email' => $sRecipient
									]);

									if($oSetPerson->Count() === 0) {

										static::Trace('.. No Person found.');

									}

									/** @var Person $oPerson Person(s) in iTop with email set to that of the recipient. */
									while($oPerson = $oSetPerson->Fetch()) {

										if($sBehavior === PolicyBehavior::DO_NOTHING->value) {

											static::Trace('.. Simulation (Do nothing). Would mark Person::'.$oPerson->GetKey().' as "inactive" based on attribute value.');

										}
										else {

											static::Trace('.. Marking Person::'.$oPerson->GetKey().' as "inactive" based on attribute value.');
											$oPerson->Set('status', 'inactive');
											$oPerson->DBUpdate();

										}

									}

								}

							}

						}

						// Handle behavior. Return: don't let a second matching delivery-status part in the
						// same message trigger another (e.g. bounce) violation for it.
						static::HandleViolation();
						return;

					}
					
				}
				
			}
			
		}
		
		
		
	}

	/**
	 * Whether this bounce embeds an original message (RFC 3464's message/rfc822 or
	 * text/rfc822-headers part) whose own Message-ID carries this installation's HMAC signature
	 * (see EmailReplica::MakeMessageId()/IsValidMessageIdSignature(), #95) - proof this iTop
	 * instance itself generated and sent that message, and not just attacker-suppliable content
	 * inside this bounce.
	 *
	 * A prior implementation matched the embedded message's From: header against this mailbox's
	 * own address/aliases instead. That is not a valid proxy for authenticity: the embedded
	 * message/rfc822 part is MIME content fully controlled by whoever sent the *outer* bounce
	 * message, regardless of whether the outer message itself passed SPF/DKIM - anyone with any
	 * real, authenticated mail account could fabricate an embedded part claiming to be From this
	 * mailbox's own address.
	 *
	 * @param EmailMessage $oEmail
	 * @param RawEmailMessage $oRawEmail The raw, enclosing bounce message (not the embedded part).
	 * @return bool
	 */
	private static function EmbeddedMessageIsGenuineBounce(EmailMessage $oEmail, RawEmailMessage $oRawEmail) : bool {

		// A failed SPF/DKIM check on the enclosing bounce message means it did not genuinely
		// originate the way it claims to; don't even bother inspecting its embedded content.
		if($oRawEmail->HasFailedAuthentication()) {
			return false;
		}

		foreach($oEmail->aAttachments as $aAttachment) {

			if(!in_array(strtolower(trim($aAttachment['mimeType'])), ['message/rfc822', 'text/rfc822-headers'], true)) {
				continue;
			}

			try {
				$oEmbedded = new RawEmailMessage((string) $aAttachment['content']);
			}
			catch(Exception $e) {
				continue;
			}

			$sEmbeddedMessageId = $oEmbedded->GetMessageId();

			if(
				preg_match('/^<iTop_(.+)_([0-9]+)_([a-f0-9]+)_.+@.+openitop\.org>$/', $sEmbeddedMessageId, $aMatches) === 1
				&& EmailReplica::IsValidMessageIdSignature($aMatches[1], $aMatches[2], $aMatches[3])
			) {
				return true;
			}

		}

		return false;

	}

}


