<?php

namespace Combodo\iTop\Extension\Helper;

use MetaModel;

class ImapOptionsHelper
{
	protected $aImapOptions;

	public function __construct()
	{
		$this->aImapOptions = MetaModel::GetModuleSetting('combodo-email-synchro', 'imap_options', ['imap']);
	}

	public function HasOption($sOption)
	{
		return in_array($sOption, $this->aImapOptions);
	}
}