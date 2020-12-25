<?php namespace Intervolga\Sed\Entities;

use Intervolga\Sed\Tables\ProcessTaskGroupTable;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class ProcessTaskGroup extends TableElement
{
    /**
     * @return \Bitrix\Main\Entity\Base
     */
    protected static function getEntity()
    {
        return ProcessTaskGroupTable::getEntity();
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
    public function setName($value)
    {
        return $this->setFieldValue('NAME', $value);
    }


    public function getProcessId()
    {
        return $this->getFieldValue('PROCESS_ID');
    }

    public function getName()
    {
        return $this->getFieldValue('NAME');
    }


    /**
     * @param $groupId
     * @return bool
     */
    public static function isUsedInTaskGroupStatusTriggers($groupId)
    {
        if(!$groupId) {
            return false;
        }

        $elements = \Intervolga\Sed\Entities\TaskGroupStatusTrigger::getListByFilter(array('GROUP_ID' => $groupId));
        return (!emptY($elements));
    }

    /**
     * @param $groupId
     * @return bool
     */
    public static function isUsedInTriggerEffects($groupId)
    {
        if(!$groupId) {
            return false;
        }

        $elements = \Intervolga\Sed\Entities\TriggerEffect::getListByFilter(
            array(
                'PARAM.CODE' => 'GROUP_ID',
                'PARAM_VALUE' => $groupId
            ),
            array(),
            array('PARAM.CODE')
        );

        return (!empty($elements));
    }
}