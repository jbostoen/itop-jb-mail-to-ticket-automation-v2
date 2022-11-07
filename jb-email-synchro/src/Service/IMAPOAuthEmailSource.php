<?php

namespace Combodo\iTop\Extension\Service;

use \Combodo\iTop\Extension\Helper\ImapOptionsHelper;
use \Combodo\iTop\Extension\Helper\ProviderHelper;
use \EmailSource;
use \Exception;
use \IssueLog;
use \MessageFromMailbox;
use \MetaModel;

class IMAPOAuthEmailSource extends EmailSource {
	
	const LOG_CHANNEL = 'OAuth';

	/** LOGIN username @var string */
	protected $sLogin;
	protected $sServer;
	/** * @var IMAPOAuthStorage */
	protected $oStorage;
	protected $sTargetFolder;
	protected $sMailbox;

	/**
	 * Constructor.
	 *
	 * @param $oMailbox
	 *
	 * @throws \Exception
	 */
	public function __construct($oMailbox) {
		
		$this->sServer = $oMailbox->Get('server');
		$this->sLogin = $oMailbox->Get('login');
		$this->sMailbox = $oMailbox->Get('mailbox');
		$this->iPort = $oMailbox->Get('port');
		$this->sTargetFolder = $oMailbox->Get('target_folder');

		$oMailbox->Trace("IMAPOAuthEmailSource Start for $this->sServer", static::LOG_CHANNEL);
		IssueLog::Debug("IMAPOAuthEmailSource Start for $this->sServer", static::LOG_CHANNEL);
		
		$aImapOptions = preg_split('/\\r\\n|\\r|\\n/', $oMailbox->Get('imap_options'));
		$sSSL = '';
		
		if(in_array('ssl', $aImapOptions) == true) {
			$sSSL = 'ssl';
		} 
		elseif(in_array('tls', $aImapOptions) == true) {
			$sSSL = 'tls';
		}
		
		
		$this->oStorage = new IMAPOAuthStorage([
			'user'     => $this->sLogin,
			'host'     => $this->sServer,
			'port'     => $this->iPort,
			'ssl'      => $sSSL,
			'folder'   => $this->sMailbox,
			'provider' => ProviderHelper::getProviderForIMAP($oMailbox),
			'novalidatecert' => in_array('novalidate-cert', $aImapOptions)
		]);
		
		$oMailbox->Trace("IMAPOAuthEmailSource End for $this->sServer", static::LOG_CHANNEL);
		IssueLog::Debug("IMAPOAuthEmailSource End for $this->sServer", static::LOG_CHANNEL);

		// Calls parent with original arguments
		parent::__construct();
		
	}

	/**
	 * Initializes the message when it is being processed.
	 * @param $index integer The index between zero and count
	 * @return void
	 */
	public function InitMessage($index) {
		
		// Preventive measure. For restored emails, sometimes there's still an IMAP flag indicating it was 'marked for removal'.
		$this->UndeleteMessage($index);
		
		return;
	}
	
	public function GetMessagesCount() {
		
		IssueLog::Debug("IMAPOAuthEmailSource Start GetMessagesCount for $this->sServer", static::LOG_CHANNEL);
		$iCount = $this->oStorage->countMessages();
		IssueLog::Debug("IMAPOAuthEmailSource $iCount message(s) found for $this->sServer", static::LOG_CHANNEL);

		return $iCount;

	}

	public function GetMessage($index) {
		
		$bUseMessageId = (bool)MetaModel::GetModuleSetting('jb-email-synchro', 'use_message_id_as_uid', true);
		
		$iOffsetIndex = 1 + $index;
		$sUID = $this->oStorage->getUniqueId($iOffsetIndex);
		
		IssueLog::Debug("IMAPOAuthEmailSource Start GetMessage $iOffsetIndex (UID $sUID) for $this->sServer", static::LOG_CHANNEL);
		
		try {
			$oMail = $this->oStorage->getMessage($iOffsetIndex);
		}
		// Likely an Exception\InvalidArgumentException
		catch(Exception $e) {
			IssueLog::Debug("IMAPOAuthEmailSource Failed to get message $iOffsetIndex (UID $sUID): ".$e->getMessage(), static::LOG_CHANNEL);
		}
		
		$sUIDL = ($bUseMessageId == true ? $oMail->getHeader('message-id') : $sUID);
		
		$oNewMail = new MessageFromMailbox($sUIDL, $oMail->getHeaders()->toString(), $oMail->getContent());
		IssueLog::Debug("IMAPOAuthEmailSource End GetMessage $iOffsetIndex for $this->sServer", static::LOG_CHANNEL);

		return $oNewMail;
		
	}

	public function DeleteMessage($index) {
		
		$this->oStorage->removeMessage(1 + $index);
		
	}

	/**
	 * Marks the message for undeletion (IMAP-flag) of the given index [0..Count] from the mailbox.
	 * @param $index integer The index between zero and count
	 */
	public function UndeleteMessage($index) {
		
		$this->oStorage->undeleteMessage(1 + $index);
		
	}
	
	public function GetName() {
		return $this->sLogin;
	}

	public function GetListing() {
		
		$aReturn = [];

		// The Combodo version uses a foreach loop, which triggers a call to the GetMessage() method which may result in errors and make it difficult to know which message was causing issues.
		// Furthermore, it may lead to a potential crash.
		// This alternative approach tries to process each message and skips messages which result in an error.
		// Also to research: behavior in the laminas-mail library upon deletion of a message while the job is processed, since they're queried in this connection?
		
		$iMessageCount = $this->oStorage->countMessages();
		$iMessageId = 0;

		while($iMessageId < $iMessageCount) {
			
			IssueLog::Debug("IMAPOAuthEmailSource GetListing $iMessageId for $this->sServer", static::LOG_CHANNEL);
			
			try {
				
				$oMessage = $this->oStorage->getMessage($iMessageId);
			
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
					IssueLog::Debug("Mail to Ticket: unhandled 'Received:' header: ".$sReceived, static::LOG_CHANNEL);
					
					// Default to current time to avoid crash.
					$uTime = strtotime('now');
					
				}
				
				
				$bUseMessageId = (bool)MetaModel::GetModuleSetting('jb-email-synchro', 'use_message_id_as_uid', true);
				
				// Add to the list
				$aReturn[] = [
					'msg_id' => $iMessageId,
					'uidl' => ($bUseMessageId == true ? $oMessage->getHeader('message-id', 'string') : $this->oStorage->getUniqueId($iMessageId)),
					'udate' => $uTime
				];
				
			}
			catch(Exception $e) {
				
				// Skip, but log.
				IssueLog::Debug("IMAPOAuthEmailSource GetListing $iMessageId resulted in an error: ".$e->getMessage(), static::LOG_CHANNEL);
				
			}
			
			$iMessageId += 1;
			
		}

		return $aReturn;
	}

	/**
	 * Move the message of the given index [0..Count] from the mailbox to another folder
	 *
	 * @param $index integer The index between zero and count
	 */
	public function MoveMessage($index) {
		$this->oStorage->moveMessage(1 + $index, $this->sTargetFolder);

		return true;
	}

	public function Disconnect() {
		$this->oStorage->close();
	}

	public function GetMailbox() {
		return $this->sMailbox;
	}
	
	 /**
	  * Get IMAP connection. Exposed to dedicated extensions.
	  *
	  * @return IMAP connection
	  */
	 public function GetConnection() {
		
		return $this->rImapConn;
		
	 }
	 
}
