<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260711
 */
 

namespace JeffreyBostoenExtensions\MailToTicket\Steps;

use JeffreyBostoenExtensions\MailToTicket\{
	ProcessingHelper
};


/**
 * Class AttachmentForbiddenMimeType. A policy to enforce some rules on the attachment.
 */
abstract class AttachmentForbiddenMimeType extends Base {
	
	/**
	 * @inheritDoc
	 */
	public static int $iPrecedence = 10;
	
	/**
	 * @inheritDoc
	 */
	public static string $sXMLSettingsPrefix = 'policy_attachment_forbidden_mimetype';
		
	private static function GetEffectiveMimeTypes(array $aAttachment) : array {

		$aTypes = [$aAttachment['mimeType']];

		if(!empty($aAttachment['content']) && function_exists('finfo_open')) {

			$rFinfo = finfo_open(FILEINFO_MIME_TYPE);
			$sSniffed = finfo_buffer($rFinfo, $aAttachment['content']);
			finfo_close($rFinfo);

			if($sSniffed !== false) {
				$aTypes[] = $sSniffed;
			}

		}

		return array_unique($aTypes);

	}

	/**
	 * @inheritDoc
	 */
	public static function Execute() : void {
		
		$oEmail = ProcessingHelper::GetMail();
		
		// Checking if attachments are in line with configured policies.
		
			$sForbiddenMimeTypes = static::GetStepSetting('mimetypes');
			
			if(trim($sForbiddenMimeTypes) == '') {
				static::Trace('.. No forbidden MimeTypes specified.');
			}
			else {
				
				$aForbiddenMimeTypes = preg_split(static::NEWLINE_REGEX, $sForbiddenMimeTypes);
			
				static::Trace('.. Forbidden MimeTypes: '. implode(' - ', $aForbiddenMimeTypes));
				static::Trace('.. # Attachments: '. count($oEmail->aAttachments));
				
				switch(static::GetStepSetting('behavior')) {
					
					case PolicyBehavior::BOUNCE_DELETE->value:
					case PolicyBehavior::BOUNCE_MARK_AS_UNDESIRED->value:
					case PolicyBehavior::DELETE->value:
					case PolicyBehavior::MARK_AS_UNDESIRED->value:
					case PolicyBehavior::DO_NOTHING->value:
						
						// Forbidden attachments? 
						foreach($oEmail->aAttachments as $iIndex => $aAttachment) { 
							static::Trace('.. Attachment #'.$iIndex.' MimeType: '.$aAttachment['mimeType']);
							
							if(array_intersect(static::GetEffectiveMimeTypes($aAttachment), $aForbiddenMimeTypes) !== []) {

								static::Trace('... Found attachment with forbidden MimeType "'.$aAttachment['mimeType'].'"');
								static::HandleViolation();
								
								// No specific fallback								
								// Stop processing any further!
								return;
							}
						}
					
						break; // Defensive programming
					
					case 'fallback_ignore_forbidden_attachments':
					
						// Ticket will be processed. Forbidden attachments will be removed here.
						foreach($oEmail->aAttachments as $index => $aAttachment) {
							if(array_intersect(static::GetEffectiveMimeTypes($aAttachment), $aForbiddenMimeTypes) !== []) {
								static::Trace("Attachment Content-Id ". $aAttachment['content-id'] . " - Mime Type: {$aAttachment['mimeType']} = forbidden.");
								// Removing attachment
								unset($oEmail->aAttachments[$index]);
							}
							else {
								static::Trace("Attachment Content-Id ". $aAttachment['content-id'] . " - Mime Type: {$aAttachment['mimeType']} = allowed.");
							}
						}
						break;
						
					
					default:
						static::Trace('.. Unexpected "behavior"');
						break;
				}
		
			}
			
		
	}
	
}
