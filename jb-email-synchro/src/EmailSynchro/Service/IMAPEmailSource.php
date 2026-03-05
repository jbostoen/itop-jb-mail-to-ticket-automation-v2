<?php

namespace Combodo\iTop\Extension\EmailSynchro\Service;

use DirectoryTree\ImapEngine\Enums\ImapFetchIdentifier;
use DirectoryTree\ImapEngine\FolderInterface;
use DirectoryTree\ImapEngine\Mailbox;
use DirectoryTree\ImapEngine\MailboxInterface;
use EmailSource;
use Exception;
use IssueLog;
use MailInboxBase;
use MessageFromMailbox;

class IMAPEmailSource extends EmailSource {

	/** @var string LOG_CHANNEL Name of the log channel. */
	public const LOG_CHANNEL = IMAPEmailLogger::LOG_CHANNEL;

	/** @var string LOG_DEBUG_CLASS Name of this class(for debugging). */
	public const LOG_DEBUG_CLASS = 'IMAPEmailSource';
	
	/** @var string CONFIG_AUTHENTICATION Authentication type. */
	public const CONFIG_AUTHENTICATION = 'plain';

	/** @var string $sLogin Mailbox login. */
	protected $sLogin;
	
	/** @var string $sPassword Mailbox password (if applicable). */
	protected $sPassword;

	/** @var string $sServer Mailbox server. */
	protected $sServer;

	/** @var string $sTargetFolder Folder to store message in after processing. */
	protected $sTargetFolder;

	/** @var string $sMailbox The mailbox(folder) name. */
	protected $sMailbox;

	/** @var string|null $sAccessToken Access token to use instead of password, if set. */
	protected ?string $sAccessToken = null;

	/** @var MailboxInterface $oMailBox */
	private MailboxInterface $oMailbox;

	/** @var int $iPort The mail server port to connect to. */
	protected $iPort;

	/**
	 * @var FolderInterface|null
	 */
	private $oFolder;

	/** @var bool $bMessagesDeleted Whether messages were deleted. */
	private $bMessagesDeleted = false;

	public function __construct(MailInboxBase $oMailbox) {

		$this->sServer = $oMailbox->Get('server');
		$this->sLogin = $oMailbox->Get('login');
		$this->sMailbox = $oMailbox->Get('mailbox');
		$this->iPort = $oMailbox->Get('port');
		$this->sTargetFolder = $oMailbox->Get('target_folder');
		$this->sPassword = $this->sAccessToken ?? $oMailbox->Get('password');

		IssueLog::Debug("IMAPEmailSource Start for $this->sServer", static::LOG_CHANNEL);
		$aImapOptions = preg_split('/\\r\\n|\\r|\\n/', $oMailbox->Get('imap_options'));

		$sSSL = match(true) {
			in_array('ssl', $aImapOptions) => 'ssl',
			in_array('tls', $aImapOptions) => 'starttls',
			default => null,
		};

		$aOptions = [
			'port' => $this->iPort,
			'username' => $this->sLogin,
			'password' => $this->sPassword,
			'encryption' => $sSSL,
			'authentication' => static::CONFIG_AUTHENTICATION,
			'host' => $this->sServer,
			'debug' => IMAPEmailLogger::class,
		];

		if(in_array('validate_cert', $aImapOptions)) {
			IssueLog::Debug("IMAPEmailSource - SSL certificate validation enabled", static::LOG_CHANNEL);
			$aOptions['validate_cert'] = true;
		}

		$this->oMailbox = new Mailbox($aOptions);
		$this->oMailbox->connect();

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

		IssueLog::Debug(static::LOG_DEBUG_CLASS." Start GetMessagesCount for $this->sServer", static::LOG_CHANNEL);
		$iCount = $this->GetFolder()->status()['MESSAGES'] ?? 0;
		IssueLog::Debug(static::LOG_DEBUG_CLASS." $iCount message(s) found for $this->sServer", static::LOG_CHANNEL);

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

			if(!$oMessage) {
				return null;
			}
			$sUIDL = static::UseMessageIdAsUid() ? $oMessage->messageId() : $oMessage->uid();

		} catch(Exception $e) {

			IssueLog::Error(__METHOD__." $iOffsetIndex for $this->sServer throws an exception", static::LOG_CHANNEL, [
				'exception.message' => $e->getMessage(),
				'exception.stack'   => $e->getTraceAsString(),
			]);

			return null;
		}
		$oNewMail = new MessageFromMailbox($sUIDL, $oMessage->head(), $oMessage->body());
		IssueLog::Debug(__METHOD__." End: $iOffsetIndex for $this->sServer", static::LOG_CHANNEL);

		return $oNewMail;
	}

	public function DeleteMessage($index)
	{
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

		} catch(Exception $e) {
			IssueLog::Error(__METHOD__." $iOffsetIndex for $this->sServer throws an exception", static::LOG_CHANNEL, [
				'exception.message' => $e->getMessage(),
				'exception.stack'   => $e->getTraceAsString(),
			]);

			return null;
		}
		IssueLog::Debug(__METHOD__." End: $iOffsetIndex for $this->sServer", static::LOG_CHANNEL);

		return true;
	}

	
	/**
	 * Unmarks the message for deletion(IMAP-flag) of the given index [0..Count] from the mailbox.
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
		catch(Exception $e) {
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
		$oMessages = $this->GetFolder()
			->messages()
			->withHeaders()
			->get();
		foreach($oMessages as $oMessage) {
			$aReturn[] = [
				'msg_id' => $oMessage->messageId(),
				'uidl' => static::UseMessageIdAsUid() ? $oMessage->messageId() : $oMessage->uid(),
			];
		}
		return $aReturn;
	}

	/**
	 * @inheritDoc
	 */
	public function GetFolder() {
		
		if($this->oFolder === null) {
			$this->oFolder =  $this->oMailbox->folders()->find($this->sMailbox);
		}
		return $this->oFolder;

	}

	/**
	 * Move the message of the given index [0..Count] from the mailbox to another folder
	 *
	 * @param $index integer The index between zero and count
	 *
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

			if(!$oMessage) {
				return false;
			}

			// Use copy+delete instead of move as GMail won't expunge automatically and break our way of iterating over messages indexes
			$oMessage->copy($this->sTargetFolder);
			$oMessage->delete();
			$this->bMessagesDeleted = true;

		} catch(Exception $e) {
			IssueLog::Error(__METHOD__." $iOffsetIndex for $this->sServer throws an exception", static::LOG_CHANNEL, [
				'exception.message' => $e->getMessage(),
				'exception.stack'   => $e->getTraceAsString(),
			]);

			return false;
		}

		IssueLog::Debug(__METHOD__." End: $iOffsetIndex for $this->sServer", static::LOG_CHANNEL);
		return true;
	}


	/**
	 * @inheritDoc
	 */
	public function Disconnect() {

		// Expunge deleted messages before disconnecting.
		if($this->bMessagesDeleted) {
			IssueLog::Debug(__METHOD__." Expunging deleted messages for $this->sServer", static::LOG_CHANNEL);
			$this->GetFolder()->expunge();
		}

		$this->oMailbox->disconnect();
	}

	/**
	 * @inheritDoc
	 */
	public function GetMailbox() {
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
