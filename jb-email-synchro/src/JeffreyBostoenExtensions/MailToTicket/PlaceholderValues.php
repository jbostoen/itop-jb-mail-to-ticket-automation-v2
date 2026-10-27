<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.261024
 */

namespace JeffreyBostoenExtensions\MailToTicket;

use DateTime;
use Exception;

/**
 * Class PlaceholderValues. A set of text values that can be used as placeholders, with the same syntax as the attributes of an iTop object.
 *
 * Passed as "<name>->object()" in the context arguments of an action. iTop then resolves "$<name>->...$" through GetForTemplate():
 * - "$mail->subject$": the value as it is.
 * - "$mail->html_escape(subject)$": the value, escaped for use in an HTML body.
 * - "$mail->date_only(date)$", "$mail->time_only(date)$": the date or the time part of a value that holds a date and time (Y-m-d H:i:s). A value that is already a date (Y-m-d, for "date_only") or a time (H:i:s, for "time_only") is returned as it is, and so is any other value.
 *
 * The verb to escape is not called "html" on purpose: for iTop objects, "html()" generates the HTML representation of an attribute (e.g. "$ticket->html(description)$"), which is not the same as escaping a text value.
 * The values may come from a received e-mail, so they may contain anything.
 */
class PlaceholderValues {

	/** @var array<string, string> $aValues Values by name. */
	private array $aValues = [];

	/**
	 * Constructor.
	 *
	 * @param array<string, scalar|null> $aValues Values by name.
	 */
	public function __construct(array $aValues) {

		foreach($aValues as $sName => $mValue) {
			$this->aValues[(string)$sName] = (string)$mValue;
		}

	}

	/**
	 * Gets a copy with extra values. Existing values with the same name are overwritten.
	 *
	 * @param array<string, scalar|null> $aValues Values by name.
	 *
	 * @return static
	 */
	public function With(array $aValues) : static {

		return new static(array_merge($this->aValues, $aValues));

	}

	/**
	 * Gets the names of the values.
	 *
	 * @return string[]
	 */
	public function GetNames() : array {

		return array_map('strval', array_keys($this->aValues));

	}

	/**
	 * Gets the date part or the time part of a value that holds a date and time.
	 * A value that is already (only) that part, or that is not a valid date and time, is returned as it is.
	 *
	 * @param string $sValue Value, e.g. "2026-01-02 03:04:05".
	 * @param string $sFormat Format of the part: "Y-m-d" or "H:i:s".
	 *
	 * @return string
	 */
	private static function GetPart(string $sValue, string $sFormat) : string {

		$oDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $sValue);
		if($oDateTime !== false && $oDateTime->format('Y-m-d H:i:s') === $sValue) {
			return $oDateTime->format($sFormat);
		}

		return $sValue;

	}

	/**
	 * Resolves a placeholder. Called by iTop (MetaModel::ApplyParams()).
	 *
	 * @param string $sPlaceholder Name of the value, or "verb(name)". Supported verbs: "html_escape", "date_only", "time_only".
	 *
	 * @return string
	 *
	 * @throws Exception For an unknown name or verb. iTop logs this and leaves the placeholder untouched.
	 */
	public function GetForTemplate(string $sPlaceholder) : string {

		$sVerb = '';
		$sName = $sPlaceholder;

		if(preg_match('/^([a-z_]+)\((.*)\)$/', $sPlaceholder, $aMatches) === 1) {
			$sVerb = $aMatches[1];
			$sName = $aMatches[2];
		}

		if(!array_key_exists($sName, $this->aValues)) {
			throw new Exception("Unknown value '$sName' in placeholder '$sPlaceholder'.");
		}

		return match($sVerb) {
			'' => $this->aValues[$sName],
			'html_escape' => htmlspecialchars($this->aValues[$sName], ENT_QUOTES, 'UTF-8'),
			'date_only' => static::GetPart($this->aValues[$sName], 'Y-m-d'),
			'time_only' => static::GetPart($this->aValues[$sName], 'H:i:s'),
			default => throw new Exception("Unknown verb '$sVerb' in placeholder '$sPlaceholder'."),
		};

	}

}
