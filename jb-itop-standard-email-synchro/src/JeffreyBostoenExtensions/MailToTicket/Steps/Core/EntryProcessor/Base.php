<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260521
 */

namespace JeffreyBostoenExtensions\MailToTicket\Steps\Core\EntryProcessor;

// Generic.
use ReflectionClass;

/**
 * Interface iBase.
 */
interface iBase {

    /** 
     * Returns the rank. 
     * @return float
     */
    public static function GetRank() : float;

    /** 
     * Whether this processor is applicable.
     * @return bool
     */
    public static function IsApplicable() : bool;

    /** 
     * The execution. Use this method. 
     * 
     * @var string $sEntry The log entry to process. 
     * This will be the result of the previous processor in the chain, or the original log entry if this is the first processor.  
     * Note that it would be possible to completely override previous results. Handle with care.
     * 
     * @return string
     */
    public static function DoExecute(string $sEntry) : string;

}


/**
 * Class Base.  
 * An abstract class that implements the iEntryProcessor interface. All entry processors should extend this class.  
 * 
 * The idea is to allow chaining of multiple entry processors to finalize the log entry. 
 * 
 * To implement a new entry processor, create a new non-abstract class that extends this one.
 * 
 */
abstract class Base implements iBase {

	
	/**
	 * @var Base[] $aCachedEntryProcessors The list of non-abstract entry processors.
	 */
	public static ?array $aCachedEntryProcessors = null;


    /** 
     * @var float $fRank The rank of execution. Lower = first. 
     */
    private static float $fRank = 1;

    /**
     * @inheritDoc
     */
    public static function GetRank(): float {
        return static::$fRank;
    }

    /** 
     * @inheritDoc
     */
    public static function IsApplicable() : bool {
        return true;
    }


    /**
     * @inheritDoc
     */
    public static function DoExecute(string $sEntry) : string {
        return $sEntry;
    }


    /**
     * Returns the list of entry processsors.
     */
    public static function GetEntryProcessors() : array {

        // - Build the list of entry processors once.

			if(static::$aCachedEntryProcessors === null) {
					
				foreach(get_declared_classes() as $sClassName) {

					$oReflection = new ReflectionClass($sClassName);

					if($oReflection->implementsInterface(iBase::class) && !$oReflection->isAbstract()) {
						
						static::$aCachedEntryProcessors[] = new $sClassName();

					}
				}

				// - Sort the list.

					usort(static::$aCachedEntryProcessors, function(Base $a, Base $b) {
						return $b->GetRank() <=> $a->GetRank();
					});

			}

        return static::$aCachedEntryProcessors;

    }

}
