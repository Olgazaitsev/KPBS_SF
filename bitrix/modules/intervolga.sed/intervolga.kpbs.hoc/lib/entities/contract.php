<?php namespace Intervolga\Sed\Entities;

use Intervolga\Sed\Tables\ContractTable;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Contract extends TableElement
{
    const DAYS_TO_HARMONIZE_DEFAULT = 1;

    /**
     * @return \Bitrix\Main\Entity\Base
     */
    protected static function getEntity()
    {
        return ContractTable::getEntity();
    }

    /*
     * --------------------------------
     * ------- Собственные поля -------
     * --------------------------------
     */

    /**
     * @param $value
     * @return $this
     */
    public function setName($value)
    {
        return $this->setFieldValue('NAME', $value);
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
    public function setProcessId($value)
    {
        return $this->setFieldValue('PROCESS_ID', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setFileId($value)
    {
        return $this->setFieldValue('FILE_ID', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setDaysToHarmonize($value)
    {
        return $this->setFieldValue('DAYS_TO_HARMONIZE', $value);
    }

    public function setUserFieldValue($fieldName, $value)
    {
        return $this->setFieldValue($fieldName, $value);
    }

    public function getName()
    {
        return $this->getFieldValue('NAME');
    }

    public function getProcessStatusId()
    {
        return $this->getFieldValue('PROCESS_STATUS_ID');
    }

    public function getProcessId()
    {
        return $this->getFieldValue('PROCESS_ID');
    }

    public function getFileId()
    {
        return $this->getFieldValue('FILE_ID');
    }

    public function getDaysToHarmonize()
    {
        return $this->getFieldValue('DAYS_TO_HARMONIZE');
    }

    /*
     * -----------------------------------------------
     * ---- Поля связанных таблиц (без alias'ов) -----
     * -----------------------------------------------
     */
    public function getReferenceProcessName()
    {
        return $this->getFieldValue('INTERVOLGA_SED_TABLES_CONTRACT_PROCESS_NAME');
    }

    public function getReferenceProcessStatusName()
    {
        return $this->getFieldValue('INTERVOLGA_SED_TABLES_CONTRACT_PROCESS_STATUS_NAME');
    }

    public function getReferenceProcessStatusCode()
    {
        return $this->getFieldValue('INTERVOLGA_SED_TABLES_CONTRACT_PROCESS_STATUS_CODE');
    }

    public function getReferenceTaskId()
    {
        return $this->getFieldValue('INTERVOLGA_SED_TABLES_CONTRACT_TASK_TASK_ID');
    }

    public function getReferenceRoleId()
    {
        return $this->getFieldValue('INTERVOLGA_SED_TABLES_CONTRACT_PARTICIPANT_ROLE_ID');
    }

    public function getReferenceUserId()
    {
        return $this->getFieldValue('INTERVOLGA_SED_TABLES_CONTRACT_PARTICIPANT_USER_ID');
    }

    public function getUserFieldValue($fieldName)
    {
        return $this->getFieldValue($fieldName);
    }

    /**
     * @return bool
     */
    public function cantBeDeleted()
    {
        global $USER;
        return !$USER->IsAdmin() &&
            (
                $this->getReferenceProcessStatusCode() != \Intervolga\Sed\Entities\ProcessStatus::STATUS_CODE_APPROVED &&
                $this->getReferenceProcessStatusCode() != \Intervolga\Sed\Entities\ProcessStatus::STATUS_CODE_NOT_APPROVED &&
                $this->getReferenceProcessStatusCode() != \Intervolga\Sed\Entities\ProcessStatus::STATUS_CODE_NEW
            );
    }
}