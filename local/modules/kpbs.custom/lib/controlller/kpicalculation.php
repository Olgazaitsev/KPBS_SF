<?php

namespace kpbs\custom\controller;

use Bitrix\Main;
use Bitrix\Crm;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Config\Option;

class kpicalculation extends Controller
{
    public function getStatistics($params)
    {

        $listtable = array(1,2,3);
        return $listtable;
    }
}