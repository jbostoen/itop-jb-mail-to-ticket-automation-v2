<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260421
 */

namespace JeffreyBostoenExtensions\MailToTicket;

// iTop.
use MetaMOdel;

/**
 * Class Helper. A helper class.
 */
abstract class Helper {
    

	/**
	 * Returns the value of the "use_message_id_as_uid" setting.
	 * 
	 * For some e-mail providers such as Microsoft Outlook 365 and GMail, it's recommended to set this setting to "true".  
	 * With those e-mail providers, the UID may change between two sessions, which makes it an unreliable value.  
	 * Instead, when enabled, the Message-Id can be used instead to uniquely identify a message.
	 *
	 * Notes:
	 * - It's possible to receive multiple messages with the same Message-Id. 
	 *   This could also simply occur because the message gets copied.
	 *   Since the contents of the message will be the same, the behavior is to process such messages only once.
	 * - When changing the "use_message_id_as_uid" setting in the configuration file, 
	 *   all the messages present in the mailbox will be considered as "new" and thus processed again.  
	 * - Some e-mail providers do not return a "Message-Id" property.
	 *
	 * @return bool
	 */
	public static function UseMessageIdAsUid() : bool {
		
		// Note: Contrary to Combodo's version: in most environments it seems better that this is enabled by default.
		return (bool)MetaModel::GetModuleSetting('jb-email-synchro', 'use_message_id_as_uid', true);
		
	}

}