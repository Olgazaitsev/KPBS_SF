<?php namespace Intervolga\Sed\Tools;

use Intervolga\Sed\Entities\TaskStatusTransition;
use Intervolga\Sed\Entities\TaskStatusElement;
use Intervolga\Sed\Entities\TaskStatusField;
use Intervolga\Sed\Entities\TaskTypeElement;
use Intervolga\Sed\Entities\TaskTypeField;
use Intervolga\Sed\Tools\Utils;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Handler
{
    protected static $skipTaskActions = false;
    protected static $skipContractActions = false;


    public static function onAfterComponentTemplatePage(\Bitrix\Main\Event $event)
    {
        $componentName = $event->getParameter('componentName');
        $templateName = $event->getParameter('templateName');


        if($componentName == 'bitrix:tasks.task' && $templateName == '.default') {
            self::hideExtraUfInTaskEdit();
        }
        elseif($componentName == 'bitrix:tasks.task' && $templateName == 'view') {
            self::hideExtraUfInTaskDetail();
            self::reCountLogElementsInTaskDetail();
        }
        elseif($componentName == 'bitrix:tasks.widget.buttons' && $templateName == 'task') {
            self::widgetButtonsHandler($event->getParameter('arParams'));
        }
        elseif($componentName == 'bitrix:tasks.task.detail.parts' && $templateName == 'flat') {
            $resultData = $event->getParameter('arResult');
            if($resultData['BLOCK'] == 'sidebar') {
                self::changeStatusNameInSidebar($resultData['TEMPLATE_DATA']['DATA']['TASK']);
            }
        }
    }

    protected static function hideExtraUfInTaskEdit()
    {
        $fieldToExcludeIds = TaskStatusField::getAllFieldsId();

        $taskTypeField = TaskTypeField::getOne();
        $fieldToExcludeIds[] = $taskTypeField->getId();

        if(!empty($fieldToExcludeIds)) {
            $asset = \Bitrix\Main\Page\Asset::getInstance();
                $asset->addString('<script>BX.ready(function () { BX.CustomTaskStatuses.hideExtraUfInTaskEdit(' . \CUtil::PhpToJSObject($fieldToExcludeIds) .'); });</script>');
        }
    }

    protected static function hideExtraUfInTaskDetail()
    {
        $customStatusesFieldName = TaskStatusField::getFieldLabel();
        $asset = \Bitrix\Main\Page\Asset::getInstance();
        $asset->addString('<script>BX.ready(function () { BX.CustomTaskStatuses.hideExtraUfInTaskDetail(' . \CUtil::PhpToJSObject($customStatusesFieldName) .'); });</script>');
    }

    protected static function reCountLogElementsInTaskDetail()
    {
        $asset = \Bitrix\Main\Page\Asset::getInstance();
        $asset->addString('<script>BX.ready(function () { BX.CustomTaskStatuses.reCountLogElementsInTaskDetail(); });</script>');
    }

    protected static function widgetButtonsHandler($taskParams)
    {
        $taskType = $taskParams['TASK'][TaskTypeField::TASK_TYPE_FIELD_NAME];

        if(!empty($taskType)) {

            $transitions = array();
            $currentStatusId = $taskParams['TASK'][TaskStatusField::getFieldNamePrefix() . $taskType];
            $statuses = TaskStatusElement::getListAll($taskType);

            for ($i = 0; $i < count($statuses); $i++) {
                for ($j = 0; $j < count($statuses); $j++) {
                    if($i != $j) {
                        $transitions[$statuses[$i]->getId()][$statuses[$j]->getId()] = array(
                            'needComment' => null,
                            'nativeStatusId' => $statuses[$j]->getNativeTaskStatus(),
                            'customStatusTitle' => $statuses[$j]->getName(),
                        );
                    }
                }
            }

            $parameters = array(
                'TASK_TYPE_ID' => $taskType,
                'TRANSITIONS' => $transitions,
                'CREATED_BY' => $taskParams['TASK']['CREATED_BY'],
                'RESPONSIBLE_ID' => $taskParams['TASK']['RESPONSIBLE_ID'],
            );
            $includeComponentEvent = new \Bitrix\Main\Event('intervolga.sed', 'OnTaskTransitionsCreated', $parameters);
            $includeComponentEvent->send();

            $results = $includeComponentEvent->getResults();
            foreach ($results as $result) {
                if ($result->getType() == \Bitrix\Main\EventResult::SUCCESS) {
                    $newParameters = $result->getParameters();

                    if (isset($newParameters['TRANSITIONS'])) {
                        $transitions = $newParameters['TRANSITIONS'];
                    }
                }
            }

            $JsData = \CUtil::PhpToJSObject(array(
                'currentStatusId' => $currentStatusId,
                'transitionsScheme' => $transitions,
                'taskId' => $taskParams['TASK_ID'],
                'taskTypeId' => $taskParams['TASK']['UF_TASK_TTYPE'],
                'userId' => $taskParams['USER_ID'],
                'sessId' => bitrix_sessid()
            ));

            $asset = \Bitrix\Main\Page\Asset::getInstance();
            $asset->addString('<script>BX.ready(function () {
                var widgetButtonsHandlerInstance = new BX.CustomTaskStatuses.widgetButtonsHandler();
                widgetButtonsHandlerInstance.init(' . $JsData . ');
            });</script>');
        }
    }

    /**
     * @param $taskTypeId
     * @return array
     */
    protected static function getDefaultTaskStatusInfo($taskTypeId)
    {
        $result = array();
        $defaultStatus = TaskStatusElement::getOneByFilter(array(), $taskTypeId, array('ID' => 'ASC'));

        $result['STATUS'] = $defaultStatus->getNativeTaskStatus();
        $result['REAL_STATUS'] = $defaultStatus->getNativeTaskStatus();
        $result[TaskStatusField::getFieldNamePrefix() . $taskTypeId] = $defaultStatus->getId();

        return $result;
    }

    /**
     * При создании задач специального типа подставляем значение UF-статуса по умолчанию
     *
     * @param $arFields
     */
    public static function onBeforeTaskAdd(&$arFields)
    {
        $taskTypeId = $arFields[TaskTypeField::TASK_TYPE_FIELD_NAME];
        if(!empty($taskTypeId)) {
            try {
                $fieldsToUpdate = static::getDefaultTaskStatusInfo($taskTypeId);
                foreach ($fieldsToUpdate as $key => $field) {
                    $arFields[$key] = $field;
                }
            }
            catch (\Bitrix\Main\SystemException $e) {}
        }
    }

    public static function onBeforeTaskUpdate($ID, &$arFields, &$arTaskCopy)
    {
        global $APPLICATION;

        if(array_key_exists(TaskTypeField::TASK_TYPE_FIELD_NAME, $arFields) && ($arFields[TaskTypeField::TASK_TYPE_FIELD_NAME] != $arTaskCopy[TaskTypeField::TASK_TYPE_FIELD_NAME])) {
            // Изменился тип задач
            if($arTaskCopy[TaskTypeField::TASK_TYPE_FIELD_NAME]) {
                // Тип задач был установлен ранее
                $APPLICATION->ThrowException(Loc::getMessage('C.HANDLER.EX_NO_CHANGE_TASK_TYPE'));
                return false;
            }
            else {
                // Тип задач не был назначен ранее
                $taskTypeId = $arFields[TaskTypeField::TASK_TYPE_FIELD_NAME];
                try {
                    $fieldsToUpdate = static::getDefaultTaskStatusInfo($taskTypeId);
                    foreach ($fieldsToUpdate as $key => $field) {
                        $arFields[$key] = $field;
                    }
                }
                catch (\Bitrix\Main\SystemException $e) {}
            }
        }

        // Пользовательский тип не менялся, но изменился реальный статус задачи
        if(array_key_exists('STATUS', $arFields) && $arTaskCopy[TaskTypeField::TASK_TYPE_FIELD_NAME] && ($arFields['STATUS'] != $arTaskCopy['STATUS'])) {
            $taskStatusFieldName = TaskStatusField::getFieldNamePrefix() . $arTaskCopy[TaskTypeField::TASK_TYPE_FIELD_NAME];
            if(!isset($arFields[$taskStatusFieldName]) || ($arFields[$taskStatusFieldName] == $arTaskCopy[$taskStatusFieldName])) {
                // запрещаем изменять реальный статус задачи без изменения пользовательского
                $APPLICATION->ThrowException(Loc::getMessage('C.HANDLER.EX_NO_CH_TASK_USER_STATUS'));
                return false;
            }

        }

        return true;
    }


    public static function onBeforeTaskNotificationSend($params)
    {
        if(!empty($params['arChanges'])) {
            $allStatusFieldNames = TaskStatusField::getAllTaskStatusFieldNames();
            $textForReplace = null;
            foreach ($params['arChanges'] as $field => $changedValues) {
                if(in_array($field, $allStatusFieldNames) && $changedValues['FROM_VALUE'] && $changedValues['TO_VALUE']) {
                    $statuses = TaskStatusElement::getListByFilter(array(
                        'ID' => array(
                            $changedValues['FROM_VALUE'],
                            $changedValues['TO_VALUE']
                        )
                    ), TaskStatusField::getTaskTypeIdFromFieldNameStatic($field));
                    $statuses = TaskStatusElement::makeIdsAsArrayKeys($statuses);
                    $textForReplace = '[COLOR=#000]' . $statuses[$changedValues['FROM_VALUE']]->getName() . ' -> ' . $statuses[$changedValues['TO_VALUE']]->getName() . '[/COLOR]';
                    break;
                }
            }
            if($textForReplace) {
                $params['message'] = Utils::replaceNotificationMsg($params['message'], $textForReplace);
                $params['message_email'] = Utils::replaceNotificationMsg($params['message_email'], $textForReplace);
            }
        }
    }

    protected static function changeStatusNameInSidebar($taskData)
    {
        $taskTypeId = $taskData[TaskTypeField::TASK_TYPE_FIELD_NAME];
        if(empty($taskTypeId)) {
            return;
        }

        try {
            $taskType = TaskTypeElement::getById($taskTypeId);
            $statusFieldName = TaskStatusField::getOneByEntityFilter($taskType)->getFieldName();
            $currentStatusId = $taskData[$statusFieldName];
            $currentStatusName = null;

            if (empty($currentStatusId)) {
                $currentStatusName = TaskStatusElement::getOneByFilter(array(), $taskType)->getName();
            } else {
                $currentStatusName = TaskStatusElement::getById($currentStatusId, $taskType)->getName();
            }

            $asset = \Bitrix\Main\Page\Asset::getInstance();
            $asset->addString('<script>BX.ready(function () { BX.CustomTaskStatuses.changeStatusNameInSidebar(' . \CUtil::PhpToJSObject($currentStatusName) . '); });</script>');
        }
        catch (\Exception $e) {}
    }

    public static function onAfterTaskStatusAdd(\Bitrix\Main\Event $event)
    {
        $status = $event->getParameter('status');
        if($status instanceof TaskStatusElement) {
            try {
                $statusField = TaskStatusField::getById($status->getUserFieldId(), null, false);
                $taskTypeId = $statusField->getTaskTypeIdFromFieldName();

                try {
                    // если будет выброшено исключение ObjectNotFoundException, значит тип задач модулем не используется
                    \Intervolga\Sed\Entities\ProcessTaskType::getOneByFilter(array('TASK_TYPE_ID' => $taskTypeId));
                    TaskStatusTransition::addStatusTransitions($status, $statusField);
                }
                catch (\Bitrix\Main\ObjectNotFoundException $e) {}
            }
            catch (\Exception $e) {}
        }
    }

    public static function onAfterTaskStatusRemove(\Bitrix\Main\Event $event)
    {
        $statusId = $event->getParameter('statusId');
        try {
            TaskStatusTransition::removeByStatusId($statusId);
        }
        catch (\Exception $e) {}
    }

    /**
     * @param \Bitrix\Main\Event $event
     * @throws \Bitrix\Main\InvalidOperationException
     * @return \Bitrix\Main\EventResult
     */
    public static function onBeforeTaskTypeRemove(\Bitrix\Main\Event $event)
    {
        $taskTypeIdToRemove = $event->getParameter('taskTypeId');

        // запрещаем удалять ТЗ для инициатора без генерации исключения
        $initiatorTType = TaskTypeElement::getByXmlId(\Intervolga\Sed\Entities\Process::INITIATOR_TTYPE_CODE);
        if($initiatorTType->getId() == $taskTypeIdToRemove) {
            return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::ERROR);
        }

        // если установлен следующий флаг, то необходимые проверки уже были сделаны
        if(!\Intervolga\Sed\Tables\ProcessTaskTypeTable::hasElementJustBeenSuccessfullyRemoved($taskTypeIdToRemove)) {
            try {
                // если элемент с таким ID не будет найден, то будет выброшено исключение ObjectNotFoundException
                $processTaskType = \Intervolga\Sed\Entities\ProcessTaskType::getOneByFilter(array('TASK_TYPE_ID' => $taskTypeIdToRemove));
                throw new \Bitrix\Main\InvalidOperationException(Loc::getMessage('SED.TOOLS.ON_BEFORE_TTYPE_REMOVE.TTYPE_USED_BY_PROCESS'));
            }
            catch (\Bitrix\Main\ObjectNotFoundException $e) {
               Utils::checkIncompleteTasks($taskTypeIdToRemove);
            }
        }

        return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS);
    }

    public static function onBeforeTaskStatusRemove(\Bitrix\Main\Event $event)
    {
        $parameters = $event->getParameters();
        if($parameters['statusId'] && $parameters['userFieldId']) {

            $taskStatusField = TaskStatusField::getById($parameters['userFieldId'], null, false);
            $taskTypeId = $taskStatusField->getTaskTypeIdFromFieldName();

            // запрещаем удаление, если есть задачи в этом статусе
            $tasksInStatus = Utils::getTaskData(
                array(
                    \Intervolga\Sed\Entities\TaskTypeField::TASK_TYPE_FIELD_NAME => $taskTypeId,
                    \Intervolga\Sed\Entities\TaskStatusField::getFieldNamePrefix() . $taskTypeId => $parameters['statusId']
                ),
                array('ID')
            );
            if(!empty($tasksInStatus)) {
                $idList = array();
                foreach ($tasksInStatus as $task) {
                    $idList[] = $task['ID'];
                }
                throw new \Bitrix\Main\InvalidOperationException(Loc::getMessage('C.UTILS.ER_DEL_STATUS_HAS_UNCOMPLETED_TASKS', array('#ID_LIST#' => implode(', ', $idList))));
            }

            $taskStatus = TaskStatusElement::getById($parameters['statusId'], null, false);

            if(\Intervolga\Sed\Entities\Process::isTStatusCodeDefault($taskStatus->getCode())) {
                try {
                    // если данный ТЗ не используется в процессе согласования, то будет выброшено исключение ObjectNotFoundException
                    $processTaskType = \Intervolga\Sed\Entities\ProcessTaskType::getOneByFilter(array('TASK_TYPE_ID' => $taskTypeId));
                    throw new \Bitrix\Main\InvalidOperationException(Loc::getMessage('SED.TOOLS.ON_BEFORE_TSTATUS_REMOVE.REQUIRED_STATUS'));
                }
                catch (\Bitrix\Main\ObjectNotFoundException $e) {}
            }
            elseif(\Intervolga\Sed\Entities\TaskStatusTrigger::isStatusUsed($taskStatus->getId()) || \Intervolga\Sed\Entities\TaskGroupStatusTrigger::isStatusUsed($taskStatus->getId())) {
                // статус не является обязательным, но присутствует в каких-либо обработчиках, то удалять его запрещено
                throw new \Bitrix\Main\InvalidOperationException(Loc::getMessage('SED.TOOLS.ON_BEFORE_TSTATUS_REMOVE.NOT_REQUIRED_STATUS'));
            }
        }
    }

    public static function onBeforeTaskStatusUpdate(\Bitrix\Main\Event $event)
    {
        $status = $event->getParameter('status');
        if($status instanceof TaskStatusElement && $status->getId()) {
            $taskStatusField = TaskStatusField::getById($status->getUserFieldId(), null, false);
            $taskTypeId = $taskStatusField->getTaskTypeIdFromFieldName();

            try {
                // если данный ТЗ не используется в процессе согласования, то будет выброшено исключение ObjectNotFoundException
                $processTaskType = \Intervolga\Sed\Entities\ProcessTaskType::getOneByFilter(array('TASK_TYPE_ID' => $taskTypeId));

                $oldStatus = TaskStatusElement::getById($status->getId(), null, false);

                // запрещаем изменять символьные коды обязательных статусов
                if(\Intervolga\Sed\Entities\Process::isTStatusCodeDefault($oldStatus->getCode()) && ($oldStatus->getCode() != $status->getCode())) {
                    throw new \Bitrix\Main\InvalidOperationException(Loc::getMessage('SED.TOOLS.ON_BEFORE_TSTATUS_UPDATE.DEFAULT_STATUS_CODE'));
                }
            }
            catch (\Bitrix\Main\ObjectNotFoundException $e) {}
        }
    }

    /**
     * Запрет на изменение XML_ID "ТЗ для инициатора"
     *
     * @param \Bitrix\Main\Event $event
     */
    public static function onBeforeTaskTypeUpdate(\Bitrix\Main\Event $event)
    {
        $type = $event->getParameter('type');
        if($type instanceof TaskTypeElement && $type->getId() && $type->getCode() != \Intervolga\Sed\Entities\Process::INITIATOR_TTYPE_CODE) {
            try {
                $initiatorType = TaskTypeElement::getByXmlId(\Intervolga\Sed\Entities\Process::INITIATOR_TTYPE_CODE);
                if($type->getId() == $initiatorType->getId()) {
                    throw new \Bitrix\Main\InvalidOperationException(Loc::getMessage('SED.TOOLS.ON_BEFORE_TTYPE_UPDATE.INITIATOR_TTYPE_CODE'));
                }
            }
            catch (\Bitrix\Main\ObjectNotFoundException $e) {}
        }
    }

    public static function onTaskTransitionsCreated(\Bitrix\Main\Event $event)
    {
        $parameters = $event->getParameters();

        global $USER;

        $userRole = null;
        if($USER->GetID() == $parameters['RESPONSIBLE_ID']) {
            $userRole = TaskStatusTransition::RESPONSIBLE_CODE;
        }
        elseif($USER->GetID() == $parameters['CREATED_BY']) {
            $userRole = TaskStatusTransition::ORIGINATOR_CODE;
        }

        if($userRole) {
            $transitions = \Intervolga\Sed\Entities\TaskStatusTransition::getListByFilter(array(
                'TASK_TYPE' => $parameters['TASK_TYPE_ID'],
                'USER_ROLE' => $userRole,
                'TRANSITION_ALLOWED' => true
            ));

            $allowedTransitions = array();
            if(!empty($transitions)) {
                foreach ($transitions as $transition) {
                    $allowedTransitions[$transition->getSourceStatus()][$transition->getDestStatus()] = array(
                        'needComment' => $transition->isCommentNeeded(),
                        'customStatusTitle' => $transition->getButtonLabel(),
                        'btnSort' => $transition->getButtonSort(),
                        'btnColor' => $transition->getButtonColor(),
                        'btnTextColor' => $transition->getButtonTextColor(),
                        'btnHoverMode' => $transition->getButtonHoverMode()
                    );
                }
            }

            foreach ($parameters['TRANSITIONS'] as $srcId => $destItem) {
                foreach ($destItem as $destId => $item) {
                    if($allowedTransitions[$srcId]) {
                        if($allowedTransitions[$srcId][$destId]) {
                            $parameters['TRANSITIONS'][$srcId][$destId]['needComment'] = $allowedTransitions[$srcId][$destId]['needComment'];
                            if($allowedTransitions[$srcId][$destId]['customStatusTitle']) {
                                $parameters['TRANSITIONS'][$srcId][$destId]['customStatusTitle'] = $allowedTransitions[$srcId][$destId]['customStatusTitle'];
                            }
                            if(isset($allowedTransitions[$srcId][$destId]['btnSort'])) {
                                $parameters['TRANSITIONS'][$srcId][$destId]['btnSort'] = $allowedTransitions[$srcId][$destId]['btnSort'];
                            }
                            if($allowedTransitions[$srcId][$destId]['btnColor']) {
                                $parameters['TRANSITIONS'][$srcId][$destId]['btnColor'] = $allowedTransitions[$srcId][$destId]['btnColor'];
                            }
                            if($allowedTransitions[$srcId][$destId]['btnTextColor']) {
                                $parameters['TRANSITIONS'][$srcId][$destId]['btnTextColor'] = $allowedTransitions[$srcId][$destId]['btnTextColor'];
                            }
                            if(isset($allowedTransitions[$srcId][$destId]['btnHoverMode'])) {
                                $parameters['TRANSITIONS'][$srcId][$destId]['btnHoverMode'] = $allowedTransitions[$srcId][$destId]['btnHoverMode'];
                            }
                        }
                        else {
                            unset($parameters['TRANSITIONS'][$srcId][$destId]);
                        }
                    }
                    else {
                        unset($parameters['TRANSITIONS'][$srcId]);
                    }
                }
            }
        }
        else {
            $parameters['TRANSITIONS'] = array();
        }

        return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, $parameters);
    }

    /**
     * Запрещено удалять задачи, которые участвуют в процессе согласования.
     *
     * @param $ID
     * @param $arFields
     * @return bool
     */
    public static function onBeforeTaskDelete($ID, $arFields)
    {
        try {
            \Intervolga\Sed\Entities\ContractTask::getOneByFilter(array('TASK_ID' => $ID));
            return false;
        }
        catch (\Bitrix\Main\ObjectNotFoundException $e) {}

        return true;
    }

    public static function onContractUpdate($contractId, $fields)
    {
        $oldContract = \Intervolga\Sed\Entities\Contract::getById($contractId);
        $newProcessStatusId = (int)$fields['PROCESS_STATUS_ID'];
        $oldProcessStatusId = (int)$oldContract->getProcessStatusId();

        if($oldProcessStatusId == $newProcessStatusId) {
            return;
        }

        \Intervolga\Sed\Tools\Utils::handleFinalProcessStatus($contractId, $newProcessStatusId);

//        if(static::$skipContractActions) {
//            return;
//        }
//
//        static::$skipContractActions = true;
//
//        $contactActionListWithParams = \Intervolga\Sed\Entities\TriggerEffect::getContractActionListWithParams($oldContract->getProcessId(), $oldProcessStatusId, $newProcessStatusId);
//
//        $actionListInfo = array();
//        foreach($contactActionListWithParams as $item) {
//            $actionListInfo[$item->getTriggerId()][$item->getActionCode()][$item->getParamCode()] = $item->getParamValue();
//        }
//
//        foreach ($actionListInfo as $actionInfo) {
//            \Intervolga\Sed\Tools\Utils::performAction($actionInfo, array_merge($fields, array('ID' => $contractId)));
//        }
//
//        static::$skipContractActions = false;
    }

    /**
     * 1. Обработчик должен реагировать на изменение комбинации параметров
     *      id процесса,
     *      id исполнителя (через него получаем id роли),
     *      id постановщика (аналогично)
     *      id статуса
     *
     * @param int $ID
     * @param array $arFields - набор измененных полей
     * @param array $arTaskCopy - набор текущих полей объекта
     */
    public static function onTaskUpdate($ID, &$arFields, &$arTaskCopy)
    {
        if(static::$skipTaskActions) {
            return;
        }

        $contactTask = null;
        try {
            $contactTaskFilter = array('TASK_ID' => $ID);
            $contactTaskSelect = array('CONTRACT.NAME', 'CONTRACT.DAYS_TO_HARMONIZE', 'CONTRACT.FILE_ID');
            $contactTask = \Intervolga\Sed\Entities\ContractTask::getOneByFilter($contactTaskFilter, array(), $contactTaskSelect);
        }
        catch (\Bitrix\Main\ObjectNotFoundException $e) {
            return;
        }

        static::$skipTaskActions = true;

        $taskTypeFieldName = TaskTypeField::TASK_TYPE_FIELD_NAME;
        $taskType = TaskTypeElement::getById($arTaskCopy[$taskTypeFieldName]);
        $taskStatusFieldName = TaskStatusField::getFieldNamePrefix() . $taskType->getId();
        $oldTaskStatusId = $arTaskCopy[$taskStatusFieldName];
        $newTaskStatusId = $arFields[$taskStatusFieldName];

        if($oldTaskStatusId == $newTaskStatusId) {
            return;
        }

        $taskGroupActionListWithParams = array();
        if ($contactTask->getGroupId()) {
            $taskGroupActionListWithParams = \Intervolga\Sed\Entities\TriggerEffect::getTaskGroupActionListWithParams(
                $ID,
                $taskType->getId(),
                $contactTask->getContractId(),
                $contactTask->getGroupId(),
                (int)$arTaskCopy['CREATED_BY'],
                (int)$newTaskStatusId
            );
        }
        $taskActionListWithParams = \Intervolga\Sed\Entities\TriggerEffect::getTaskActionListWithParams(
            $contactTask->getContractId(),
            (int)$arTaskCopy['CREATED_BY'],
            (int)$arTaskCopy['RESPONSIBLE_ID'],
            (int)$contactTask->getCreatorRoleId(),
            (int)$contactTask->getResponsibleRoleId(),
            (int)$oldTaskStatusId,
            (int)$newTaskStatusId
        );

        $actionListInfo = array();
        foreach (array_merge($taskActionListWithParams, $taskGroupActionListWithParams) as $item) {
            $actionListInfo[$item->getTriggerId()][$item->getActionCode()][$item->getParamCode()] = $item->getParamValue();
        }

        $additionalParams = array(
            'CONTRACT' => array(
                'ID' => $contactTask->getContractId(),
                'NAME' => $contactTask->getReferenceContractName(),
                'DAYS_TO_HARMONIZE' => $contactTask->getReferenceContractDaysToHarmonize(),
                'FILE_ID' => $contactTask->getReferenceContractFileId()
            ),
            'TASK' => array(
                'ID' => $ID
            )
        );

        foreach ($actionListInfo as $actionInfo) {
            \Intervolga\Sed\Entities\Action::performAction($actionInfo, $additionalParams);
        }

        static::$skipTaskActions = false;

        return;
    }
}