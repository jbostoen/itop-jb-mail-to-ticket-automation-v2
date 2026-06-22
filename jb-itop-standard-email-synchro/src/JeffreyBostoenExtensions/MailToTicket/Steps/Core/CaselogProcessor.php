<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260521
 */

/**
 * Interface iCaselogProcessor. Once the standard 
 */
interface iCaselogProcessor {

    public static function DoExecute(string $sEntry) : $sEntry;

}

abstract class CaselogProcessor {



}