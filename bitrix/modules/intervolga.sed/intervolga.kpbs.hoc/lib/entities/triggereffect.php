<?php namespace Intervolga\Sed\Entities;

use Bitrix\Main\ObjectNotFoundException;
use Intervolga\Sed\Tables\TriggerEffectTable;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class TriggerEffect extends TableElement
{
    /*
     * --------------------------------
     * ------- Собственные поля -------
     * --------------------------------
     */

    /**
     * @param $value
     * @return $this
     */
    public function setTriggerId($value)
    {
        return $this->setFieldValue('TRIGGER_ID', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setActionId($value)
    {
        return $this->setFieldValue('ACTION_ID', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setParamId($value)
    {
        return $this->setFieldValue('PARAM_ID', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setParamValue($value)
    {
        if(is_array($value)) {
            $value = serialize($value);
        }

        return $this->setFieldValue('PARAM_VALUE', $value);
    }



    public function getTriggerId()
    {
        return $this->getFieldValue('TRIGGER_ID');
    }

    public function getTriggerType()
    {
        return $this->getFieldValue('TRIGGER_TYPE');
    }

    public function getActionId()
    {
        return $this->getFieldValue('ACTION_ID');
    }

    public function getParamId()
    {
        return $this->getFieldValue('PARAM_ID');
    }

    public function getParamValue()
    {
        $value = $this->getFieldValue('PARAM_VALUE');
        $processedValue = @unserialize($value);

        if($value === 'b:0;' || $processedValue !== false) {
            return $processedValue;
        }
        else {
            return $value;
        }
    }

    /*
     * -----------------------------------------------
     * ---- Поля связанных таблиц (без alias'ов) -----
     * -----------------------------------------------
     */
    public function getContractTriggerProcessId()
    {
        return $this->getFieldValue('INTERVOLGA_SED_TABLES_TRIGGER_EFFECT_CONTRACT_TRIGGER_PROCESS_ID');
    }

    public function getContractTriggerOldProcessStatusId()
    {
        return $this->getFieldValue('INTERVOLGA_SED_TABLES_TRIGGER_EFFECT_CONTRACT_TRIGGER_OLD_PROCESS_STATUS_ID');
    }

    public function getContractTriggerNewProcessStatusId()
    {
        return $this->getFieldValue('INTERVOLGA_SED_TABLES_TRIGGER_EFFECT_CONTRACT_TRIGGER_NEW_PROCESS_STATUS_ID');
    }

    public function getActionCode()
    {
        return $this->getFieldValue('INTERVOLGA_SED_TABLES_TRIGGER_EFFECT_ACTION_CODE');
    }

    public function getParamCode()
    {
        return $this->getFieldValue('INTERVOLGA_SED_TABLES_TRIGGER_EFFECT_PARAM_CODE');
    }


    /**
     * @return \Bitrix\Main\Entity\Base
     */
    protected static function getEntity()
    {
        return TriggerEffectTable::getEntity();
    }

    public static function createEmpty($triggerType = null)
    {
        if(!static::checkTriggerType($triggerType)) {
            $triggerType = null;
        }

        return parent::createEmpty()
            ->setFieldValue('TRIGGER_TYPE', $triggerType);

    }

    protected static function checkTriggerType($triggerType)
    {
        return (
            ContractStatusTrigger::getType() == $triggerType ||
            TaskStatusTrigger::getType() == $triggerType ||
            TaskGroupStatusTrigger::getType() == $triggerType
        );
    }

    /**
     * Выбираем данные из таблицы intervolga_sed_trigger_effect, используя JOIN слудующих таблиц
     * 1. intervolga_sed_contract_status_trigger
     * 2. intervolga_sed_action
     * 3. intervolga_sed_action_param
     *
     * @param $processId
     * @param $oldProcessStatusId
     * @param $newProcessStatusId
     * @return static[]
     */
    public static function getContractActionListWithParams($processId, $oldProcessStatusId, $newProcessStatusId)
    {
        $filter = array(
            'CONTRACT_TRIGGER.PROCESS_ID' => $processId,
            '=TRIGGER_TYPE' => ContractStatusTrigger::getType(),
            '!ACTION.CODE' => false,
            '!PARAM.CODE' => false,
            '!TRIGGER_ID' => false,
            array(
                'LOGIC' => 'OR',
                array(
                    'CONTRACT_TRIGGER.OLD_PROCESS_STATUS_ID' => $oldProcessStatusId,
                    'CONTRACT_TRIGGER.NEW_PROCESS_STATUS_ID' => $newProcessStatusId
                ),
                array(
                    'CONTRACT_TRIGGER.OLD_PROCESS_STATUS_ID' => false,
                    'CONTRACT_TRIGGER.NEW_PROCESS_STATUS_ID' => $newProcessStatusId
                ),
                array(
                    'CONTRACT_TRIGGER.OLD_PROCESS_STATUS_ID' => $oldProcessStatusId,
                    'CONTRACT_TRIGGER.NEW_PROCESS_STATUS_ID' => false
                ),
            )
        );

        $select = array(
            'CONTRACT_TRIGGER.PROCESS_ID',
            'CONTRACT_TRIGGER.OLD_PROCESS_STATUS_ID',
            'CONTRACT_TRIGGER.NEW_PROCESS_STATUS_ID',
            'ACTION.CODE',
            'PARAM.CODE',
        );

        return static::getListByFilter($filter, array(), $select);
    }

//    public static function onContractUpdate($contractId, $contractData)
//    {
//        $oldContract = Contract::getById($contractId);
//        $newProcessStatusId = (int)$contractData['PROCESS_STATUS_ID'];
//        $oldProcessStatusId = (int)$oldContract->getProcessStatusId();
//
//        if($oldProcessStatusId == $newProcessStatusId) {
//            return;
//        }
//
//        $contactActionListWithParams = static::getContractActionListWithParams($oldContract->getProcessId(), $oldProcessStatusId, $newProcessStatusId);
//
//        $actionListInfo = array();
//        foreach($contactActionListWithParams as $item) {
//            $actionListInfo[$item->getTriggerId()][$item->getActionCode()][$item->getParamCode()] = $item->getParamValue();
//        }
//
//        foreach ($actionListInfo as $actionInfo) {
//            \Intervolga\Sed\Tools\Utils::performAction($actionInfo, array_merge($contractData, array('ID' => $contractId)));
//        }
//    }

    /**
     * @param $contractId
     * @param $originatorId
     * @param $responsibleId
     * @param $originatorRoleId
     * @param $responsibleRoleId
     * @param $oldTaskStatusId
     * @param $newTaskStatusId
     * @return array|static[]
     */
    public static function getTaskActionListWithParams($contractId, $originatorId, $responsibleId, $originatorRoleId, $responsibleRoleId, $oldTaskStatusId, $newTaskStatusId)
    {
        if(!$contractId || !$originatorId || !$responsibleId || !$originatorRoleId || !$responsibleRoleId || !$oldTaskStatusId || !$newTaskStatusId) {
            return array();
        }

        try {
            $contract = Contract::getById($contractId);
        } catch (ObjectNotFoundException $exception) {
             return array();
        }

        $filter = array(
            'TASK_TRIGGER.PROCESS_ID' => $contract->getProcessId(),
            '=TRIGGER_TYPE' => TaskStatusTrigger::getType(),
            '!ACTION.CODE' => false,
            '!PARAM.CODE' => false,
            '!TRIGGER_ID' => false,
            array(
                'LOGIC' => 'OR',
                array('TASK_TRIGGER.PROCESS_STATUS_ID' => $contract->getProcessStatusId()),
                array('TASK_TRIGGER.PROCESS_STATUS_ID' => false)
            ),
            array(
                'LOGIC' => 'OR',
                array('TASK_TRIGGER.RESPONSIBLE_ROLE_ID' => $responsibleRoleId),
                array('TASK_TRIGGER.RESPONSIBLE_ROLE_ID' => false)
            ),
            array(
                'LOGIC' => 'OR',
                array('TASK_TRIGGER.ORIGINATOR_ROLE_ID' => $originatorRoleId),
                array('TASK_TRIGGER.ORIGINATOR_ROLE_ID' => false)
            ),
            array(
                'LOGIC' => 'OR',
                array(
                    'TASK_TRIGGER.OLD_UF_STATUS_ID' => $oldTaskStatusId,
                    'TASK_TRIGGER.NEW_UF_STATUS_ID' => $newTaskStatusId
                ),
                array(
                    'TASK_TRIGGER.OLD_UF_STATUS_ID' => false,
                    'TASK_TRIGGER.NEW_UF_STATUS_ID' => $newTaskStatusId
                ),
                array(
                    'TASK_TRIGGER.OLD_UF_STATUS_ID' => $oldTaskStatusId,
                    'TASK_TRIGGER.NEW_UF_STATUS_ID' => false
                ),
            )
        );

        $select = array(
            'TASK_TRIGGER.PROCESS_ID',
            'TASK_TRIGGER.PROCESS_STATUS_ID',
            'TASK_TRIGGER.OLD_UF_STATUS_ID',
            'TASK_TRIGGER.NEW_UF_STATUS_ID',
            'TASK_TRIGGER.RESPONSIBLE_ROLE_ID',
            'TASK_TRIGGER.ORIGINATOR_ROLE_ID',
            'ACTION.CODE',
            'PARAM.CODE',
        );

        return static::getListByFilter($filter, array(), $select);
    }

    /*
     * 1. Выбираем  все задачи данной группы (с максимальным ID инстанса данной группы)
     * 2. Получаем все статусы задач (+ учитываем потенциально обновленный новый статус)
     * 3. Получаем все условия из т-цы group_trigger (с фильтром по process_id, group_id, ...)
     * 4. Цикл по условиям (Проверяем пересечение условий)
     *      - "all_in" - все статусы задач должны быть равны значению "all_in"
     *      - "all_out" - все статусы задач должны быть НЕ равны значениям из "all_out"
     *      - "anyone_in" - статус хотя бы одной задачи равен значению "anyone_in"
     *      - "anyone_out" - существует хотя бы одна задача со статусом, не принадлежащим значениям "anyone_out"
     */
    public static function getTaskGroupActionListWithParams($taskId, $taskTypeId, $contractId, $groupId, $originatorId, $newTaskStatusId)
    {
        if(!$taskId || !$taskTypeId || !$contractId || !$originatorId || !$newTaskStatusId || !$groupId) {
            return array();
        }

        try {
            $contractData = Contract::getOneByFilter(
                array('ID' => $contractId, 'PARTICIPANT.USER_ID' => $originatorId),
                array(),
                array('PARTICIPANT.USER_ID', 'PARTICIPANT.ROLE_ID')
            );
        }
        catch (\Bitrix\Main\ObjectNotFoundException $e) {
            return array();
        }

        $processId = $contractData->getProcessId();
        $processStatusId = $contractData->getProcessStatusId();
        $originatorRoleId = $contractData->getReferenceRoleId();
        $lastGroupInstanceId = self::getLastGroupInstanceId($contractId, $groupId);

        $groupTasks = \Intervolga\Sed\Tools\Utils::getTaskData(
            array(
                'ID' => ContractTask::getTaskIdsByFilter(
                    array(
                        'GROUP_ID' => $groupId,
                        'CONTRACT_ID' => $contractId,
                        'GROUP_INSTANCE_ID' => $lastGroupInstanceId
                    )
                ),
                TaskTypeField::TASK_TYPE_FIELD_NAME => $taskTypeId
            ),
            array('ID', 'UF_*')
        );

        $groupTasksStatuses = array();
        foreach ($groupTasks as $arTask) {
            $groupTasksStatuses[$arTask['ID']] = ($arTask['ID'] == $taskId) ? $newTaskStatusId : $arTask[TaskStatusField::getFieldNamePrefix() . $taskTypeId];
        }

        $triggers = TaskGroupStatusTrigger::getListByFilter(array(
            'PROCESS_ID' => $processId,
            'GROUP_ID' => $groupId,
            'PROCESS_STATUS_ID' => $processStatusId,
            array(
                'LOGIC' => 'OR',
                array('ORIGINATOR_ROLE_ID' => $originatorRoleId),
                array('ORIGINATOR_ROLE_ID' => false)
            ),
        ));

        if(!count($triggers)) {
            return array();
        }

        $triggerIds = array();
        foreach ($triggers as $trigger) {
            if($trigger->checkAllInStatus($groupTasksStatuses) &&
                $trigger->checkAnyOneInStatus($groupTasksStatuses) &&
                $trigger->checkAllOutOfStatuses($groupTasksStatuses) &&
                $trigger->checkAnyOutOfStatuses($groupTasksStatuses)
            ) {
                $triggerIds[] = $trigger->getId();
            }
        }

        if(!count($triggerIds)) {
            return array();
        }

        return static::getListByFilter(
            array(
                'TRIGGER_ID' => $triggerIds,
                '=TRIGGER_TYPE' => TaskGroupStatusTrigger::getType(),
                '!ACTION.CODE' => false,
                '!PARAM.CODE' => false,
            ),
            array(),
            array(
                'ACTION.CODE',
                'PARAM.CODE',
            )
        );
    }

    protected static function getLastGroupInstanceId($contractId, $taskGroupId)
    {
        try {
            $lastGroupTask = ContractTask::getOneByFilter(
                array(
                    'CONTRACT_ID' => $contractId,
                    'GROUP_ID' => $taskGroupId
                ),
                array(
                    'GROUP_INSTANCE_ID' => 'DESC'
                )
            );

            return intval($lastGroupTask->getGroupInstanceId());
        } catch (\Bitrix\Main\ObjectNotFoundException $exception) {
            return 0;
        }
    }
}