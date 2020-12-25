<?php namespace Intervolga\Sed\Entities;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class TaskTypeField extends EnumerableUfElement
{
    const TASK_ENTITY_ID = 'TASKS_TASK';
    const TASK_TYPE_FIELD_NAME = 'UF_TASK_TTYPE';
    const DEFAULT_CAPTION = 'Default caption';

    public static function getDefaultCaption()
    {
        return Loc::getMessage('C.TASKTYPEFIELD.DEFAULT_CAPTION');
    }

    public static function createEmpty()
    {
        return parent::createEmpty()
            ->setEntityId(static::TASK_ENTITY_ID)
            ->setFieldName(static::TASK_TYPE_FIELD_NAME)
            ->setFieldLabel(Loc::getMessage('C.TASKTYPEFIELD.TASK_TYPE_LABEL'));
    }

    protected static function makeArrayFromEntityFilter($entityFilter = null)
    {
        $filter = array(
            'ENTITY_ID' => static::TASK_ENTITY_ID,
            'FIELD_NAME' => static::TASK_TYPE_FIELD_NAME,
        );
        $parentFilter = parent::makeArrayFromEntityFilter();

        if(is_array($parentFilter)) {
            $filter = array_merge($parentFilter, $filter);
        }

        return $filter;
    }

    public static function getOne()
    {
        return static::getOneByFilter(array());
    }

    // запрещаем удаление пользовательского поля ТЗ
    protected static function deleteById($id)
    {
        throw new \Bitrix\Main\InvalidOperationException(Loc::getMessage('C.TASKTYPEFIELD.TASK_TYPE_DELETE_ERROR'));
    }
}