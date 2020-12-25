<?php namespace Intervolga\Sed\Entities;

use Intervolga\Sed\Tables\ProcessStatusTable;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class ProcessStatus extends TableElement
{
    const STATUS_CODE_NEW = 'NEW';
    const STATUS_CODE_PROGRESS = 'PROGRESS';
    const STATUS_CODE_PAUSED = 'PAUSED';
    const STATUS_CODE_APPROVED = 'APPROVED';
    const STATUS_CODE_NOT_APPROVED = 'NOT_APPROVED';

    /**
     * @param $code
     * @return mixed
     */
    public static function getStatusName($code)
    {
        $statusNames = array(
            'STATUS_NAME_NEW' => Loc::getMessage('PROCESSSTATUS.ST_N_NEW'),
            'STATUS_NAME_PROGRESS' => Loc::getMessage('PROCESSSTATUS.ST_N_PROGRESS'),
            'STATUS_NAME_PAUSED' => Loc::getMessage('PROCESSSTATUS.ST_N_PAUSED'),
            'STATUS_NAME_APPROVED' => Loc::getMessage('PROCESSSTATUS.ST_N_APPROVED'),
            'STATUS_NAME_NOT_APPROVED' => Loc::getMessage('PROCESSSTATUS.ST_N_NOT_APPROVED'),
        );

        return $statusNames[$code];
    }

    /**
     * @return \Bitrix\Main\Entity\Base
     */
    protected static function getEntity()
    {
        return ProcessStatusTable::getEntity();
    }


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
    public function setProcessId($value)
    {
        return $this->setFieldValue('PROCESS_ID', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setCode($value)
    {
        return $this->setFieldValue('CODE', $value);
    }


    public function getName()
    {
        return $this->getFieldValue('NAME');
    }

    public function getProcessId()
    {
        return $this->getFieldValue('PROCESS_ID');
    }

    public function getCode()
    {
        return $this->getFieldValue('CODE');
    }

    /**
     * @return bool
     */
    public static function isDefault($code)
    {
        return (
            $code == static::STATUS_CODE_NEW ||
            $code == static::STATUS_CODE_PROGRESS ||
            $code == static::STATUS_CODE_PAUSED ||
            $code == static::STATUS_CODE_APPROVED ||
            $code == static::STATUS_CODE_NOT_APPROVED
        );
    }

    public static function getFirstStatusByProcessId($processId)
    {
        return static::getOneByFilter(array(
            'PROCESS_ID' => $processId,
            'CODE' => static::STATUS_CODE_NEW
        ));
    }

    /**
     * @param $statusId
     * @return bool
     */
    public static function isUsedInContractStatusTriggers($statusId)
    {
        if(!$statusId) {
            return false;
        }

        $elements = \Intervolga\Sed\Entities\ContractStatusTrigger::getListByFilter(array(
            'LOGIC' => 'OR',
            array('OLD_PROCESS_STATUS_ID' => $statusId),
            array('NEW_PROCESS_STATUS_ID' => $statusId)
        ));

        return (!empty($elements));
    }

    /**
     * @param $statusId
     * @return bool
     */
    public static function isUSedInTaskStatusTriggers($statusId)
    {
        if(!$statusId) {
            return false;
        }

        $elements = \Intervolga\Sed\Entities\TaskStatusTrigger::getListByFilter(array('PROCESS_STATUS_ID' => $statusId));
        return (!empty($elements));
    }

    /**
     * @param $statusId
     * @return bool
     */
    public static function isUsedInTaskGroupStatusTriggers($statusId)
    {
        if(!$statusId) {
            return false;
        }

        $elements = \Intervolga\Sed\Entities\TaskGroupStatusTrigger::getListByFilter(array('PROCESS_STATUS_ID' => $statusId));
        return (!empty($elements));
    }

    /**
     * @param $statusId
     * @return bool
     */
    public static function isUSedInTriggerEffects($statusId)
    {
        if(!$statusId) {
            return false;
        }

        $elements = \Intervolga\Sed\Entities\TriggerEffect::getListByFilter(
            array(
                'PARAM.CODE' => 'PROCESS_STATUS_ID',
                'PARAM_VALUE' => $statusId
            ),
            array(),
            array('PARAM.CODE')
        );

        return (!empty($elements));
    }

    /**
     * @param int $processId
     * @param array $arOrder
     * @param array $arSelect
     * @param int $limit
     * @param int $offset
     * @return static[]
     */
    public static function getListByProcessId($processId, $arOrder = array(), $arSelect = array(), $limit = 0, $offset = 0)
    {
        return static::getListByFilter(array('PROCESS_ID' => $processId), $arOrder, $arSelect, $limit, $offset);
    }
}