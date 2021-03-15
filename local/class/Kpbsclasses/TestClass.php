<?php

namespace local\Kpbsclasses;

class TestClass
{

    public static function init()
    {
        \Bitrix\Main\Loader::includeModule('main');
        if(\CModule::IncludeModule("crm"))
        {
            $arFilter = array('CLOSED'=>'Y');
            $arSelect = array('ID', 'ASSIGNED_BY_ID', 'CLOSED', 'CLOSEDATE', 'DATE_MODIFY');
            $obResDeal = \CCrmDeal::GetListEx(false,$arFilter,false,false,$arSelect);
            while ($arResDealfirst = $obResDeal->Fetch()) {
                echo "<pre>";
                print_r($arResDealfirst);
                echo "</pre>";
            }
        }



    }

    public static function OnSearchReindex(&$NS, $oCallback, $callback_method)
    {

        echo "okay";

    }

}
