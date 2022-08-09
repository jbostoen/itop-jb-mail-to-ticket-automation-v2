<?php

namespace Combodo\iTop\Extension\Service;

use Combodo\iTop\Extension\Helper\ImapOptionsHelper;
use Combodo\iTop\Extension\Helper\ProviderHelper;
use EmailSource;
use IssueLog;
use MessageFromMailbox;

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
		$aImapOptions = preg_split('/\\r\\n|\\r|\\n/', $this->Get('imap_options'));
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
			'ssl'      => $this->sSSL,
			'folder'   => $this->sMailbox,
			'provider' => ProviderHelper::getProviderForIMAP($oMailbox),
			'novalidatecert' => in_array('novalidate-cert', $aImapOptions)
		]);
		IssueLog::Debug("IMAPOAuthEmailSource End for $this->sServer", static::LOG_CHANNEL);

		// Calls parent with original arguments
		parent::__construct();
		
	}

	public function GetMessagesCount() {
		
		IssueLog::Debug("IMAPOAuthEmailSource Start GetMessagesCount for $this->sServer", static::LOG_CHANNEL);
		$iCount = $this->oStorage->countMessages();
		IssueLog::Debug("IMAPOAuthEmailSource $iCount message(s) found for $this->sServer", static::LOG_CHANNEL);

		return $iCount;

	}

	public function GetMessage($index) {
		
		$iOffsetIndex = 1 + $index;
		$sUIDL = $this->oStorage->getUniqueId($iOffsetIndex);
		IssueLog::Debug("IMAPOAuthEmailSource Start GetMessage $iOffsetIndex (UID $sUIDL) for $this->sServer", static::LOG_CHANNEL);
		$oMail = $this->oStorage->getMessage($iOffsetIndex);
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
		
		// @todo Check https://github.com/laminas/laminas-mail/issues/179
		
	}
	
	public function GetName() {
		return $this->sLogin;
	}

	public function GetListing() {
		$aReturn = [];

		foreach ($this->oStorage as $iMessageId => $oMessage) {
			IssueLog::Debug("IMAPOAuthEmailSource GetListing $iMessageId for $this->sServer", static::LOG_CHANNEL);
			$aReturn[] = ['msg_id' => $iMessageId, 'uidl' => $this->oStorage->getUniqueId($iMessageId)];
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
