<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}

use Bitrix\Main\Localization\Loc;
use Intervolga\Sed\Subscription\Subscriptions;

class SettingsDetailComponent extends CBitrixComponent
{
	public function executeComponent()
	{
        if (!Subscriptions::checkForUiComponent()) {
            return null;
        }

		$this->arResult = $this->arParams['RESULT'];

		$errorText = $this->arResult['MESSAGES']['BODY'];
		if (strlen($errorText) > 0) {
			if (strpos($errorText, 'Duplicate entry') > 0 &&
				strpos($errorText, 'UC_sed_participant_role') > 0
			) {
				$this->arResult['MESSAGES']['BODY'] = Loc::getMessage('SED_DETAIL_COMP_V2.ERR_NAME_DUPLICATE');
			}
		}

		$this->includeComponentTemplate();
	}
}