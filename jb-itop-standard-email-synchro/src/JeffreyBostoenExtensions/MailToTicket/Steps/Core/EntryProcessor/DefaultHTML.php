<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260521
 */

namespace JeffreyBostoenExtensions\MailToTicket\Steps\Core\EntryProcessor;

use JeffreyBostoenExtensions\MailToTicket\ProcessingHelper;
use JeffreyBostoenExtensions\MailToTicket\Steps\Core\CreateOrUpdateTicket;

// iTop.
use utils;

/**
 * Class DefaultHTML.  
 * An abstract class that implements the iEntryProcessor interface. All entry processors should extend this class.  
 * 
 * The idea is to allow chaining of multiple entry processors to finalize the log entry. 
 * 
 * To implement a new entry processor, create a new non-abstract class that extends this one.
 * 
 */
class DefaultHTML extends Base {

    /** 
     * @inheritDoc
     */
    public static function IsApplicable() : bool {

        // This default processor will disable itself if there are other entry processors available.
        return count(Base::GetEntryProcessors()) === 2 && (ProcessingHelper::GetMail()->sBodyFormat === 'text/html');

    }

    /**
     * @inheritDoc
     */
    public static function DoExecute(string $sEntry) : string {
        
        $oEmail = ProcessingHelper::GetMail();
        

        $sEntry = $oEmail->GetNewPartHTML($sEntry);

        if(strip_tags($sEntry) === '') {

            // No new part (only blank tags)...
            // It's better use the whole text of the message
            $sEntry = $oEmail->sBodyText;

        }

        $sEntry = CreateOrUpdateTicket::ManageInlineImages($sEntry, false);

        return $sEntry;

    }

}
