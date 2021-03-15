<?php


namespace kpbs\custom;

use Bitrix\Main\Engine\Controller;

class kpicalcul extends Controller
{
    public function getStatAction($user, $from)
    {
        \Bitrix\Main\Diag\Debug::writeToFile($signals, "ajax", "__miros.log");
        return $user;
    }
}