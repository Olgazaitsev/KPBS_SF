<?php
use \kpbs\custom\filldatawarehouse;

if(Bitrix\Main\Loader::includeModule('kpbs.custom')) {
    //echo "found";
    //\kpbs\custom\Utility::TestFunction();
    \kpbs\custom\filldatawarehouse::executefilling();
}