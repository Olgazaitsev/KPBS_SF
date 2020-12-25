<?php namespace Intervolga\Sed\Entities;

use Intervolga\Sed\Tables\ActionParamTable;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class ActionParam extends TableElement
{
    const PARAM_TYPE_ROLE = 'ROLE';
    const PARAM_TYPE_TASK_TYPE = 'T_TYPE';
    const PARAM_TYPE_PROCESS_STATUS = 'PROCESS_STATUS';
    const PARAM_TYPE_TASK_GROUP = 'T_GROUP';
    const PARAM_TYPE_STRING = 'STRING';
    const PARAM_TYPE_TEXTAREA = 'TEXTAREA';


    /**
     * @return \Bitrix\Main\Entity\Base
     */
    protected static function getEntity()
    {
        return ActionParamTable::getEntity();
    }

    /*
     * Собственные поля
     */

    public function getCode()
    {
        return $this->getFieldValue('CODE');
    }

    public function getActionId()
    {
        return $this->getFieldValue('ACTION_ID');
    }

    public function getName()
    {
        return $this->getFieldValue('NAME');
    }

    public function getType()
    {
        return $this->getFieldValue('TYPE');
    }

    public function isMultiple()
    {
        return (bool)$this->getFieldValue('IS_MULTIPLE');
    }

    public function isRequired()
    {
        return (bool)$this->getFieldValue('IS_REQUIRED');
    }

    /*
     * Поля свзянных таблиц
     */

    public function getReferenceActionName()
    {
        return $this->getFieldValue('INTERVOLGA_SED_TABLES_ACTION_PARAM_ACTION_NAME');
    }

    public function getReferenceActionCode()
    {
        return $this->getFieldValue('INTERVOLGA_SED_TABLES_ACTION_PARAM_ACTION_CODE');
    }

    /*
     * Прочие методы
     */

    public function hasOptions()
    {
        return (
            $this->getType() == static::PARAM_TYPE_ROLE ||
            $this->getType() == static::PARAM_TYPE_TASK_TYPE ||
            $this->getType() == static::PARAM_TYPE_PROCESS_STATUS ||
            $this->getType() == static::PARAM_TYPE_TASK_GROUP
        );
    }
}