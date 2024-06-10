<?php

namespace Combodo\iTop\Extension\Service;

use \Combodo\iTop\Extension\Helper\MessageHelper;
use \Combodo\iTop\Extension\Helper\ProviderHelper;
use \EmailSource;
use \Exception;
use \IssueLog;
use \MessageFromMailbox;

class IMAPOAuthEmailSource extends EmailSource {
	
	const LOG_CHANNEL = 'OAuth';

	/** LOGIN username @var string */
	protected $sLogin;
	protected $sServer;
	
	/** @var \MailInbox */
	protected $MailInboxBase = null;
	
	/** * @var IMAPOAuthStorage */
	protected $oStorage;
	protected $sTargetFolder;
	protected $sMailbox;

	protected $iPort;
	protected $oMailbox;

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
		$this->oMailbox = $oMailbox;

		$oMailbox->Trace(__METHOD__." Start for $this->sServer", static::LOG_CHANNEL);
		IssueLog::Debug(__METHOD__." Start for $this->sServer", static::LOG_CHANNEL);
		
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
		
		$oMailbox->Trace(__METHOD__." End for $this->sServer", static::LOG_CHANNEL);
		IssueLog::Debug(__METHOD__." End for $this->sServer", static::LOG_CHANNEL);

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
		
		IssueLog::Debug(__METHOD__." Start GetMessagesCount for $this->sServer", static::LOG_CHANNEL);
		$iCount = $this->oStorage->countMessages();
		IssueLog::Debug(__METHOD__." $iCount message(s) found for $this->sServer", static::LOG_CHANNEL);

		return $iCount;

	}

	public function GetMessage($index) {
		
		$iOffsetIndex = 1 + $index;
		$sUIDL = $this->oStorage->getUniqueId($iOffsetIndex);
		
		$this->oMailbox->Trace(__METHOD__." Start GetMessage $iOffsetIndex (UID $sUIDL) for $this->sServer");
		IssueLog::Debug(__METHOD__." Start GetMessage $iOffsetIndex (UID $sUIDL) for $this->sServer", static::LOG_CHANNEL);
		
		try {
			
			$oMail = $this->oStorage->getMessage($iOffsetIndex);
			
		}
		// Likely a Laminas\Mail\Exception\InvalidArgumentException
		// For example: jb-mail-to-ticket-automation-v2 GitHub issue #27
		catch(Exception $e) {
			
			$this->oMailbox->Trace(__METHOD__." Failed to get message $iOffsetIndex (UID $sUIDL): ".$e->getMessage());
			IssueLog::Error(__METHOD__." Failed to get message $iOffsetIndex (UID $sUIDL): ".$e->getMessage(), static::LOG_CHANNEL, [
				'exception.message' => $e->getMessage(),
				'exception.stack' => $e->getTraceAsString()
			]);
			return null;
			
		}
		
		$sUIDL = (static::UseMessageIdAsUid() == true && MessageHelper::GetMessageId($oMail) != '' ? MessageHelper::GetMessageId($oMail) : $sUIDL);
		
		$oNewMail = new MessageFromMailbox($sUIDL, $oMail->getHeaders()->toString(), $oMail->getContent());
		$this->oMailbox->Trace(__METHOD__." End GetMessage $iOffsetIndex for $this->sServer");
		IssueLog::Debug(__METHOD__." End GetMessage $iOffsetIndex for $this->sServer", static::LOG_CHANNEL);

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
	
	/**
	 * @inheritDoc
	 */
	public function GetName() {
		return $this->sLogin;
	}


	/**
	 * @inheritDoc
	 */
	public function GetSourceId() {
		return $this->sServer.'/'.$this->sLogin;
	}
	
	/**
	 * @inheritDoc
	 */
	public function GetListing() {
		
		$iMessageCount = $this->oStorage->countMessages();
		
		if($iMessageCount === 0) {
			
			IssueLog::Debug(__METHOD__." for {$this->sServer}: no messages", static::LOG_CHANNEL);
			return [];
			
		}

		$aReturn = [];
		$bUseMessageId = static::UseMessageIdAsUid();

		// Iterates manually over the message iterator
		// We aren't using foreach as we need to catch each exception ! (N°5633)
		// We must iterate nevertheless for IMAPOAuthStorage::getUniqueId to work (will return a string during an iteration but an array if not iterating)
		$this->oStorage->rewind();
		while($this->oStorage->valid()) {
			
			$iMessageId = $this->oStorage->key();
			
			IssueLog::Debug(__METHOD__." messageId={$iMessageId} for {$this->sServer}", static::LOG_CHANNEL);
			
			try {
				
				$oMessage = $this->oStorage->current();
			
				$uTime = MessageHelper::GetMessageSentTime($oMessage);
				
				if($bUseMessageId == true) {
					$sUIDL =  MessageHelper::GetMessageId($oMessage);
				}
				else {
					$sUIDL = $this->oStorage->getUniqueId($iMessageId);
				}
				
				// Add to the list
				$aReturn[] = [
					'msg_id' => $iMessageId,
					'uidl' => $sUIDL,
					'udate' => $uTime
				];
				
			}
			catch(Exception $e) {
				IssueLog::Error(__METHOD__." messageId={$iMessageId} for {$this->sServer}: an exception occurred", static::LOG_CHANNEL, [
					'exception.message' => $e->getMessage(),
					'exception.stack'   => $e->getTraceAsString(),
				]);
				// Still return something. Skipping is handled elsewhere.
				$aReturn[] = ['msg_id' => $iMessageId, 'uidl' => null, 'udate' => null];
				continue;
			}
			finally {
				$this->oStorage->next();
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
