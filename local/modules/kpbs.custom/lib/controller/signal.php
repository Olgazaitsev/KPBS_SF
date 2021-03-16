<?php
namespace kpbs\custom\Controller;

use Bitrix\Main;
use Bitrix\Crm;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Config\Option;

class Signal extends Controller
{
    public function getSignalAction($user, $from)
    {
        $signalarr = [1,2,3];
        return $signalarr;
    }
}

