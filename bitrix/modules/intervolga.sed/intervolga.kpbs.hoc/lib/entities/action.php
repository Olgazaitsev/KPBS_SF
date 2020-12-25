<?php namespace Intervolga\Sed\Entities;

use Bitrix\Main\ObjectNotFoundException;
use Intervolga\Sed\Tables\ActionTable;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Action extends TableElement
{
    const ACTION_PREFIX = 'action';


    /**
     * @return \Bitrix\Main\Entity\Base
     */
    protected static function getEntity()
    {
        return ActionTable::getEntity();
    }


    public function setName($value)
    {
        return $this->setFieldValue('NAME', $value);
    }

    public function setCode($value)
    {
        return $this->setFieldValue('CODE', $value);
    }


    public function getName()
    {
        return $this->getFieldValue('NAME');
    }

    public function getCode()
    {
        return $this->getFieldValue('CODE');
    }

    /**
     * @param array $actionInfo - массив с информацией о дейтсвиях, которые необходимо выполнить
     * @param array $additionalParams - может сожержать данные об экземпляре согласования и задаче, из которой было вызвано дейтсвие
     */
    public static function performAction($actionInfo, $additionalParams)
    {
        if(!empty($actionInfo) && is_array($actionInfo)) {
            foreach ($actionInfo as $actionKey => $actionParams) {
                $action = static::ACTION_PREFIX . $actionKey;
                if(method_exists(__CLASS__, $action)) {
                    static::$action($actionParams, $additionalParams);
                }
            }
        }
    }

    /**
     * Параметры по умолчанию ( intervolga_sed_action_param.code )
     * $params['ORIGINATOR_ID'] - id постановщика задачи
     * $params['UF_TASK_TTYPE'] - id типа создаваемых задач
     * $params['GROUP_ID'] - id группы задач
     * $params['RESPONSIBLE_IDS'] - список id ответственных
     * $params['TASK_TITLE'] - название задачи
     * $params['TASK_DESC'] - описание задачи
     */
    protected static function actionCreateTaskGroup($params, $additionalParams)
    {
        $originatorRoleId = (int)$params['ORIGINATOR_ID'];
        $taskType = (int)$params['UF_TASK_TTYPE'];
        $groupId = (int)$params['GROUP_ID'];
        $responsibleRoleIds = $params['RESPONSIBLE_IDS'];

        if($originatorRoleId < 1 || $taskType < 1 || $groupId < 1 || !is_array($responsibleRoleIds) || !count($responsibleRoleIds)) {
            throw new \Bitrix\Main\ArgumentNullException('Some required params are missing');
        }

        $participantIdList = Participant::getUserIdList($additionalParams['CONTRACT']['ID']);
        $taskTypeInstance = TaskTypeElement::getById($taskType);
        $taskDefaultStatus = TaskStatusElement::getOneByFilter(array(), $taskTypeInstance, array('ID' => 'ASC'));
        $taskStatusFieldName = TaskStatusField::getFieldNameByEntityFilter($taskTypeInstance);

        $endDateTime = new \DateTime('now + ' . ($additionalParams['CONTRACT']['DAYS_TO_HARMONIZE'] + 1). ' days');

        $taskParams = array(
            'DESCRIPTION_IN_BBCODE' => 'N',
            //'START_DATE_PLAN' => ConvertTimeStamp($startDateTime->getTimestamp(), 'SHORT'),
            'END_DATE_PLAN' => ConvertTimeStamp($endDateTime->getTimestamp(), 'SHORT'),
            'DEADLINE' => ConvertTimeStamp($endDateTime->getTimestamp(), 'SHORT'),
            'CREATED_BY' => $participantIdList[$originatorRoleId],
            'UF_TASK_TTYPE' => $taskTypeInstance->getId(),
            $taskStatusFieldName => $taskDefaultStatus->getId(),
        );

        // Название задачи
        if(is_string($params['TASK_TITLE']) && mb_strlen($params['TASK_TITLE'])) {
            $taskParams['TITLE'] = $params['TASK_TITLE'];
        }
        else {
            $taskParams['TITLE'] = Loc::getMessage('C.ACTION.TIT_NEGOTIATION_OF_CONTRACT') . ' | ' . $additionalParams['CONTRACT']['NAME'];
        }

        // Описание задачи
        $taskParams['DESCRIPTION'] = static::getNewTaskDescription($additionalParams['CONTRACT']['ID'], $params['TASK_DESC']);

        // Файл, связанный с задачей
        if($additionalParams['CONTRACT']['FILE_ID']) {
            $taskParams['UF_TASK_WEBDAV_FILES'] = array('n' . $additionalParams['CONTRACT']['FILE_ID']);
        }
    
	    /**
         * Инстанс группы задач
         * @see https://youtrack.ivsupport.ru/issue/RD_SED-26
         */
        $groupInstanceId = self::getNextGroupInstanceId($additionalParams['CONTRACT']['ID'], $groupId);

        foreach ($responsibleRoleIds as $responsibleRoleId) {
            // делаем наблюдателями всех, кроме постановщика и ответственного
            $taskParams['AUDITORS'] = array();
            foreach ($participantIdList as $roleId => $userId) {
                if(!($roleId == $originatorRoleId || $roleId == $responsibleRoleId)) {
                    $taskParams['AUDITORS'][] = $userId;
                }
            }

            $taskParams['RESPONSIBLE_ID'] = $participantIdList[$responsibleRoleId];
            \Intervolga\Sed\Tools\Utils::createContractRelatedTask(
                $taskParams,
                $participantIdList[$originatorRoleId],
                $originatorRoleId,
                $responsibleRoleId,
                $additionalParams['CONTRACT']['ID'],
                $taskTypeInstance->getId(),
                $groupId,
                false,
                $groupInstanceId
            );
        }
    }

    /*
     * Параметры по умолчанию ( intervolga_sed_action_param.code )
     * $params['RESPONSIBLE_ID'] - id роли ответственного
     * $params['CREATED_BY'] - id роли постановщика
     * $params['UF_TASK_TTYPE'] - id типа задач
     * $params['TASK_TITLE'] - название задачи
     * $params['TASK_DESC'] - описание задачи
     */
    protected static function actionCreateTask($params, $additionalParams)
    {
        $responsibleRoleId = (int)$params['RESPONSIBLE_ID'];
        $originatorRoleId = (int)$params['ORIGINATOR_ID'];
        $taskType = (int)$params['UF_TASK_TTYPE'];

        if($responsibleRoleId < 1 || $originatorRoleId < 1 || $taskType < 1) {
            throw new \Bitrix\Main\ArgumentNullException(Loc::getMessage('C.ACTION.MISSING_REQUIRED_PARAMS'));
        }

        // получаем список всех участников согласования
        $participantIdList = Participant::getUserIdList($additionalParams['CONTRACT']['ID']);

        // формируем список наблюдателей - все, кроме постановщика и ответственного
        $auditorsIds = array();
        foreach ($participantIdList as $roleId => $userId) {
            if(!($roleId == $responsibleRoleId || $roleId == $originatorRoleId)) {
                $auditorsIds[] = $userId;
            }
        }

        $taskTypeInstance = TaskTypeElement::getById($taskType);
        $taskDefaultStatus = TaskStatusElement::getOneByFilter(array(), $taskTypeInstance, array('ID' => 'ASC'));
        $taskStatusFieldName = TaskStatusField::getFieldNameByEntityFilter($taskTypeInstance);

        $endDateTime = new \DateTime('now + ' . ($additionalParams['CONTRACT']['DAYS_TO_HARMONIZE'] + 1). ' days');

        $taskParams = array(
            'DESCRIPTION_IN_BBCODE' => 'N',
            //'START_DATE_PLAN' => ConvertTimeStamp($startDateTime->getTimestamp(), 'SHORT'),
            'END_DATE_PLAN' => ConvertTimeStamp($endDateTime->getTimestamp(), 'SHORT'),
            'DEADLINE' => ConvertTimeStamp($endDateTime->getTimestamp(), 'SHORT'),
            'RESPONSIBLE_ID' => $participantIdList[$responsibleRoleId],
            'CREATED_BY' => $participantIdList[$originatorRoleId],
            'UF_TASK_TTYPE' => $taskTypeInstance->getId(),
            $taskStatusFieldName => $taskDefaultStatus->getId(),
            'AUDITORS' => $auditorsIds, //наблюдатели задачи
        );

        // Название задачи
        if(is_string($params['TASK_TITLE']) && mb_strlen($params['TASK_TITLE'])) {
            $taskParams['TITLE'] = $params['TASK_TITLE'];
        }
        else {
            $taskParams['TITLE'] = Loc::getMessage('C.ACTION.TIT_NEGOTIATION_OF_CONTRACT') . Loc::getMessage('C.ACTION.SEPARATOR') . $additionalParams['CONTRACT']['NAME'];
        }

        // Описание задачи
        $taskParams['DESCRIPTION'] = static::getNewTaskDescription($additionalParams['CONTRACT']['ID'], $params['TASK_DESC']);

        // Файл, связанный с задачей
        if($additionalParams['CONTRACT']['FILE_ID']) {
            $taskParams['UF_TASK_WEBDAV_FILES'] = array('n' . $additionalParams['CONTRACT']['FILE_ID']);
        }

        \Intervolga\Sed\Tools\Utils::createContractRelatedTask($taskParams, $participantIdList[$originatorRoleId], $originatorRoleId, $responsibleRoleId, $additionalParams['CONTRACT']['ID'], $taskTypeInstance->getId());
    }

    /*
     * Параметры по умолчанию ( intervolga_sed_action_param.code )
     * $params['PROCESS_STATUS_ID'] - id нового статуса
    */
    protected static function actionUpdateContractStatus($params, $additionalParams)
    {
        $contractId = $additionalParams['CONTRACT']['ID'];
        $newProcessStatus = $params['PROCESS_STATUS_ID'];

        if($contractId && $newProcessStatus) {
            Contract::getById($contractId)
                ->setProcessStatusId($newProcessStatus)
                ->save();
        }
    }

    /*
     * Параметры по умолчанию
     * $params['RESPONSIBLE_ID'] - id роли нового ответственного по задаче
     * $params['ORIGINATOR_ID'] - id роли нового постановщика задачи
     */
    protected static function actionUpdateTask($params, $additionalParams)
    {
        $originatorRoleId = (int)$params['ORIGINATOR_ID'];
        $responsibleRoleId = (int)$params['RESPONSIBLE_ID'];
        $taskId = (int)$additionalParams['TASK']['ID'];

        if($responsibleRoleId < 1 || $originatorRoleId < 1 || $taskId < 1) {
            throw new \Bitrix\Main\ArgumentNullException(Loc::getMessage('C.ACTION.MISSING_REQUIRED_PARAMS'));
        }

        $participantsIdList = Participant::getUserIdList($additionalParams['CONTRACT']['ID']);

        if($participantsIdList[$originatorRoleId] && $participantsIdList[$responsibleRoleId]) {
            $updateTaskParams = array(
                'CREATED_BY' => $participantsIdList[$originatorRoleId],
                'RESPONSIBLE_ID' => $participantsIdList[$responsibleRoleId],
                'AUDITORS' => array()
            );

            // наблюдатели в задаче - все участники процесса, кроме постановщика и ответственного
            foreach ($participantsIdList as $roleId => $userId) {
                if($roleId != $originatorRoleId && $roleId != $responsibleRoleId) {
                    $updateTaskParams['AUDITORS'][] = $userId;
                }
            }

            if($additionalParams['CONTRACT']['DAYS_TO_HARMONIZE']) {
                $endDateTime = new \DateTime('now + ' . ($additionalParams['CONTRACT']['DAYS_TO_HARMONIZE'] + 1). ' days');
                $endDateTimeFormatted = ConvertTimeStamp($endDateTime->getTimestamp(), 'SHORT');

                $updateTaskParams['END_DATE_PLAN'] = $endDateTimeFormatted;
                $updateTaskParams['DEADLINE'] = $endDateTimeFormatted;
            }

            global $USER;
            \Intervolga\Sed\Tools\Utils::updateTaskItem($taskId, $USER->GetID(), $updateTaskParams);

            try {
                $contractTask = ContractTask::getOneByFilter(array('TASK_ID' => $taskId));
                $contractTask->setCreatorRoleId($originatorRoleId);
                $contractTask->setResponsibleRoleId($responsibleRoleId);
                $contractTask->save();
            }
            catch (\Bitrix\Main\ObjectNotFoundException $e) {}
        }
    }

    /**
     * Возврашает описание для создаваемой задачи
     * @param $contractId
     * @param null|string $defaultValue
     * @return string
     */
    protected static function getNewTaskDescription($contractId, $defaultValue = null)
    {
        $description = '';

        if(is_string($defaultValue) && mb_strlen($defaultValue)) {
            $description = $defaultValue . '<br>';
        }

        $description .= '<a href="/sed/contract/' . $contractId . '/">' . Loc::getMessage('C.ACTION.LINK_NEGOTIATION_PROC') . '</a>';
        return $description;
    }

	/**
     * Метод возвращает ID инстанса группы задач, который необходимо присвоить всем задачам создаваемой в данный
     * момент группы.
     *
     * @param $contractId
     * @param $taskGroupId
     * @return int
     * @throws \Exception
     */
    protected static function getNextGroupInstanceId($contractId, $taskGroupId)
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

            $lastGroupInstanceId = intval($lastGroupTask->getGroupInstanceId());
            return ++$lastGroupInstanceId;
        } catch (ObjectNotFoundException $exception) {
            return 1;
        }
    }
}