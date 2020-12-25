<?php namespace Intervolga\Sed\Entities;

use Intervolga\Sed\Tables\ContractTaskTable;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class ContractTask extends TableElement
{
    /**
     * @return \Bitrix\Main\Entity\Base
     */
    protected static function getEntity()
    {
        return ContractTaskTable::getEntity();
    }


    /**
     * @param $value
     * @return $this
     */
    public function setTaskId($value)
    {
        return $this->setFieldValue('TASK_ID', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setTaskTypeId($value)
    {
        return $this->setFieldValue('TASK_TYPE_ID', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setContractId($value)
    {
        return $this->setFieldValue('CONTRACT_ID', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setMasterTask($value)
    {
        return $this->setFieldValue('IS_MASTER', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setCreatorRoleId($value)
    {
        return $this->setFieldValue('CREATOR_ROLE_ID', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setResponsibleRoleId($value)
    {
        return $this->setFieldValue('RESP_ROLE_ID', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setGroupId($value)
    {
        return $this->setFieldValue('GROUP_ID', $value);
    }

    /**
     * @param $value
     * @return ContractTask
     */
    public function setGroupInstanceId($value)
    {
        return $this->setFieldValue('GROUP_INSTANCE_ID', $value);
    }



    public function getTaskId()
    {
        return $this->getFieldValue('TASK_ID');
    }

    public function getTaskTypeId()
    {
        return $this->getFieldValue('TASK_TYPE_ID');
    }

    public function getContractId()
    {
        return $this->getFieldValue('CONTRACT_ID');
    }

    public function isMasterTask()
    {
        return $this->getFieldValue('IS_MASTER');
    }

    public function getCreatorRoleId()
    {
        return $this->getFieldValue('CREATOR_ROLE_ID');
    }

    public function getResponsibleRoleId()
    {
        return $this->getFieldValue('RESP_ROLE_ID');
    }

    public function getGroupId()
    {
        return $this->getFieldValue('GROUP_ID');
    }

    public function getGroupInstanceId()
    {
        return $this->getFieldValue('GROUP_INSTANCE_ID');
    }


    public function getReferenceContractName()
    {
        return $this->getFieldValue('INTERVOLGA_SED_TABLES_CONTRACT_TASK_CONTRACT_NAME');
    }

    public function getReferenceContractDaysToHarmonize()
    {
        return $this->getFieldValue('INTERVOLGA_SED_TABLES_CONTRACT_TASK_CONTRACT_DAYS_TO_HARMONIZE');
    }

    public function getReferenceContractFileId()
    {
        return $this->getFieldValue('INTERVOLGA_SED_TABLES_CONTRACT_TASK_CONTRACT_FILE_ID');
    }


    /**
     * @param array $filter
     * @return array
     */
    public static function getTaskIdsByFilter($filter)
    {
        $taskIds = array();

        $elements = static::getListByFilter($filter);
        foreach ($elements as $element) {
            $taskIds[] = $element->getTaskId();
        }

        return $taskIds;
    }
}