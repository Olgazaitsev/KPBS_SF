<?php namespace Intervolga\Sed\Entities;

use Intervolga\Sed\Tables\ProcessTaskTypeTable;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class ProcessTaskType extends TableElement
{
    /**
     * @return \Bitrix\Main\Entity\Base
     */
    protected static function getEntity()
    {
        return ProcessTaskTypeTable::getEntity();
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
    public function setTaskTypeId($value)
    {
        return $this->setFieldValue('TASK_TYPE_ID', $value);
    }


    public function getProcessId()
    {
        return $this->getFieldValue('PROCESS_ID');
    }

    public function getTaskTypeId()
    {
        return $this->getFieldValue('TASK_TYPE_ID');
    }


    /**
     * @param ProcessTaskType[] $instances
     * @return array
     */
    public static function getTTypeIdsByEntityList($instances)
    {
        $ttypeIds = array();
        if(!empty($instances)) {
            foreach ($instances as $instance) {
                $ttypeIds[] = $instance->getTaskTypeId();
            }
        }
        return $ttypeIds;
    }

    /**
     * @param int $processTaskTypeId
     * @return bool
     */
    public static function isUsedInTriggerEffects($processTaskTypeId)
    {
        if(!$processTaskTypeId) {
            return false;
        }

        $elements = \Intervolga\Sed\Entities\TriggerEffect::getListByFilter(
            array(
                'PARAM.CODE' => 'UF_TASK_TTYPE',
                'PARAM_VALUE' => $processTaskTypeId
            ),
            array(),
            array('PARAM.CODE')
        );

        return (!empty($elements));
    }
}