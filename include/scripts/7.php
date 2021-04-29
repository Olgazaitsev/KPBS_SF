<?php
use \kpbs\custom\Utility;

if(Bitrix\Main\Loader::includeModule('kpbs.custom')) {
    echo "found";
    //\kpbs\custom\Utility::TestFunction();
    print_r(Utility::IsDealFieldsInvisibleEnabled(1));
}