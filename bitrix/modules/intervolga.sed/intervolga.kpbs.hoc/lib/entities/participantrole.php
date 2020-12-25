<?php namespace Intervolga\Sed\Entities;

use Intervolga\Sed\Tables\ParticipantRoleTable;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class ParticipantRole extends TableElement
{
    const INITIATOR_ROLE_NAME = 'Initiator';

    /**
     * @return mixed
     */
    public static function getInitiatorRoleName()
    {
        return Loc::getMessage('PARTICIPANTROLE_INITIATOR_ROLE_NAME');
    }

    /**
     * @return \Bitrix\Main\Entity\Base
     */
    protected static function getEntity()
    {
        return ParticipantRoleTable::getEntity();
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
    public function setDefaultUserId($value)
    {
        return $this->setFieldValue('DEFAULT_USER_ID', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setInitiatorFlag($value)
    {
        return $this->setFieldValue('IS_INITIATOR', $value);
    }


    public function getName()
    {
        return $this->getFieldValue('NAME');
    }

    public function getProcessId()
    {
        return $this->getFieldValue('PROCESS_ID');
    }

    public function getDefaultUserId()
    {
        return $this->getFieldValue('DEFAULT_USER_ID');
    }

    public function isInitiator()
    {
        return $this->getFieldValue('IS_INITIATOR');
    }


    /**
     * @param $roleId
     * @return bool
     */
    public static function isUsedInTaskStatusTriggers($roleId)
    {
        if(!$roleId) {
            return false;
        }

        $elements = \Intervolga\Sed\Entities\TaskStatusTrigger::getListByFilter(array(
            'LOGIC' => 'OR',
            array('RESPONSIBLE_ROLE_ID' => $roleId),
            array('ORIGINATOR_ROLE_ID' => $roleId)
        ));

        return (!empty($elements));
    }

    /**
     * @param $roleId
     * @return bool
     */
    public static function isUsedInTaskGroupStatusTriggers($roleId)
    {
        if(!$roleId) {
            return false;
        }

        $elements = \Intervolga\Sed\Entities\TaskGroupStatusTrigger::getListByFilter(array('ORIGINATOR_ROLE_ID' => $roleId));
        return (!empty($elements));
    }

    /**
     * @param $roleId
     * @return bool
     */
    public static function isUsedInTriggerEffects($roleId)
    {
        if(!$roleId) {
            return false;
        }

        $params = \Intervolga\Sed\Entities\TriggerEffect::getListByFilter(
            array(
                'PARAM.CODE' => array('RESPONSIBLE_ID', 'ORIGINATOR_ID'),
                'PARAM_VALUE' => $roleId
            ),
            array(),
            array('PARAM.CODE')
        );

        if(!empty($params)) {
            return true;
        }
        else {
            $taskGroupResponsibleParams = \Intervolga\Sed\Entities\TriggerEffect::getListByFilter(array('PARAM.CODE' => 'RESPONSIBLE_IDS'), array(), array('PARAM.CODE'));

            if(count($taskGroupResponsibleParams)) {
                foreach ($taskGroupResponsibleParams as $param) {
                    if(in_array($roleId, $param->getParamValue())) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param int $processId
     * @return static
     */
    public static function getProcessInitiator($processId)
    {
        return static::getOneByFilter(array(
            'PROCESS_ID' => $processId,
            'IS_INITIATOR' => true,
        ));
    }
}