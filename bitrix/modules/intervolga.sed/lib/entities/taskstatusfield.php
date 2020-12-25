<?php namespace Intervolga\Sed\Entities;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class TaskStatusField extends TaskTypeDependantField
{
    public static function getFieldLabel()
    {
        return Loc::getMessage('C.TASKSTATUSFIELD.FIELD_LABEL');
    }

    public static function getFieldNamePrefix()
    {
        return 'UF_TASK_CS_';
    }

    public static function createEmpty($entityFilter = null)
    {
        return parent::createEmpty($entityFilter)
            ->setFieldLabel(static::getFieldLabel());
    }

    public static function getAllTaskStatusFieldNames()
    {
        $fields = static::getListAll();
        $fieldNames = array();
        foreach($fields as $field) {
            $fieldNames[] = $field->getFieldName();
        }

        return $fieldNames;
    }
}