<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Intervolga\Sed\Subscription\Subscriptions;

class SettingsListComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        if (!Subscriptions::checkForUiComponent()) {
            return null;
        }

        $this->arResult = $this->arParams['RESULT'];
        $this->includeComponentTemplate();
        $this->addJs();
    }

    protected function addJs()
    {
        $params = array(
            'detailUrl' => $this->arParams['JS_DATA']['DETAIL_PAGE_URL'],
            'detailPageParam' => $this->arParams['JS_DATA']['DETAIL_PAGE_PARAM'],
            'sessId' => bitrix_sessid(),
            'ajaxUrl' => $this->arParams['JS_DATA']['AJAX_URL'],
            'deleteActionName' => $this->arParams['JS_DATA']['DELETE_ACTION_NAME'],
            'deleteActionExtraParams' => $this->arParams['JS_DATA']['DELETE_ACTION_EXTRA_PARAMS'],
            'disableDeleteAction' => $this->arParams['JS_DATA']['DISABLE_DELETE_ACTION']
        );

        \Bitrix\Main\Page\Asset::getInstance()->addString('<script>document.addEventListener("DOMContentLoaded", function() {
            var CSTSettingsListInstance = new CSTSettingsList(' . \CUtil::PhpToJSObject($params, false, false, true) . ');
        });</script>');
    }
}