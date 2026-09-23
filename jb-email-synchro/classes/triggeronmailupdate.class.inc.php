<?php
// Copyright (C) 2012-2019 Combodo SARL
//
//   This program is free software; you can redistribute it and/or modify
//   it under the terms of the GNU Lesser General Public License as published by
//   the Free Software Foundation; version 3 of the License.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of the GNU General Public License
//   along with this program; if not, write to the Free Software
//   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
/**
 * @copyright   Copyright (c) 2012-2026 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

use JeffreyBostoenExtensions\MailToTicket\{
	Logger,
	ProcessingHelper
};

/**
 * To trigger notifications when a ticket is updated from an incoming eMail
 */
class TriggerOnMailUpdate extends TriggerOnObject
{
	public static function Init()
	{
		$aParams = array
		(
			"category" => "core/cmdb,application,grant_by_profile", // "application" category => admins can perform a CSV import
			"key_type" => "autoincrement",
			"name_attcode" => "description",
			"state_attcode" => "",
			"reconc_keys" => array(),
			"db_table" => "priv_trigger_onmailupdate",
			"db_key_field" => "id",
			"db_finalclass_field" => "",
			"display_template" => "",
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		
		// -- Display lists
		// Attributes to be displayed for the complete details
		MetaModel::Init_SetZListItems('details', array(
			'description',
			'target_class',
			'filter',
			'action_list',
			'subscription_policy',
		)); 
		
		// Attributes to be displayed for the complete details
		MetaModel::Init_SetZListItems('list', array(
			'finalclass', 
			'target_class', 
			'description'
		));

	}

	/**
	 * Activates the trigger, but only if the object is within the scope of the OQL filter (if any).
	 * Unlike TriggerOnObject, the filter is evaluated with the context arguments (e.g. :sender->id, :mail->subject).
	 *
	 * @param array $aContextArgs Context arguments. Expected: "this->object()", "sender->object()" and the scalar "mail->..." arguments.
	 *
	 * @return void
	 *
	 * @throws ArchivedObjectException
	 * @throws CoreException
	 */
	public function DoActivate($aContextArgs) {

		$oObject = $aContextArgs['this->object()'] ?? null;
		$oSender = $aContextArgs['sender->object()'] ?? null;

		$this->Trace('Evaluating trigger "%1$s" for object %2$s (sender: %3$s).',
			$this->Get('description'),
			($oObject instanceof DBObject) ? get_class($oObject).'::'.$oObject->GetKey() : '(none)',
			($oSender instanceof DBObject) ? get_class($oSender).'::'.$oSender->GetKey() : '(unknown)'
		);

		if(!$this->IsTargetObjectInContext($aContextArgs)) {
			$this->Trace('Not activated: the object is not within the scope of the filter.');
			return;
		}

		// - Trigger::DoActivate() silently returns when the context tags do not match; this check only adds a trace.
		if(!$this->IsContextValid()) {
			$this->Trace('Not activated: the context tags (%1$s) do not match the current context.', implode(', ', $this->Get('context')->GetValues()));
			return;
		}

		// - Listing the actions requires additional queries, so it is only done when tracing is enabled.
		if(Logger::IsLogLevelEnabled(Logger::LEVEL_TRACE, Logger::CHANNEL_DEFAULT)) {
			$this->TraceActions();
		}

		// - Scalar arguments (e.g. the caller-controlled "mail->subject") are not escaped by MetaModel::ApplyParams(),
		//   while notifications are usually rendered as HTML. Escape them to prevent markup injection.
		foreach($aContextArgs as $sArgName => $mValue) {
			if(is_string($mValue)) {
				$aContextArgs[$sArgName] = htmlspecialchars($mValue, ENT_QUOTES, 'UTF-8');
			}
		}

		// - TriggerOnObject::DoActivate() is skipped on purpose: it evaluates the filter again, but without any query arguments.
		//   This would fail as soon as the filter refers to :sender or :mail.
		Trigger::DoActivate($aContextArgs);

		$this->Trace('Activated.');

	}

	/**
	 * Traces the actions linked to this trigger, and whether they will be executed.
	 *
	 * @return void
	 *
	 * @throws ArchivedObjectException
	 * @throws CoreException
	 */
	protected function TraceActions() : void {

		/** @var ormLinkSet $oLinkSet */
		$oLinkSet = $this->Get('action_list');

		if($oLinkSet->Count() === 0) {
			$this->Trace('No actions are linked to this trigger.');
			return;
		}

		foreach($oLinkSet as $oLink) {

			/** @var Action|null $oAction */
			$oAction = MetaModel::GetObject('Action', $oLink->Get('action_id'), false, true);

			if($oAction === null) {
				$this->Trace('Action %1$s: not found.', $oLink->Get('action_id'));
				continue;
			}

			$this->Trace('Action %1$s::%2$s "%3$s" (order %4$s): %5$s.',
				get_class($oAction),
				$oAction->GetKey(),
				$oAction->Get('name'),
				$oLink->Get('order'),
				$oAction->IsActive() ? 'will be executed' : 'skipped, not active (status: '.$oAction->Get('status').')'
			);

		}

		// - Trigger::DoActivate() iterates the same link set using Fetch(): reset the cursor.
		$oLinkSet->rewind();

	}

	/**
	 * Traces a message, prefixed with this trigger's class and ID.
	 *
	 * @param string $sMessage The message (sprintf format).
	 * @param mixed ...$args Arguments for the message.
	 *
	 * @return void
	 */
	protected function Trace(string $sMessage, ...$args) : void {

		ProcessingHelper::Trace('.. %1$s::%2$s: %3$s', get_class($this), $this->GetKey(), (count($args) > 0) ? sprintf($sMessage, ...$args) : $sMessage);

	}

	/**
	 * Checks whether the object ("this->object()") is within the scope of the OQL filter, evaluated with the context arguments.
	 *
	 * @param array $aContextArgs Context arguments.
	 *
	 * @return bool True if no filter is defined, or if the filter returns the object.
	 *
	 * @throws CoreException
	 * @throws MySQLException
	 * @throws OQLException
	 */
	public function IsTargetObjectInContext(array $aContextArgs) : bool {

		$sFilter = trim($this->Get('filter') ?? '');

		if($sFilter === '') {
			$this->Trace('No filter defined.');
			return true;
		}

		$this->Trace('Filter: %1$s', $sFilter);

		$oObject = $aContextArgs['this->object()'] ?? null;

		// - Without an object, the filter can not be applied.
		if(!($oObject instanceof DBObject)) {
			$this->Trace('No "this" object in the context: the filter can not be applied.');
			return false;
		}

		$oSearch = DBObjectSearch::FromOQL($sFilter);

		// - A filter on another class (branch) can never return this object.
		if(!is_a($oObject, $oSearch->GetClass())) {
			$this->Trace('The object class %1$s is not (a subclass of) the filter class %2$s.', get_class($oObject), $oSearch->GetClass());
			return false;
		}

		$oSearch->AddCondition('id', $oObject->GetKey(), '=');
		$oSearch->AllowAllData();

		// - Empty arguments (e.g. an unidentified sender) are left out, so a filter referring to them simply does not match.
		//   Passing a null object would cause a fatal error while resolving the query argument.
		$aQueryArgs = array_filter($aContextArgs, fn($mValue) => $mValue !== null);

		try {

			$oSet = new DBObjectSet($oSearch, [], $aQueryArgs);
			$bMatch = $oSet->CountExceeds(0);

			$this->Trace('The filter %1$s the object.', $bMatch ? 'returns' : 'does not return');
			return $bMatch;

		}
		catch(MissingQueryArgument $e) {

			// - Only the argument names are traced; the values (e.g. the e-mail body) may be large or sensitive.
			$this->Trace('The filter can not be applied (%1$s). Available arguments: %2$s', $e->getMessage(), implode(', ', array_keys($aQueryArgs)));
			return false;

		}

	}

}
