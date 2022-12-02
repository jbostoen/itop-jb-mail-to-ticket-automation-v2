<?php
/*
 * @copyright   Copyright (C) 2010-2022 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

namespace Combodo\iTop\Extension\Helper;

class MessageHelper {
	
	/**
	 * Returns Message-ID of an e-mail.
	 *
	 * @param \Object $oMessage Laminas e-mail object
	 *
	 * @return \String
	 */
	public static function GetMessageId($oMessage) {
		
		return $oMessage->getHeader('Message-ID', 'string');
		
	}
	
	/**
	 * Returns Message-ID of an e-mail.
	 *
	 * @param \Object $oMessage Laminas e-mail object
	 *
	 * @return \Integer UNIX timestamp
	 */
	public static function GetMessageId($oMessage) {
		
	
		// Mimic 'udate' from original IMAP implementation.
		// Note that 'Delivery-Date' is optional, so rely on 'Received' instead.
		// Force header to be returned as 'array'
		// Examples:
		// Received: from VI1PR02MB5952.eurprd02.prod.outlook.com ([fe80::b18c:101a:ab2c:958e]) by VI1PR02MB5952.eurprd02.prod.outlook.com ([fe80::b18c:101a:ab2c:958e%7]) with mapi id 15.20.5723.026; Fri, 14 Oct 2022 10:48:44 +0000
		// Received: from VI1PR02MB5997.eurprd02.prod.outlook.com (2603:10a6:800:182::9) by PR3PR02MB6393.eurprd02.prod.outlook.com with HTTPS; Thu, 20 Oct 2022 08:36:28 +0000 ARC-Seal: i=2; a=rsa-sha256; s=arcselector9901; d=microsoft.com; cv=pass;
		$aHeaders = $oMessage->getHeader('received', 'array');
		$sHeader = $aHeaders[0]; // Note: currently using original 'received' time on the final server. Perhaps this should be the time from the first server instead? (last element)
		$sReceived = explode(';', $sHeader)[1]; // Get date part of string. See examples above.
		$sReceived = preg_replace('/[^A-Za-z0-9,\:\+\- ]/', '', $sReceived); // Remove newlines etc which will result in failing strtotime. Keep only relevant characters.
		
		if(preg_match('/[0-3]{0,1}[0-9] (Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec) (19|20)[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2} [+-][0-9]{4}/', $sReceived, $aMatches)) {
			
			$uTime = strtotime($aMatches[0]);
			
		}
		else {
			
			// Keep track of this example.
			$this->oMailbox->Trace("Mail to Ticket: unhandled 'Received:' header: ".$sReceived);
			IssueLog::Debug("Mail to Ticket: unhandled 'Received:' header: ".$sReceived, static::LOG_CHANNEL);
			
			// Default to current time to avoid crash.
			$uTime = strtotime('now');
			
		}
		
		return $uTime;
		
	}
	
}