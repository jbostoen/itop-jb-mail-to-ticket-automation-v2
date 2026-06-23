<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260521
 */

namespace JeffreyBostoenExtensions\MailToTicket\Steps\Core\EntryProcessor;

use JeffreyBostoenExtensions\MailToTicket\ProcessingHelper;

// iTop.
use utils;

/**
 * Class DefaultText.  
 * An abstract class that implements the iEntryProcessor interface. All entry processors should extend this class.  
 * 
 * The idea is to allow chaining of multiple entry processors to finalize the log entry. 
 * 
 * To implement a new entry processor, create a new non-abstract class that extends this one.
 * 
 */
class DefaultText extends Base {

    /** 
     * @inheritDoc
     */
    public static function IsApplicable() : bool {

        // This default processor will disable itself if there are other entry processors available.
        return count(Base::GetEntryProcessors()) === 2 && (ProcessingHelper::GetMail()->sBodyFormat !== 'text/html');

    }

    /**
     * @inheritDoc
     */
    public static function DoExecute(string $sEntry) : string {
        
        $oEmail = ProcessingHelper::GetMail();
        $sCaseLogEntry = $oEmail->GetNewPart($sEntry, $oEmail->sBodyFormat); // GetNewPart always returns a plain text version of the message.
        $sCaseLogEntry = utils::TextToHtml($sCaseLogEntry);

        return $sEntry;

    }

}
