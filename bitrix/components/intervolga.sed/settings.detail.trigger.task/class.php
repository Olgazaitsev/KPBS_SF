<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class SettingsDetailTriggerTaskComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        $this->arResult = $this->arParams['RESULT'];
        $this->includeComponentTemplate();
        $this->addJs();
    }

    protected function addJs()
    {
        \Bitrix\Main\Page\Asset::getInstance()->addString('<script>document.addEventListener("DOMContentLoaded", function() {
            var SedSettingsDetailTaskTriggerInstance = new SedSettingsDetailTaskTrigger(' . \CUtil::PhpToJSObject(array(
                'actions' => $this->arParams['JS_DATA']['ACTIONS'],
                'selectedActions' => $this->arParams['JS_DATA']['SELECTED_ACTIONS'],
//                'paramOptions' => $this->arParams['JS_DATA']['PARAM_OPTIONS'],
                'paramInputPrefix' => $this->arParams['JS_DATA']['PARAM_INPUT_PREFIX'],
                'actionInputPrefix' => $this->arParams['JS_DATA']['ACTION_INPUT_PREFIX']
            ), false, false, true) . ');
        });</script>');
    }
}