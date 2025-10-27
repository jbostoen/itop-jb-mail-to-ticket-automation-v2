<?php

namespace Combodo\iTop\Extension\Service;

use Combodo\iTop\Extension\Helper\ProviderHelper;
use DirectoryTree\ImapEngine\Enums\ImapFetchIdentifier;
use DirectoryTree\ImapEngine\FolderInterface;
use DirectoryTree\ImapEngine\Mailbox;
use DirectoryTree\ImapEngine\MailboxInterface;
use DirectoryTree\ImapEngine\Message;
use EmailSource;
use Exception;
use IssueLog;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use MessageFromMailbox;

class IMAPOAuthEmailSource extends EmailSource {
	
	const LOG_CHANNEL = 'OAuth';

	/** LOGIN username @var string */
	protected $sLogin;
	protected $sServer;
	
	/** @var \MailInbox */
	protected $MailInboxBase = null;
	
	protected $sTargetFolder;

	/** @var string $sMailbox The mailbox name. */
	protected $sMailbox;

	protected $iPort;

	/**
	 * @var MailboxInterface|null
	 */
	private $oMailbox;

	/**
	 * @var FolderInterface|null
	 */
	private $oFolder;
	private $bMessagesDeleted = false;

	/**
	 * Constructor.
	 *
	 * @param $oMailbox
	 *
	 * @throws Exception
	 */
	public function __construct($oMailbox) {
		
		$this->sServer = $oMailbox->Get('server');
		$this->sLogin = $oMailbox->Get('login');
		$this->sMailbox = $oMailbox->Get('mailbox');
		$this->iPort = $oMailbox->Get('port');
		$this->sTargetFolder = $oMailbox->Get('target_folder');
		$this->oMailbox = $oMailbox;

		IssueLog::Debug(__METHOD__." Start for $this->sServer", static::LOG_CHANNEL);
		
		$aImapOptions = preg_split('/\\r\\n|\\r|\\n/', $oMailbox->Get('imap_options'));
		$sSSL = '';
		
		if(in_array('ssl', $aImapOptions) == true) {
			$sSSL = 'ssl';
		} 
		elseif(in_array('tls', $aImapOptions) == true) {
			$sSSL = 'tls';
		}
		

		$oProvider = ProviderHelper::getProviderForIMAP($oMailbox);
		$sAccessToken = '';
		try {
			$sAccessToken = ProviderHelper::GetAccessTokenForProvider($oProvider);
		}
		catch (IdentityProviderException $e) {
			IssueLog::Error('Failed to get IMAP oAuth credentials for incoming mails for provider ' . $oProvider::GetVendorName() , static::LOG_CHANNEL, [
				'exception.message' => $e->getMessage(),
				'exception.stack' => $e->getTraceAsString(),
			]);
		}

		if(empty($sAccessToken)) {
			IssueLog::Error('No OAuth token for IMAP for provider '.$oProvider::GetVendorName(), static::LOG_CHANNEL);
		}

		$this->oMailbox = new Mailbox([
			'port' => $this->iPort,
			'username' => $this->sLogin,
			'password' => $sAccessToken,
			'encryption' => $sSSL,
			'authentication' => 'oauth',
			'host' => $this->sServer,
			'debug' => IMAPOAuthEmailLogger::class,
		]);

		$this->oMailbox->connect();
		
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
		$iCount = $this->GetFolder()->status()['MESSAGES'] ?? 0;
		IssueLog::Debug(__METHOD__." $iCount message(s) found for $this->sServer", static::LOG_CHANNEL);

		return $iCount;

	}

	public function GetMessage($index) {
		
		$iOffsetIndex = 1 + $index;
		IssueLog::Debug(__METHOD__." Start: $iOffsetIndex for $this->sServer", static::LOG_CHANNEL);


		try {

			/** @var Message $oMessage */
			$oMessage = $this->GetFolder()
				->messages()
				->withHeaders()
				->withBody()
				->findOrFail($iOffsetIndex, ImapFetchIdentifier::MessageNumber);


			if (!$oMessage) {
				return null;
			}
			$sUIDL = static::UseMessageIdAsUid() ? $oMessage->messageId() : $oMessage->uid();
		}
		catch(Exception $e) {
			
			IssueLog::Error(__METHOD__." $iOffsetIndex for $this->sServer throws an exception", static::LOG_CHANNEL, [
				'exception.message' => $e->getMessage(),
				'exception.stack' => $e->getTraceAsString()
			]);
			return null;
			
		}
		
		$oNewMail = new MessageFromMailbox($sUIDL, $oMessage->head(), $oMessage->body());
		IssueLog::Debug(__METHOD__." End GetMessage $iOffsetIndex for $this->sServer", static::LOG_CHANNEL);

		return $oNewMail;
		
	}

	public function DeleteMessage($index) {
		
		$iOffsetIndex = 1 + $index;

		IssueLog::Debug(__METHOD__." Start: $iOffsetIndex for $this->sServer", static::LOG_CHANNEL);
		try {

			/** @var Message $oMessage */
			$oMessage = $this->GetFolder()
				->messages()
				->find($iOffsetIndex, ImapFetchIdentifier::MessageNumber);

			if(!$oMessage) {
				return null;
			}

			$oMessage->delete();
			$this->bMessagesDeleted = true;

		}
		catch (Exception $e) {
			IssueLog::Error(__METHOD__." $iOffsetIndex for $this->sServer throws an exception", static::LOG_CHANNEL, [
				'exception.message' => $e->getMessage(),
				'exception.stack' => $e->getTraceAsString(),
			]);

			return null;
		}
		IssueLog::Debug(__METHOD__." End: $iOffsetIndex for $this->sServer", static::LOG_CHANNEL);

		return true;
		
	}

	/**
	 * Unmarks the message for deletion (IMAP-flag) of the given index [0..Count] from the mailbox.
	 * @param $index integer The index between zero and count
	 */
	public function UndeleteMessage($index) {
		
		$iOffsetIndex = 1 + $index;

		IssueLog::Debug(__METHOD__." Start: $iOffsetIndex for $this->sServer", static::LOG_CHANNEL);
		try {

			/** @var Message $oMessage */
			$oMessage = $this->GetFolder()
				->messages()
				->find($iOffsetIndex, ImapFetchIdentifier::MessageNumber);

			if(!$oMessage) {
				return null;
			}

			$oMessage->restore();

		}
		catch (Exception $e) {
			IssueLog::Error(__METHOD__." $iOffsetIndex for $this->sServer throws an exception", static::LOG_CHANNEL, [
				'exception.message' => $e->getMessage(),
				'exception.stack' => $e->getTraceAsString(),
			]);

			return null;
		}
		IssueLog::Debug(__METHOD__." End: $iOffsetIndex for $this->sServer", static::LOG_CHANNEL);

		return true;
		
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
		
		$aReturn = [];
		$oFolder = $this->GetFolder();
		$oMessages = $oFolder->messages()->withHeaders()->get();

		/** @var Message $oMessage */
		foreach($oMessages as $oMessage) {
			$aReturn[] = [
				'msg_id' => $oMessage->messageId(),
				'uidl' => static::UseMessageIdAsUid()? $oMessage->messageId() : $oMessage->uid(),
				'udate' => $oMessage->date()->format('Y-m-d H:i:s'),
			];

		}

		return $aReturn;
	}

	/**
	 * Gets the (default) folder.
	 *
	 * @return FolderInterface|null
	 */
	public function GetFolder() {

		if($this->oFolder === null) {
			$this->oFolder = $this->oMailbox->folders()->find($this->sMailbox);
		}
		return $this->oFolder;

	}

	/**
	 * Move the message of the given index [0..Count] from the mailbox to another folder
	 *
	 * @param $index integer The index between zero and count
	 * @throws \DirectoryTree\ImapEngine\Exceptions\ImapCapabilityException
	 */
	public function MoveMessage($index) {

		$iOffsetIndex = 1 + $index;
		IssueLog::Debug(__METHOD__." Start: $iOffsetIndex for $this->sServer", static::LOG_CHANNEL);
		try {
			
			/** @var Message $oMessage */
			$oMessage = $this->GetFolder()
				->messages()
				->find($iOffsetIndex, ImapFetchIdentifier::MessageNumber);

			if (!$oMessage) {
				return false;
			}

			// Use copy+delete instead of move as GMail won't expunge automatically and break our way of iterating over messages indexes
			$oMessage->copy($this->sTargetFolder);
			$oMessage->delete();
			$this->bMessagesDeleted = true;
		}
		catch (Exception $e) {
			IssueLog::Error(__METHOD__." $iOffsetIndex for $this->sServer throws an exception", static::LOG_CHANNEL, [
				'exception.message' => $e->getMessage(),
				'exception.stack' => $e->getTraceAsString(),
			]);

			return false;
		}

		IssueLog::Debug(__METHOD__." End: $iOffsetIndex for $this->sServer", static::LOG_CHANNEL);
		return true;

	}

	public function Disconnect() {

		// Expunge deleted messages before disconnecting.
		if($this->bMessagesDeleted) {
			IssueLog::Debug(__METHOD__." Expunging deleted messages for $this->sServer", static::LOG_CHANNEL);
			$this->GetFolder()->expunge();
		}

		$this->oMailbox->disconnect();
		
	}


	/**
	 * Returns the name of the mailbox.
	 *
	 * @return string
	 */
	public function GetMailbox() : string {

		return $this->sMailbox;

	}
	
	/**
	 * Returns the mailbox object.
	 *
	 * @return MailboxInterface|null
	 */
	public function GetMailboxObject() : MailboxInterface|null {

		return $this->oMailbox;
		
	}
	
	 
}
