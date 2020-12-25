<?php namespace Intervolga\Sed\Entities;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

abstract class TaskTypeDependantElement extends UfEnumElement
{
    protected static $isUfCreationAllowed = true;

    protected static function getFieldId($entityFilter = null)
    {
        throw new \Bitrix\Main\NotImplementedException();
    }

    public static function createEmpty($entityFilter = null)
    {
        return parent::createEmpty()
            ->setUserFieldId(static::getFieldId($entityFilter));
    }

    protected static function makeArrayFromEntityFilter($entityFilter = null)
    {
        return array('USER_FIELD_ID' => static::getFieldId($entityFilter));
    }
    
}