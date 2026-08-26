<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260711
 */
 

namespace JeffreyBostoenExtensions\MailToTicket\Steps;

use JeffreyBostoenExtensions\MailToTicket\{
	eNextAction,
	Helper,
	ProcessingHelper
};

// iTop.
use MetaModel;
use EmailContact;
use EmailMessage;
use EmailProcessor;
use EmailSource;
use MailInboxStandard;
use RawEmailMessage;
use Ticket;


/**
 * Interface iStep defines what the classes implementing steps should look like.
 */
interface iStep {
	
}


/**
 * Enum PolicyBehavior defines the behavior of a policy when it is violated.  
 * Note: Fallbacks are NOT listed here, as they're usually very specific; and are unlikely to be handled in the regular handling.
 */
enum PolicyBehavior : string {
	
	case BOUNCE_DELETE = 'bounce_delete';
	case BOUNCE_MARK_AS_UNDESIRED = 'bounce_mark_as_undesired';
	case DELETE = 'delete';
	case DO_NOTHING = 'do_nothing';
	case MARK_AS_UNDESIRED = 'mark_as_undesired';
	case MOVE = 'move';
	case MARK_AS_ERROR = 'mark_as_error';
	case SKIP_FOR_NOW = 'skip_for_now';
	case ABORT_ALL_FURTHER_PROCESSING = 'abort_all_further_processing';

}


/**
 * Class Step. An abstract class defining a step (an action to take in the processing of the e-mail).
 */
abstract class Base implements iStep {

	/**
	 * @var int $iPrecedence It's not necessary that this number is unique; but when all steps are listed; they will be sorted ascending (intended to make sure some checks run first; before others).
	 */
	public static int $iPrecedence = 20;
	
	/**
	 * @var string $sXMLSettingsPrefix (XML) settings prefix for step.
	 */
	public static string $sXMLSettingsPrefix = 'step_generic';
	
	/**
	 * @var string $sEmailIndex Index in the e-mail source. Note: name is experimental; use methods instead to set/get
	 */
	public static ?string $iEmailIndex = null;
	
	/**
	 * Execution of what needs to happen in this step. This is an individual method; so it can easily be overridden without doing common things defined in the Init() method.
	 *
	 * @return void
	 */
	public static function Execute() : void {
		
	}
	
	/**
	  * Gets the step's XML settings prefix. In the datamodel, especially for policies, settings can be defined. Most common one would be something like 'example_step_behavior'.
	  *
	  * @return string
	 */
	public static function GetXMLSettingsPrefix() : string {
		
		return static::$sXMLSettingsPrefix;
		
	}
	
	/**
	 * Shorthand to obtain a setting configured in the maibox properties for a specific step.
	 *
	 * @return string
	 */
	public static function GetStepSetting(string $sSetting) : string {
	
		$oMailBox = ProcessingHelper::GetMailBox();
		return $oMailBox->Get(static::GetXMLSettingsPrefix().'_'.$sSetting);
		
	}
	
	
	/**
	  * Gets the step's precedence.
	  *
	  * @return int
	 */
	public static function GetPrecedence() : int {
		
		return static::$iPrecedence;
		
	}
    

	/**
	 * Replace email placeholders in a string.
	 * 
	 * @param string $sString Input string.
	 * @param array $aExtraPlaceholders Optional: extra place holders.
	 *
	 * @details Also exposes some properties which are not likely to be useful (body_format) at any time, but who knows?
	 *
	 * @return String String where the placeholders are filled in.
	 */
	public static function ReplaceMailPlaceholders(string $sString, array $aExtraPlaceholders = []) {
		
		$oEmail = ProcessingHelper::GetMail();
		
		$aParams = [
			'mail->uidl' => $oEmail->sUIDL,
			'mail->message_id' => $oEmail->sMessageId,
			'mail->subject' => $oEmail->sSubject,
			'mail->caller_email' => $oEmail->sCallerEmail,
			'mail->caller_email_suffix' => explode('@', $oEmail->sCallerEmail, 2)[1] ?? '',
			'mail->caller_name' => $oEmail->sCallerName,
			'mail->recipient' => $oEmail->sRecipient,
			'mail->date' => $oEmail->sDate,
			'mail->body_text_plain' => strip_tags($oEmail->sBodyText),
			'mail->body_text'  => $oEmail->sBodyText,
			'mail->body_format' => $oEmail->sBodyFormat
		];

		if($oEmail->GetSender() !== null) {
			$aParams['sender->object()'] = $oEmail->GetSender();
		}
		
		$aParams = array_merge($aParams, $aExtraPlaceholders);
		
		// Extend. This is to allow for both the original parameter and the HTML-encoded version of it.
		$aParamsExtended = [];
		foreach($aParams as $sParam => $sValue) {
			$aParamsExtended[$sParam] = $sValue;
			$aParamsExtended[htmlentities($sParam)] = $sValue;
		}
		
		return MetaModel::ApplyParams($sString, $aParamsExtended);

	}


	/**
	 * Parses a newline-separated "attcode:value" setting (e.g. a mailbox's default_values field)
	 * into an attcode => value array ready for ProcessingHelper::InitObjectFromDefaultValues(),
	 * applying mail placeholders to each value.
	 *
	 * @param string $sRawValues
	 * @param array $aExtraPlaceholders Optional: extra placeholders, forwarded to ReplaceMailPlaceholders().
	 *
	 * @return array
	 */
	public static function ParseAttributeValues(string $sRawValues, array $aExtraPlaceholders = []) : array {

		$aLines = Helper::SplitByLine($sRawValues);
		$aValues = [];

		foreach($aLines as $sLine) {
			if(preg_match('/^([^:]+):(.*)$/', $sLine, $aMatches)) {
				$sAttCode = trim($aMatches[1]);
				$sValue = trim($aMatches[2]);
				$sValue = static::ReplaceMailPlaceholders($sValue, $aExtraPlaceholders);
				$aValues[$sAttCode] = $sValue;
			}
		}

		return $aValues;

	}


	/**
	 * Trace function used to log information about the e-mail processing.
	 *
	 * @param string $sMessage The message.
	 * @param mixed ...$args
	 *
	 * @return void
	 */
	public static function Trace($sMessage, ...$args) : void {

		$sMessage = (count($args) > 0) ? sprintf($sMessage, ...$args) : $sMessage;
        $oMailBox = ProcessingHelper::GetMailBox();
		$oMailBox->Trace($sMessage);
				
		
	}
	
	
	/**
	 * Returns the e-mail addresses of all recipients in the original e-mail.
	 *
	 * @return string[]
	 */
	public static function GetRecipientAddresses() : array {

		/** @var RawEmailMessage $oRawEmail Raw e-mail message. */
		$oRawEmail = ProcessingHelper::GetRawMail();

		/** @var EmailContact[] $aRecipients An array of recipients. */
		$aRecipients = array_merge($oRawEmail->GetTo(), $oRawEmail->GetCc());

		/** @var string[] $aAddresses An array of e-mail addresses. */
		$aAddresses = [];

		foreach($aRecipients as $aRecipient ) {
			$aAddresses[] = $aRecipient->GetEmailAddress();
		}
		return $aAddresses;

	}
	
	/**
	 * Returns an array containing the e-mail aliases of the mailbox, including the primary e-mail address.
	 *
	 * @return string[]
	 */
	public static function GetMailBoxAliases() : array {
		
		$oMailBox = ProcessingHelper::GetMailBox();
		
		// Retrieve the configured aliases.
		$sMailBoxAliases = $oMailBox->Get('mail_aliases');
		$aMailBoxAliases = (trim($sMailBoxAliases) == '' ? [] : Helper::SplitByLine($sMailBoxAliases));

		// Also retrieve the actual mailbox.
		$aMailBoxAliases[] = $oMailBox->Get('login');
		
		return array_unique($aMailBoxAliases);
		
	}
	
	/**
	 * Actions executed when the message does not comply with a policy.
	 * The default method informs the caller that the email was rejected.
	 *
	 * @return void
	 */
	public static function HandleViolation() : void {
		
			
		// Policy violations have a typical way of handling.
		// The behavior is - besides some feature-specific fallbacks - usually one of the following:
		// - bounce_delete -> bounce and delete the message
		// - bounce_mark_as_undesired -> bounce and marks the message as undesired
		// - delete -> delete the message
		// - PolicyBehavior::DO_NOTHING->value -> great, lazy. For testing purposes. To actually see if policies are processed or log additional info without doing anything in the end.
		// - mark_as_undesired -> stays in the mailbox for a few days
		// - some sort of fallback -> doesn't matter here

		$oEmail = ProcessingHelper::GetMail();
		$oMailBox = ProcessingHelper::GetMailBox();
		$sBehavior = static::GetStepSetting('behavior');
		
		static::Trace('.. Policy violated. Behavior: '.$sBehavior);
		
		// First check if email notification must be sent to caller (bounce message)
		switch($sBehavior) {
		
			// Generic cases.
			case PolicyBehavior::BOUNCE_DELETE->value:
			case PolicyBehavior::BOUNCE_MARK_AS_UNDESIRED->value:
			
				
				$oRawEmail = $oEmail->oRawEmail;
			
				// Inform the caller who doesn't follow guidelines.		
				// User education and communicating the guidelines is great; but sometimes policies need to be enforced.
				$sTo = $oEmail->sCallerEmail;
				$sFrom = $oMailBox->Get('notify_from'); 

				static::Trace('Send bounce message.');
				
				$sSubject = static::GetStepSetting('subject');
				$sBody = static::GetStepSetting('notification'); 
				
				// Return to sender
				if($sTo == '') { 
					static::Trace('.. No "to" defined, skipping bounce message.');
				}
				elseif($sFrom == '') { 
					static::Trace('.. No "from" defined, skipping bounce message.');
				}
				elseif($oRawEmail) {
					
					// Allow some customization in the bounce message
					$sSubject = static::ReplaceMailPlaceholders($sSubject);
					$sBody = static::ReplaceMailPlaceholders($sBody);
					
					if($sSubject == '') {
						$sSubject = 'Message bounced - not compliant with an enforced policy.';
					}

					static::Trace('Sending bounce message "'.$sSubject.'" to "'.$sTo.'"');
					$oRawEmail->SendAsAttachment($sTo, $sFrom, $sSubject, $sBody);

				}
				
				break;

				
		}
		
		// - Set the next action for the mail processor.

			$iNextAction = match($sBehavior) {
				PolicyBehavior::BOUNCE_DELETE->value => eNextAction::DELETE_MESSAGE,
				PolicyBehavior::DELETE->value => eNextAction::DELETE_MESSAGE,
				PolicyBehavior::MOVE->value => eNextAction::MOVE_MESSAGE,
				PolicyBehavior::MARK_AS_ERROR->value => eNextAction::MARK_MESSAGE_AS_ERROR,
				PolicyBehavior::BOUNCE_MARK_AS_UNDESIRED->value => eNextAction::MARK_MESSAGE_AS_UNDESIRED,
				PolicyBehavior::MARK_AS_UNDESIRED->value => eNextAction::MARK_MESSAGE_AS_UNDESIRED,
				PolicyBehavior::SKIP_FOR_NOW->value => eNextAction::SKIP_FOR_NOW,
				PolicyBehavior::ABORT_ALL_FURTHER_PROCESSING->value => eNextAction::ABORT_ALL_FURTHER_PROCESSING,
				PolicyBehavior::DO_NOTHING->value => eNextAction::NO_ACTION,
				default => eNextAction::NO_ACTION
			};
			
			static::Trace('Set next action for EmailProcessor to %1$s', $iNextAction->name);
			ProcessingHelper::SetNextAction($iNextAction);

			
	}

	/**
	 * Whether the step is applicable. 
	 * Make sure the initiation of the step has already happened!
	 * 
	 * @return bool
	 */
	public static function IsApplicable() : bool {

		return true;

	}

}
