<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260421
 */

namespace JeffreyBostoenExtensions\MailToTicket;

// iTop email processing.
use EmailMessage;
use EmailSource;
use MailInboxStandard;
use RawEmailMessage;

// iTop classes.
use Ticket;

/**
 * Class ProcessingHelper. A helper class to share info between various methods,  
 * and to keep track of a mailbox, e-mail message, ticket, etc. that are being processed.
 * 
 * Note: Currently only used for the background process!
 */
abstract class ProcessingHelper {

	/**
	 * @var string[] $aPreviouslyExecutedSteps Array of steps (class names) which have been executed already.
	 */
	private static array $aPreviouslyExecutedSteps = [];
	
	/**
	 * @var EmailMessage $oEmail E-mail message
	 */
	private static $oEmail = null;
	
	/**
	 * @var MailInboxStandard $oMailBox Mailbox
	 */
	private static $oMailBox = null;
	
	/**
	 * @var EmailSource $oSource E-mail source (e.g. IMAPEmailSource)
	 */
	private static $oSource = null;
	
	/**
	 * @var Ticket $oTicket Ticket object (in iTop)
	 */
	private static $oTicket = null;
	
	/**
	 * @var MessageHandler $oMessageHandler Mail data related to processing. Pass this object when calling
	 */
	private static MessageHandler $oMessageHandler;


	/**
	 * Gets the mailbox.
	 *
	 * @return MailInboxStandard
	 */
	public static function GetMailBox() : MailInboxStandard {
		
		return static::$oMailBox;
		
	}
	
	/**
	 * Sets the mailbox that's being processed.
	 *
	 * @param MailInboxStandard $oMailBox Mailbox
	 *
	 * @return void
	 */
	public static function SetMailBox(MailInboxStandard $oMailBox) : void {
		
		static::$oMailBox = $oMailBox;
		
	}

	
	/**
	 * Sets the e-mail that's being processed.
	 *
	 * @param EmailMessage $oMessage E-mail message.
	 *
	 * @return void
	 */
	public static function SetMail(EmailMessage $oEmail) : void {
		
		static::$oEmail = $oEmail;
		
	}
	
	
	/**
	 * Gets the e-mail that's being processed.
	 *
	 * @return EmailMessage
	 */
	public static function GetMail() : EmailMessage {
		
		return static::$oEmail;
		
	}
	

	/**
	 * Gets the raw e-mail that's being processed.
	 *
	 * @return RawEmailMessage
	 */
	public static function GetRawMail() : RawEmailMessage {
		
		return static::$oEmail->oRawEmail;
		
	}
	

	/**
	 * Gets the e-mail source.
	 *
	 * @return EmailSource The e-mail source.
	 */
	public static function GetMailSource() : EmailSource {
		
		return static::$oSource;
		
	}
	
	/**
	 * Sets the e-mail source.
	 *
	 * @param EmailSource $oSource E-mail source
	 *
	 * @return void
	 */
	public static function SetMailSource(EmailSource $oSource) : void {
		
		static::$oSource = $oSource;
		
	}
	

	/**
	 * Gets mail process data (indexes, UIDL, etc.) of the e-mail message that is being processed.
	 * 
	 * Note: This is an experimental method.
	 *
	 * @return MessageHandler
	 */
	public static function GetMessageHandler() : MessageHandler {
		
		return static::$oMessageHandler;
		
	}
	
	/**
	 * Sets index of e-mail message.
	 *
	 * @param MessageHandler $oHandler
	 * 
	 * @return void
	 */
	public static function SetMessageHandler(MessageHandler $oHandler) : void {
		
		static::$oMessageHandler = $oHandler;
		
	}
	
	/**
	 * Gets ticket.
	 *
	 * @return Ticket|null Ticket object if found/created; null otherwise.
	 */
	public static function GetTicket() : ?Ticket {
		
		return static::$oTicket;
		
	}
	
	/**
	 * Sets ticket.
	 *
	 * @param Ticket|null $oTicket Ticket
	 *
	 * @return void
	 */
	public static function SetTicket(?Ticket $oTicket) : void {
		
		static::$oTicket = $oTicket;
		
	}
	
	/**
	 * Gets executed steps.
	 *
	 * @return string[]
	 */
	public static function GetExecutedSteps() : array {
		
		return static::$aPreviouslyExecutedSteps;
		
	}
	
	/**
	 * Sets executed steps.
	 *
	 * @param string[] $aPreviouslyExecutedSteps Class names of previously executed steps.
	 *
	 * @return void
	 */
	public static function SetExecutedSteps(array $aPreviouslyExecutedSteps) : void {
		
		static::$aPreviouslyExecutedSteps = $aPreviouslyExecutedSteps;
		
	}
	

}