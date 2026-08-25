<?php

namespace Combodo\iTop\Extension\EmailSynchro\Service;

use JeffreyBostoenExtensions\MailToTicket\Helper;

use DirectoryTree\ImapEngine\Enums\ImapFetchIdentifier;
use DirectoryTree\ImapEngine\FolderInterface;
use DirectoryTree\ImapEngine\Mailbox;
use DirectoryTree\ImapEngine\MailboxInterface;
use DirectoryTree\ImapEngine\Message;
use EmailSource;
use Exception;
use IssueLog;
use JeffreyBostoenExtensions\MailToTicket\MessageHandler;
use MailInboxBase;
use MessageFromMailbox;

class IMAPEmailSource extends EmailSource {

	/** @var string LOG_CHANNEL Name of the log channel. */
	public const LOG_CHANNEL = IMAPEmailLogger::LOG_CHANNEL;

	/** @var string LOG_DEBUG_CLASS Name of this class(for debugging). */
	public const LOG_DEBUG_CLASS = 'IMAPEmailSource';
	
	/** @var string CONFIG_AUTHENTICATION Authentication type. */
	public const CONFIG_AUTHENTICATION = 'plain';

	/** @var string CONFIG_DEBUG_LOGGER Class used to log the raw IMAP protocol conversation. */
	public const CONFIG_DEBUG_LOGGER = IMAPEmailLogger::class;

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
			'debug' => static::CONFIG_DEBUG_LOGGER,
		];

		if(in_array('validate_cert', $aImapOptions)) {
			IssueLog::Debug("IMAPEmailSource - SSL certificate validation enabled", static::LOG_CHANNEL);
			$aOptions['validate_cert'] = true;
		}

		// The IMAP engine instantiates the "debug" logger itself (with no constructor arguments), so hand
		// over the mailbox being processed via a static property; IMAPEmailLogger routes lines through it.
		IMAPEmailLogger::$oCurrentMailbox = $oMailbox;

		$this->oMailbox = new Mailbox($aOptions);
		$this->oMailbox->connect();

		// Calls parent with original arguments
		parent::__construct();
	}


	/**
	 * Initializes the message when it is being processed.
	 * @param MessageHandler $oMsgHandler
	 * @return void
	 */
	public function InitMessage(MessageHandler $oMsgHandler) : void {
		
		// Pre-emptive measure. For restored emails, sometimes there's still an IMAP flag indicating it was 'marked for removal'.
		$this->UndeleteMessage($oMsgHandler);
		
		return;
	}

	/**
	 * Returns a message count based on the IMAP engine reseult.
	 *
	 * @return int
	 */
	public function GetMessagesCount() : int {

		IssueLog::Debug(static::LOG_DEBUG_CLASS." Start GetMessagesCount for $this->sServer", static::LOG_CHANNEL);
		$iCount = $this->GetFolder()->status()['MESSAGES'] ?? 0;
		IssueLog::Debug(static::LOG_DEBUG_CLASS." $iCount message(s) found for $this->sServer", static::LOG_CHANNEL);

		return $iCount;
	}

	
	/**
	 * Gets a message by its UID.
	 */
	public function GetMessage(MessageHandler $oMsgHandler) : ?MessageFromMailbox {

		IssueLog::Debug(__METHOD__." for $this->sServer", static::LOG_CHANNEL);
		try {

			/** @var Message $oMessage */
			$oMessage = $this->GetFolder()
				->messages()
				->withHeaders()
				->withBody()
				->findOrFail($oMsgHandler->GetUid(), ImapFetchIdentifier::Uid);

			if(!$oMessage) {
				return null;
			}

		} catch(Exception $e) {

			IssueLog::Error(__METHOD__." for $this->sServer throws an exception", static::LOG_CHANNEL, [
				'exception.message' => $e->getMessage(),
				'exception.stack'   => $e->getTraceAsString(),
			]);

			return null;
		}
		$oNewMail = new MessageFromMailbox($oMsgHandler->GetInternalIdentifier(), $oMessage->head(), $oMessage->body());

		return $oNewMail;
	}


	/**
	 * @param MessageHandler $oMsgHandler Message data
	 * @return bool
	 */
	public function DeleteMessage(MessageHandler $oMsgHandler) : bool {

		try {

			$oMessage = $oMsgHandler->GetMessageObject();

			if(!$oMessage) {
				return false;
			}

			$oMessage->delete();
			$this->bMessagesDeleted = true;


		} catch(Exception $e) {
			IssueLog::Error(__METHOD__."  for $this->sServer throws an exception", static::LOG_CHANNEL, [
				'exception.message' => $e->getMessage(),
				'exception.stack'   => $e->getTraceAsString(),
			]);

			return false;
		}
		

		return true;
	}

	
	/**
	 * Unmarks the message for deletion(IMAP-flag) of the given index [0..Count] from the mailbox.
	 * 
	 * @param MessageHandler $oMsgHandler
	 * @return bool
	 */
	public function UndeleteMessage(MessageHandler $oMsgHandler) : bool {

		try {
			
			$oMessage = $oMsgHandler->GetMessageObject();

			if(!$oMessage) {
				return false;
			}

			$oMessage->restore();

		}
		catch(Exception $e) {
			IssueLog::Error(__METHOD__." for $this->sServer throws an exception", static::LOG_CHANNEL, [
				'exception.message' => $e->getMessage(),
				'exception.stack' => $e->getTraceAsString(),
			]);

			return false;
		}
		

		return true;
		
	}
	
	/**
	 * @inheritDoc
	 */
	public function GetName() : string {
		return $this->sLogin;
	}

	/**
	 * @inheritDoc
	 */
	public function GetSourceId() : string {

		return $this->sServer.'/'.$this->sLogin;

	}

	/**
	 * @inheritDoc
	 */
	public function GetListing() : array{

		$aReturn = [];
		$oMessages = $this->GetFolder()
			->messages()
			->withHeaders()
			->get();

		/** @var Message $oMessage */
		foreach($oMessages as $iIndex => $oMessage) {

			$aReturn[] = MessageHandler::FromMessage($oMessage, $this, $iIndex);

		}
		return $aReturn;
	}

	/**
	 * @inheritDoc
	 */
	public function GetFolder() : ?FolderInterface {
		
		// Set the folder if not already done
		if ($this->oFolder === null && $this->sMailbox === '') {
			$this->oFolder = $this->oMailbox->inbox();
		} elseif ($this->oFolder === null) {
			$this->oFolder = $this->oMailbox->folders()->find($this->sMailbox);
		}

		// If there is no real folder yet, throw an error.
		if ($this->oFolder === null) {
			throw new Exception("IMAP folder '{$this->sMailbox}' not found on server {$this->sServer}. Please check the 'Mailbox (for IMAP)' field and make sure it follows RFC 3501 (https://www.rfc-editor.org/rfc/rfc3501.html#section-5.1)");
		}

		return $this->oFolder;

	}

	/**
	 * @inheritDoc
	 */
	public function MoveMessage(MessageHandler $oMsgHandler, string $sTargetFolder) : bool {

		try {

			$oMessage = $oMsgHandler->GetMessageObject();

			if(!$oMessage) {
				return false;
			}

			// Use copy+delete instead of move as GMail won't expunge automatically and break our way of iterating over messages indexes
			$oMessage->copy($sTargetFolder);
			$oMessage->delete();
			$this->bMessagesDeleted = true;

		} catch(Exception $e) {
			IssueLog::Error(__METHOD__." for $this->sServer throws an exception", static::LOG_CHANNEL, [
				'exception.message' => $e->getMessage(),
				'exception.stack'   => $e->getTraceAsString(),
			]);

			return false;
		}

		return true;
	}


	/**
	 * @inheritDoc
	 */
	public function CopyMessage(MessageHandler $oMsgHandler, string $sTargetFolder) : bool {

		try {

			$oMessage = $oMsgHandler->GetMessageObject();

			if(!$oMessage) {
				return false;
			}

			$oMessage->copy($sTargetFolder);

		} catch(Exception $e) {
			IssueLog::Error(__METHOD__." for $this->sServer throws an exception", static::LOG_CHANNEL, [
				'exception.message' => $e->getMessage(),
				'exception.stack'   => $e->getTraceAsString(),
			]);

			return false;
		}

		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function MarkAsSeen(MessageHandler $oMsgHandler) : bool {

		try {

			$oMessage = $oMsgHandler->GetMessageObject();

			if(!$oMessage) {
				return false;
			}

			$oMessage->markSeen();
			

		} catch(Exception $e) {
			IssueLog::Error(__METHOD__." for $this->sServer throws an exception", static::LOG_CHANNEL, [
				'exception.message' => $e->getMessage(),
				'exception.stack'   => $e->getTraceAsString(),
			]);

			return false;
		}

		return true;
	}

	

	/**
	 * @inheritDoc
	 */
	public function Disconnect() : void {

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


	/**
	 * Returns a Message (DirectoryTree\ImapEngine\Message) object based on the given message data.
	 *
	 * @param array $aMessage Message data
	 * @return Message|null
	 */
	private function GetMessageFromData(array $aMessage) : ?Message {

		if(Helper::UseMessageIdAsUid()) {

			// - Current version of this extension 
			//   will use the current uid (= the one that is currently returned from the mail server through the GetListing() method.).

				$iIndex = $aMessage['uid'];
				$identifier = ImapFetchIdentifier::Uid;


				// Note: This might also work, but if a copy of the same message (same Message-ID) is present, it gets more tricky to process.
				// $message = $inbox->messages()->whereHeader('Message-ID', '<unique-id@example.com>')->first();

		} else {

			$iIndex = $aMessage['original_number_in_listing'] + 1;
			$identifier = ImapFetchIdentifier::MessageNumber;

		}

		/** @var Message $oMessage */
		$oMessage = $this->GetFolder()
			->messages()
			->find($iIndex, $identifier);

		return $oMessage;

	}


}
