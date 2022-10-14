<?php

namespace Combodo\iTop\Extension\Service;

use \Combodo\iTop\Extension\Helper\ImapOptionsHelper;
use \Combodo\iTop\Extension\Helper\ProviderHelper;
use \EmailSource;
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
		
		$oMail = $this->oStorage->getMessage($iOffsetIndex);
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

		foreach($this->oStorage as $iMessageId => $oMessage) {
			
			IssueLog::Debug("IMAPOAuthEmailSource GetListing $iMessageId for $this->sServer", static::LOG_CHANNEL);
			
			// Mimic 'udate' from original IMAP implementation.
			// Force header to be returned as 'array'
			$aHeaders = $oMessage->getHeader('received', 'array');
			$sHeader = $aHeaders[0]; // Note: currently using original 'received' time. Perhaps this should be the time from the first server instead? (last element)
			$sTime = explode(';', $sHeader);
			$uTime = strtotime($sTime);
			
			$bUseMessageId = (bool)MetaModel::GetModuleSetting('jb-email-synchro', 'use_message_id_as_uid', true);
			
			
			$aReturn[] = [
				'msg_id' => $iMessageId,
				'uidl' => ($bUseMessageId == true ? $oMessage->getHeader('message-id', 'string') : $this->oStorage->getUniqueId($iMessageId)),
				'udate' => $uTime
			];
			
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
