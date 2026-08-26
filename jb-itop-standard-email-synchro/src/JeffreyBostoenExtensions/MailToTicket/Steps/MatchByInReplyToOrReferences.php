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

use JeffreyBostoenExtensions\MailToTicket\Steps\Core\SaveReferences;

// iTop internals.
use DBObjectSearch;
use DBObjectSet;

/**
 * Class MatchByInReplyToOrReferences.  
 * If no related ticket was found yet, this step tries to find one based on In-reply-to or References e-mail headers.
 */
abstract class MatchByInReplyToOrReferences extends Base {
	
	/**
	 * @inheritDoc
	 *
	 * @details This must run before policies such as PolicyBounceUnknownTicketReference, PolicyTicketResolved, PolicyTicketClosed
	 * @todo Re-evaluate PolicyBounceUnknownTicketReference. Should this step be executed before or after?
	 */
	public static int $iPrecedence = 9;
	
	/**
	 * @inheritDoc
	 */
	public static string $sXMLSettingsPrefix = 'step_match_by_in_reply_to_or_references';
	
	/**
	 * @inheritDoc
	 */
	public static function IsApplicable() : bool {

		return Helper::UseMessageIdAsUid();
		
	}

	/**
	 * @inheritDoc
	 */
	public static function Execute() : void {
		
		
		$oRawEmail = ProcessingHelper::GetRawMail();
		$oTicket = ProcessingHelper::GetTicket();
		$oMailBox = ProcessingHelper::GetMailBox();
		
		// Reset before processing each mail.
		SaveReferences::$aNewMessageIds = [
			$oRawEmail->GetMessageId()
		];
		
		$sReferences = $oRawEmail->GetHeader('References');
		$sInReplyTo = $oRawEmail->GetHeader('In-Reply-To');
		
		
		// Only if Ticket has not been matched yet.
		// Just a safety measure.
		if($sReferences === '' && $sInReplyTo === '') {
			
			static::Trace('.. Empty headers: "References" and "In-Reply-To".');
			return;
			
		}
	
		// To avoid a random order of references: build an array of unique references.
		preg_match_all('/<.+?>/', $sReferences, $aMatches);
		$aReferences = $aMatches[0];
		
		// Fallback: also check "In-Reply-To". Extract the <msgid> token(s) the same way as "References",
		// instead of using the raw header value, so surrounding whitespace, a trailing RFC 5322 comment,
		// or multiple identifiers don't prevent it from matching the normalized values stored from prior
		// processing.
		if($sInReplyTo !== '') {
			preg_match_all('/<.+?>/', $sInReplyTo, $aInReplyToMatches);
			if($aInReplyToMatches[0] !== []) {
				$aReferences = array_merge($aReferences, $aInReplyToMatches[0]);
			}
			else {
				// No <msgid> token found (non-standard header); fall back to the raw, trimmed value.
				$aReferences[] = trim($sInReplyTo);
			}
		}
		
		// Just to prevent re-processing of an existing e-mail in this mailbox configuration:
		$aReferences[] = $oRawEmail->GetMessageId();

		// In the wild, it was observed that In-Reply-To is sometimes set - but empty, e.g.:
		// In-Reply-To: <>
		$aReferences = array_unique(array_diff($aReferences, ['', '<>']));
		

		static::Trace('.. '.count($aReferences).' references: '.implode(', ', $aReferences));
		
		// Safety measure to enforce Message-ID to only be 255 characters.
		// Should be safe enough. This is assumed by Combodo's implementation as well.
		foreach($aReferences as &$sRef) {
			$sRef = substr($sRef, 0, 255);
		}
		
		// For this mailbox, find the e-mail references that were already known in the system (previously processed).
		// Keep in mind, it's possible the same e-mail UID is linked to multiple tickets (copy of a ticket plus linked references).
		$aTicketIds = [-1];

		$aKnownReferences = [];

		// AddCondition() throws on an empty IN list; nothing to match against anyway (e.g. an
		// "In-Reply-To: <>" with no usable "References" and a missing "Message-Id").
		if($aReferences !== []) {

			$oFilterLinks = new DBObjectSearch('lnkEmailUidToTicket');
			$oFilterLinks->AddCondition('mailbox_id', $oMailBox->GetKey(), '=');
			$oFilterLinks->AddCondition('message_uid', $aReferences, 'IN');
			$oSetLinks = new DBObjectSet($oFilterLinks);

			while($oLink = $oSetLinks->Fetch()) {
				$aTicketIds[] = $oLink->Get('ticket_id');
				$aKnownReferences[] = $oLink->Get('message_uid');
			}

		}
		
		// Store the references that were NOT found, as new references;
		// so they can be saved once the ticket is known.
		SaveReferences::$aNewMessageIds = array_diff($aReferences, $aKnownReferences);

		if($oTicket === null) {
			
			$oFilterTickets = new DBObjectSearch('Ticket');
			$oFilterTickets->AddCondition('id', $aTicketIds, 'IN');
			$oSetTickets = new DBObjectSet($oFilterTickets, ['id' => false]);
			
			static::Trace('.. No related ticket yet. Try matching based on "References" and "In-Reply-To".');
			
			// Process and build a list of ticket IDs.
			// Note that this could result in: no prior ticket; one prior ticket; multiple prior tickets.
			switch($oSetTickets->Count()) {
				
				case 0:
					static::Trace('.. No links to tickets found.');
					break;
					
				case 1:
					
					// One ticket found.
					static::Trace('.. Link to 1 ticket found.');
					$oTicket = $oSetTickets->Fetch();
					break;
					
				default:
				
					// Build list of unique ticket IDs.
					// In this basic implementation, it will just use the latest ticket (even if that's marked as resolved/closed and there's an older open one).
					static::Trace('.. Link to multiple tickets found.');
					$oTicket = $oSetTickets->Fetch();
					break;
				
			}
		
		
		}
		
		// By this time, a ticket may have been identified.
		ProcessingHelper::SetTicket($oTicket);
		
		
	}
	
}
