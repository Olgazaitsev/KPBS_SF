<?php namespace Intervolga\Sed\Entities;

use Intervolga\Sed\Tables\ContractStatusTriggerTable;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class ContractStatusTrigger extends AbstractTrigger
{
    /**
     * @return \Bitrix\Main\Entity\Base
     */
    protected static function getEntity()
    {
        return ContractStatusTriggerTable::getEntity();
    }

    public static function getType()
    {
        return 'CONTRACT';
    }



    public function setOldProcessStatusId($value)
    {
        return $this->setFieldValue('OLD_PROCESS_STATUS_ID', $value);
    }

    public function setNewProcessStatusId($value)
    {
        return $this->setFieldValue('NEW_PROCESS_STATUS_ID', $value);
    }



    public function getOldProcessStatusId()
    {
        return $this->getFieldValue('OLD_PROCESS_STATUS_ID');
    }

    public function getNewProcessStatusId()
    {
        return $this->getFieldValue('NEW_PROCESS_STATUS_ID');
    }
}