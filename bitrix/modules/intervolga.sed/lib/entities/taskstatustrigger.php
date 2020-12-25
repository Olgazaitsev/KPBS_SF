<?php namespace Intervolga\Sed\Entities;

use Intervolga\Sed\Tables\TaskStatusTriggerTable;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class TaskStatusTrigger extends AbstractTrigger
{
    /**
     * @return \Bitrix\Main\Entity\Base
     */
    protected static function getEntity()
    {
        return TaskStatusTriggerTable::getEntity();
    }

    public static function getType()
    {
        return 'TASK';
    }


    /**
     * @param $value
     * @return $this
     */
    public function setResponsibleRoleId($value)
    {
        return $this->setFieldValue('RESPONSIBLE_ROLE_ID', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setOriginatorRoleId($value)
    {
        return $this->setFieldValue('ORIGINATOR_ROLE_ID', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setProcessStatusId($value)
    {
        return $this->setFieldValue('PROCESS_STATUS_ID', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setNewUfStatusId($value)
    {
        return $this->setFieldValue('NEW_UF_STATUS_ID', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setOldUfStatusId($value)
    {
        return $this->setFieldValue('OLD_UF_STATUS_ID', $value);
    }



    public function getResponsibleRoleId()
    {
        return $this->getFieldValue('RESPONSIBLE_ROLE_ID');
    }

    public function getOriginatorRoleId()
    {
        return $this->getFieldValue('ORIGINATOR_ROLE_ID');
    }

    public function getProcessStatusId()
    {
        return $this->getFieldValue('PROCESS_STATUS_ID');
    }

    public function getNewUfStatusId()
    {
        return $this->getFieldValue('NEW_UF_STATUS_ID');
    }

    public function getOldUfStatusId()
    {
        return $this->getFieldValue('OLD_UF_STATUS_ID');
    }


    /**
     * @param $statusId
     * @return bool
     */
    public static function isStatusUsed($statusId)
    {
        if(!$statusId) {
            return false;
        }

        $cnt = static::getCountByFilter(array(
            'LOGIC' => 'OR',
            array('NEW_UF_STATUS_ID' => $statusId),
            array('OLD_UF_STATUS_ID' => $statusId)
        ));

        return (bool)$cnt;
    }
}