<?php namespace Intervolga\Sed\Entities;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class ContractTriggerEffect extends TriggerEffect
{
    protected static function getTriggerTypeStatic()
    {
        return 'CONTRACT';
    }
}