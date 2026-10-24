<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.261024
 */

namespace JeffreyBostoenExtensions\MailToTicket;

use Exception;

/**
 * Class PlaceholderValues. A set of text values that can be used as placeholders, with the same syntax as the attributes of an iTop object.
 *
 * Passed as "<name>->object()" in the context arguments of an action. iTop then resolves "$<name>->...$" through GetForTemplate():
 * - "$mail->subject$": the value as it is.
 * - "$mail->html(subject)$": the value, escaped for use in an HTML body. Same verb as for iTop objects (e.g. "$ticket->html(title)$").
 *
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
	 * Resolves a placeholder. Called by iTop (MetaModel::ApplyParams()).
	 *
	 * @param string $sPlaceholder Name of the value, or "verb(name)". Supported verb: "html".
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
			'html' => htmlspecialchars($this->aValues[$sName], ENT_QUOTES, 'UTF-8'),
			default => throw new Exception("Unknown verb '$sVerb' in placeholder '$sPlaceholder'."),
		};

	}

}
