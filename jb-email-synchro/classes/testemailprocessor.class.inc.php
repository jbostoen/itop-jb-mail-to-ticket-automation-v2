<?php

/**
 * Used for the unit test of the EmailMessage class
 * Simulates incoming messages by reading from a directory './log) containing .eml files
 * and processes them to check the decoding of the messages
 *
 */
class TestEmailProcessor extends EmailProcessor
{
	public function ListEmailSources()
	{
		return array( 0 => new TestEmailSource(dirname(__FILE__).'/../log', 'test'));
	}
	
	public function DispatchMessage(EmailSource $oSource, $index, $sUIDL, $oEmailReplica = null)
	{
		return EmailProcessor::PROCESS_MESSAGE;
	}
	
	/**
	 * Process the email downloaded from the mailbox.
	 * This implementation delegates the processing the MailInbox instances
	 * The caller (identified by its email) must already exists in the database
	 * @param EmailSource $oSource The source from which the email was read
	 * @param integer $index The index of the message in the mailbox
	 * @param EmailMessage $oEmail The downloaded/decoded email message
	 * @param EmailReplica $oEmailReplica The information associating a ticket to the email. This replica is new (i.e. not yet in DB for new messages)
	 * @return integer Next Action Code
	 */
	public function ProcessMessage2(EmailSource $oSource, $index, EmailMessage $oEmail, EmailReplica $oEmailReplica)
	{
		$sMessage = "[$index] ".$oEmail->sMessageId.' - From: '.$oEmail->sCallerEmail.' ['.$oEmail->sCallerName.']'.' Subject: '.$oEmail->sSubject.' - '.count($oEmail->aAttachments).' attachment(s)';
		if (empty($oEmail->sSubject))
		{
			$sMessage .= "\n=====================================\nERROR: Empty subject for the message.\n";
		}
		if (empty($oEmail->sBodyText))
		{
			$sMessage .= "\n=====================================\nERROR: Empty body for the message.\n";
		}
		else
		{
			$sNewPart = $oEmail->GetNewPart();
			$sMessage .= "\n=====================================\nFormat:{$oEmail->sBodyFormat} \nNewpart:\n{$sNewPart}\n============================================.\n";
		}
		$index = 0;
		foreach($oEmail->aAttachments as $aAttachment)
		{
			$sMessage .= "\n\tAttachment #$index\n";
			if (empty($aAttachment['mimeType']))
			{
				$sMessage .= "\n=====================================\nERROR: Empty mimeType for attachment #$index of the message.\n";
			}
			else
			{
				$sMessage .= "\t\tType: {$aAttachment['mimeType']}\n";
			}
			if (empty($aAttachment['filename']))
			{
				$sMessage .= "\n=====================================\nERROR: Empty filename for attachment #$index of the message.\n";
			}
			else
			{
				$sMessage .= "\t\tName: {$aAttachment['filename']}\n";
			}
			if (empty($aAttachment['content']))
			{
				$sMessage .= "\n=====================================\nERROR: Empty CONTENT for attachment #$index of the message.\n";
			}
			else
			{
				$sMessage .= "\t\tContent: ".strlen($aAttachment['content'])." bytes\n";
			}
			$index++;
		}
		if (!utils::IsModeCLI())
		{
			$sMessage = '<p>'.htmlentities($sMessage, ENT_QUOTES, 'UTF-8').'</p>';
		}
		echo $sMessage."\n";
		return EmailProcessor::NO_ACTION;	
	}

	/**
	 * Process the email downloaded from the mailbox.
	 * This implementation delegates the processing the MailInbox instances
	 * The caller (identified by its email) must already exists in the database
	 *
	 * @param EmailSource $oSource The source from which the email was read
	 * @param integer $index The index of the message in the mailbox
	 * @param EmailMessage $oEmail The downloaded/decoded email message
	 * @param EmailReplica $oEmailReplica The information associating a ticket to the email. This replica is new (i.e. not yet in DB for new messages)
	 * @param array $aErrors
	 *
	 * @return int
	 */
	public function ProcessMessage(EmailSource $oSource, $index, EmailMessage $oEmail, EmailReplica $oEmailReplica, &$aErrors = array())
	{
		try
		{
			$oInbox = $this->GetInboxFromSource($oSource);
			self::Trace("Test Email Synchro: MailInboxesEmailProcessor: Processing message $index ({$oEmail->sUIDL})");
			if ($oEmailReplica->IsNew())
			{
				$oTicket = $oInbox->ProcessNewEmail($oSource, $index, $oEmail);

				if (is_object($oTicket))
				{
					if (EmailBackgroundProcess::IsMultiSourceMode())
					{

						$oEmailReplica->Set('uidl', $oSource->GetName() . '_' . $oEmail->sUIDL);
					}
					else
					{
						$oEmailReplica->Set('uidl', $oEmail->sUIDL);
					}
					$oEmailReplica->Set('mailbox_path', $oSource->GetMailbox());
					$oEmailReplica->Set('message_id', $oEmail->sMessageId);
					$oEmailReplica->Set('ticket_id', $oTicket->GetKey());
					$oEmailReplica->DBInsert();

					if (!empty($oInbox->sLastError))
					{
						$this->sLastErrorSubject = "Error during ticket update";
						$this->sLastErrorMessage = $oInbox->sLastError;
						$aErrors[] = $oInbox->sLastError;
					}
				}
				else
				{
					// Error ???
					$this->sLastErrorSubject = "Failed to create a ticket for the incoming email";
					$this->sLastErrorMessage = $oInbox->sLastError;
					$aErrors[] = $oInbox->sLastError;
					self::Trace($oInbox->sLastError);
					self::Trace("Test Email Synchro: MailInboxesEmailProcessor: Failed to create a ticket for the incoming email $index ({$oEmail->sUIDL})");
				}
			}
			else
			{

				$oInbox->ReprocessOldEmail($oSource, $index, $oEmail, $oEmailReplica);
			}
			$iRetCode = $oInbox->GetNextAction();
			$sRetCode = $this->GetActionFromCode($iRetCode);
			self::Trace("Test Email Synchro: MailInboxesEmailProcessor: End of processing of the new message $index ({$oEmail->sUIDL}) retCode: ($iRetCode) $sRetCode");
		}
		catch(Exception $e)
		{
			$iRetCode = $oInbox->GetNextAction();
			$this->sLastErrorSubject = "Failed to process email $index ({$oEmail->sUIDL})";
			$this->sLastErrorMessage = "Email Synchro: Failed to create a ticket for the incoming email $index ({$oEmail->sUIDL}), reason: exception: ".$e->getMessage();
			self::Trace("Test Email Synchro: MailInboxesEmailProcessor: Failed to create a ticket for the incoming email $index ({$oEmail->sUIDL}), reason: exception: ".$e->getMessage()."\n".$e->getTraceAsString());
		}

		return $iRetCode;
	}

	private function GetInboxFromSource($oSource)
	{
		return MetaModel::NewObject('MailInboxStandard');
	}

	/**
	 * Returns a action (string) corresponding to the given action code
	 * @param int $iRetCode The action code from EmailProcessor
	 * @return string The textual code of the action
	 */
	protected function GetActionFromCode($iRetCode)
	{
		$sRetCode = 'Unknown Code '.$iRetCode;
		switch($iRetCode)
		{
			case EmailProcessor::NO_ACTION:
				$sRetCode = 'NO_ACTION';
				break;

			case EmailProcessor::DELETE_MESSAGE;
				$sRetCode = 'DELETE_MESSAGE';
				break;

			case EmailProcessor::PROCESS_MESSAGE:
				$sRetCode = 'PROCESS_MESSAGE';
				break;

			case EmailProcessor::PROCESS_ERROR:
				$sRetCode = 'PROCESS_ERROR';
				break;

			case EmailProcessor::MARK_MESSAGE_AS_ERROR:
				$sRetCode = 'MARK_MESSAGE_AS_ERROR';
				break;

			case EmailProcessor::MARK_MESSAGE_AS_UNDESIRED:
				$sRetCode = 'MARK_MESSAGE_AS_UNDESIRED';
				break;
		}
		return $sRetCode;
	}

}

EmailBackgroundProcess::RegisterEmailProcessor('TestEmailProcessor');
