<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.261024
 */

namespace JeffreyBostoenExtensions\MailToTicket;

// iTop internals.
use AbstractApplicationUIExtension;
use Combodo\iTop\Application\WebPage\WebPage;
use Dict;
use TriggerMailToTicketProcessingOnEvent;

// jb-framework.
use JeffreyBostoenExtensions\Framework\HelpContainer;

/**
 * Class PlaceholderReferenceUIExtension. Shows the placeholders that are available per event on the triggers on mail processing events,
 * in a tab of their own.
 *
 * The built-in placeholders of iTop come first, then the general placeholders and the extra values of the events the trigger is subscribed to.
 * Without events (or when editing): the complete reference.
 */
class PlaceholderReferenceUIExtension extends AbstractApplicationUIExtension {

	/**
	 * @inheritDoc
	 */
	public function OnDisplayRelations($oObject, WebPage $oPage, $bEditMode = false) {

		if(!($oObject instanceof TriggerMailToTicketProcessingOnEvent)) {
			return;
		}

		$aSections = PlaceholderReference::BuildSections($bEditMode ? [] : static::GetEventCodesOfTrigger($oObject));

		$oPage->SetCurrentTab('MailProcessingEvent:Placeholder:Tab', Dict::S('MailProcessingEvent:Placeholder:Title'));
		$oPage->add(HelpContainer::Render($oPage, 'jb-email-synchro', 'PlaceholderReference.html.twig', [
			'sIntro' => Dict::S('MailProcessingEvent:Placeholder:Intro'),
			'sPlaceholderLabel' => Dict::S('MailProcessingEvent:Placeholder:Placeholder'),
			'sDescriptionLabel' => Dict::S('MailProcessingEvent:Placeholder:Description'),
			'aSections' => $aSections,
		]));

	}

	/**
	 * Gets the codes of the events a trigger is subscribed to.
	 *
	 * @param TriggerMailToTicketProcessingOnEvent $oTrigger
	 *
	 * @return string[]
	 */
	private static function GetEventCodesOfTrigger(TriggerMailToTicketProcessingOnEvent $oTrigger) : array {

		$aEventCodes = [];

		$oLinks = $oTrigger->Get('events_list');
		while($oLink = $oLinks->Fetch()) {
			$aEventCodes[] = (string)$oLink->Get('code');
		}

		return $aEventCodes;

	}

}
