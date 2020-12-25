<?php namespace Intervolga\Sed\Tools;

use Bitrix\Main\SystemException;
use Intervolga\Sed\Entities\Participant;
use Intervolga\Sed\Entities\ContractTask;
use Intervolga\Sed\Entities\Contract;

use Intervolga\Sed\Entities\TaskStatusElement;
use Intervolga\Sed\Entities\TaskStatusField;
use Intervolga\Sed\Entities\TaskTypeElement;
use Intervolga\Sed\Entities\TaskTypeField;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

class Utils
{
    // время (в секундах), в течение которого оставленный в задаче комментарий считается актуальным
    const TASK_COMMENTS_ACTIVITY_DURATION = 180;
    const NOTIFICATION_NEW_LINE_SYMBOL = '#BR#';

    const DISK_STORAGE_ID = 'bp-sed';
    const DISK_FOLDER_CODE = 'SED_GROUP_FOLDER';

//    const TOPIC_PREFIX = 'TASKS_';

    // Префикс изменен с 'TASK_SED_' на 'TASKSED_', так как битрикс не поддерживает знак
    // подчеркивания в префиксе. При обновлении требуется конвертация данных, как указано
    // в задаче KP_AVEKSIMA_DOGOVORA-70 (это хотфикс, на бою уже сделано).
    const TOPIC_PREFIX = 'TASKSED_';
    const FORUM_ENTITY_TYPE = 'HC';
    const CONTRACT_FILE_MAX_SIZE_MB = 5;
    const TASK_FORUM_MSG_PARAM = 'TK';


    protected static $storageRights;

    /**
     * @param $taskId
     * @param $userId
     * @param $updateInfo
     * @throws \Bitrix\Main\ArgumentNullException
     * @return bool
     */
    public static function updateTaskItem($taskId, $userId, $updateInfo)
    {
        if(empty($updateInfo)) {
            throw new \Bitrix\Main\ArgumentNullException('updateInfo');
        }

        $taskObj = new \CTasks();
        $taskObj->update($taskId, $updateInfo, array('USER_ID' => $userId));

        return true;
    }

    public static function updateTaskItemStatus($taskId, $userId, $statusFieldName, $ufStatusId, $nativeStatusId)
    {
        return static::updateTaskItem($taskId, $userId, array(
            'STATUS_CHANGED_BY' => $userId,
            'STATUS_CHANGED_DATE' => \Bitrix\Tasks\UI::formatDateTime(\Bitrix\Tasks\Util\User::getTime()),
            $statusFieldName => $ufStatusId,
            'STATUS' => $nativeStatusId,
            'REAL_STATUS' => $nativeStatusId
        ));
    }

    public static function checkComments($taskId, $userId)
    {
        if (!\Bitrix\Main\Loader::includeModule('forum')) {
            throw new \Bitrix\Main\SystemException(Loc::getMessage('C.UTILS.FORUM_NOT_INSTALLED'));
        }
        $forumId = Option::get('intervolga.sed','INTERVOLGA_SED_FORUM_ID');
        if (
            intval($forumId) <= 0 ||
            !\CForumNew::getByIDEx($forumId, SITE_ID)
        ) {
            throw new SystemException(Loc::getMessage(
                'C.UTILS.WRONG_FORUM_TABLE_ID',
                array(
                    '#URL#' => '/bitrix/admin/settings.php?mid=intervolga.sed&mid_menu=1'
                )
            ));
        }
        $feed = new \Bitrix\Forum\Comments\Feed(
            $forumId,
            array(
                "type" => 'TK', // task comment
                "id" => $taskId,
                "xml_id" => 'TASK_'.$taskId,
            )
        );

        $forum = $feed->getForum();
        $topic = $feed->getTopic();
        $actualCommentsDateTime = new \DateTime('now - ' . self::TASK_COMMENTS_ACTIVITY_DURATION . ' seconds');

        $count = \CForumMessage::GetList(array(), array(
            "FORUM_ID" => $forum['ID'],
            "TOPIC_ID" => $topic['ID'],
            "AUTHOR_ID" => $userId,
            "!PARAM1" => static::TASK_FORUM_MSG_PARAM, // сама задача - это первый коментарий,
            '>=POST_DATE' => ConvertTimeStamp($actualCommentsDateTime->getTimestamp(), 'FULL')
        ), true);

        return (!empty($count));
    }

    public static function changeLog($logData, $taskTypeId)
    {
        if(empty($logData)) {
            return null;
        }

        try {
            $taskType = TaskTypeElement::getById($taskTypeId);

            $allStatusFields = TaskStatusField::getListAll();
            $allStatusFieldNames = array();
            foreach($allStatusFields as $field) {
                $allStatusFieldNames[] = $field->getFieldName();
            }

            $availableStatuses = TaskStatusElement::getListAll($taskType);
            $availableStatuses = TaskStatusElement::makeIdsAsArrayKeys($availableStatuses);

            foreach ($logData as $key => $logItem) {
                if($logItem['FIELD'] == 'STATUS') {
                    // Удаляем все стандартные поля "Статус", т.к. значение UF_TASK_TTYPE != null
                    unset($logData[$key]);
                }
                elseif(in_array($logItem['FIELD'], $allStatusFieldNames)) {
                    $logItem['FIELD'] = 'STATUS';
                    $logItem['FROM_VALUE'] = (empty($availableStatuses[$logItem['FROM_VALUE']])) ? null : $availableStatuses[$logItem['FROM_VALUE']]->getName();
                    $logItem['TO_VALUE'] = (empty($availableStatuses[$logItem['TO_VALUE']])) ? null : $availableStatuses[$logItem['TO_VALUE']]->getName();
                    $logData[$key] = $logItem;
                }
            }

            return $logData;
        }
        catch (\Bitrix\Main\SystemException $e) {
            return null;
        }
    }

    public static function replaceNotificationMsg($msg, $textForReplace)
    {
        $pos = mb_strpos($msg, self::NOTIFICATION_NEW_LINE_SYMBOL);
        if($pos === false) {
            return $msg;
        }
        else {
            return mb_substr($msg, 0, $pos) . ' ' . self::NOTIFICATION_NEW_LINE_SYMBOL . ' ' . $textForReplace;
        }
    }

    /**
     * @param \Intervolga\Sed\Entities\ParticipantRole[] $roleList
     * @param bool $getCurrentUserData
     * @return array
     */
    public static function getRealUserNamesByRoleList($roleList, $getCurrentUserData = false)
    {
        $defaultUserIds = array();
        foreach ($roleList as $role) {
            if ($role->getDefaultUserId() > 0) {
                $defaultUserIds[] = $role->getDefaultUserId();
            }
        }

        if($getCurrentUserData) {
            global $USER;
            $defaultUserIds[] = $USER->GetID();
        }

        if(empty($defaultUserIds)) {
            return array();
        }

//        return static::getUserData(array('ID' => implode(' | ', $defaultUserIds)), array('FIELDS' => array('ID', 'NAME', 'LAST_NAME')));
        return static::getUserData(array('ID' => $defaultUserIds), array('ID', 'NAME', 'LAST_NAME'));
    }

    public static function getFileExtByName($fileName)
    {
        $matches = null;
        preg_match_all('/^.*\.([^\.]+)$/s', $fileName, $matches, PREG_SET_ORDER);

        return (empty($matches[0][1])) ? '' : $matches[0][1];
    }

    /**
     * @param array $fileInfo
     * @param int $userId
     * @param int $contractId
     * @param string $contractName
     * @return \Bitrix\Disk\File
     */
    public static function saveFileToStorage($fileInfo, $userId, $contractId, $contractName)
    {
        $sedFolder = static::getFolder();
        if(empty($sedFolder)) {
            throw new SystemException(Loc::getMessage('C.UTILS.EX_NO_FOLDER_BX_DISK_ERR'));
        }

        /** @var \Bitrix\Disk\File $file */
        $file = $sedFolder->uploadFile(
            $fileInfo,
            array(
                'NAME' => $contractName . ' [' . $contractId . '].' . $fileInfo["extension"],
                'CREATED_BY' => $userId
            )
        );

        if ($sedFolder->getErrors() || !($file instanceof \Bitrix\Disk\File)) {
            throw new SystemException(Loc::getMessage('C.UTILS.EX_DOC_SAVING_BX_DISK_ERR'));
        }

        return $file;
    }

    /**
     * @param array $filter
     * @param array $select
     * @param null|\Bitrix\Main\Entity\ExpressionField|\Bitrix\Main\Entity\ExpressionField[] $runtime
     * @return array
     */
    public static function getUserData($filter = array(), $select = array(), $runtime = null)
    {
        if(!is_array($select)) {
            $select = array();
        }
        elseif(count($select)) {
            $select = array_merge(array('ID'), $select);
        }

        $query = array(
            'select' => $select,
            'filter' => is_array($filter) ? $filter : array()
        );

        if(is_array($runtime)) {
            foreach ($runtime as $runtimeItem) {
                if($runtimeItem instanceof \Bitrix\Main\Entity\ExpressionField) {
                    $query['runtime'][] = $runtimeItem;
                }
            }
        }
        elseif($runtime instanceof \Bitrix\Main\Entity\ExpressionField) {
            $query['runtime'][] = $runtime;
        }

        $db = \Bitrix\Main\UserTable::getList($query);
        $userInfo = array();
        while ($user = $db->fetch()) {
            $userInfo[$user['ID']] = $user;
        }

        return $userInfo;
    }

    public static function getFolder($createIfEmpty = true)
    {
        $folder = null;
        $storage = static::getStorage();

        if(!empty($storage)) {
            $folder = $storage->getSpecificFolderByCode(static::DISK_FOLDER_CODE);

            if(empty($folder) && $createIfEmpty) {
                $folder = $storage->addFolder(
                    array(
                        'NAME' => Loc::getMessage('C.UTILS.LABEL_DISK_FOLDER_NAME'),
                        'CODE' => static::DISK_FOLDER_CODE
                    ),
                    static::getStorageRights(),
                    true
                );
            }
        }

        return $folder;
    }

    /**
     * @param $taskId
     * @return array
     */
    public static function getTaskAttachedFileIds($taskId)
    {
        $result = array();

        if(!\Bitrix\Main\Loader::includeModule('disk')) {
            return $result;
        }

        $dbRes = \Bitrix\Disk\AttachedObject::getList(array(
            'filter' => array('ENTITY_ID' => $taskId, 'MODULE_ID' => 'tasks', 'VERSION_ID' => false),
            'select' => array('OBJECT_ID')
        ));

        while ($attachedFile = $dbRes->fetch()) {
            $result[] = $attachedFile['OBJECT_ID'];
        }

        return array_unique($result);
    }

    protected static function getStorage($createIfEmpty = true)
    {
        $driver = \Bitrix\Disk\Driver::getInstance();
        $storage = $driver->getStorageByCommonId(static::DISK_STORAGE_ID);

        if (empty($storage) && $createIfEmpty) {
            $storageData = array(
                'NAME' => Loc::getMessage('C.UTILS.LABEL_DISK_STORAGE_NAME'),
                'ENTITY_ID' => static::DISK_STORAGE_ID,
                'SITE_ID' => 's1',
            );
            $storage = $driver->addCommonStorage($storageData, static::getStorageRights());
        }

        return $storage;
    }

    protected static function getStorageRights()
    {
        if(empty(static::$storageRights)) {
            $rightsManager = \Bitrix\Disk\Driver::getInstance()->getRightsManager();
            $fullAccessTaskId = $rightsManager->getTaskIdByName($rightsManager::TASK_FULL);
            static::$storageRights = array(
                array(
                    'ACCESS_CODE' => 'AU',
                    'TASK_ID' => $fullAccessTaskId,
                )
            );
        }

        return static::$storageRights;
    }

    /**
     * @param \Bitrix\Disk\File $fileInstance
     * @param array $requestFileData
     * @return \Bitrix\Disk\Version|null
     */
    public static function uploadFileVersion($fileInstance, $requestFileData, $createdBy)
    {
        $arFile = \CFile::MakeFileArray($requestFileData['tmp_name']);
        $arFile['ORIGINAL_NAME'] = $requestFileData["name"];

        if(!isset($arFile['MODULE_ID'])) {
            $arFile['MODULE_ID'] = \Bitrix\Disk\Driver::INTERNAL_MODULE_ID;
        }

        if(empty($arFile['type'])) {
            $arFile['type'] = '';
        }

        $arFile['type'] = \Bitrix\Disk\TypeFile::normalizeMimeType($arFile['type'], $fileInstance->getOriginalName());

        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $fileId = \CFile::saveFile($arFile, \Bitrix\Disk\Driver::INTERNAL_MODULE_ID, true, true);
        if(!$fileId) {
            return null;
        }

        $updateTime = isset($arFile['UPDATE_TIME'])? $arFile['UPDATE_TIME'] : null;
        /** @var array $arFile */
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $arFile = \CFile::getFileArray($fileId);
        if(!$arFile) {
            \CFile::delete($fileId);
            return null;
        }

        if($updateTime) {
            $arFile['UPDATE_TIME'] = $updateTime;
        }

        $version = $fileInstance->addVersion($arFile, $createdBy, true);

        if(!$version) {
            \CFile::delete($fileId);
        }

        return $version;
    }

    /**
     * @param int $forumId
     * @param string $topicXmlId
     * @return bool|string
     */
    public static function addForumTopic($forumId, $topicXmlId)
    {
        global $USER;

        $topicId = \CForumTopic::Add(array(
            "TITLE" => $topicXmlId,
            "FORUM_ID" => $forumId,
            "USER_START_ID" => $USER->getID(),
            "USER_START_NAME" => $USER->getFullName(),
            "LAST_POSTER_NAME" => $USER->getFullName(),
            "APPROVED" => "Y",
            'XML_ID' => $topicXmlId
        ));

        return $topicId;
    }

    /**
     * @param int $entityId
     * @param string $message
     */
    public static function addForumFileMessage($entityId, $message)
    {
        global $USER;

        if (!\Bitrix\Main\Loader::includeModule('forum')) {
            throw new \Bitrix\Main\SystemException(Loc::getMessage('C.UTILS.FORUM_NOT_INSTALLED'));
        }

        $forumId = Option::get('intervolga.sed','INTERVOLGA_SED_FORUM_ID');
        if (
            intval($forumId) <= 0 ||
            !\CForumNew::getByIDEx($forumId, SITE_ID)
        ) {
            throw new SystemException(Loc::getMessage(
                'C.UTILS.WRONG_FORUM_TABLE_ID',
                array(
                    '#URL#' => '/bitrix/admin/settings.php?mid=intervolga.sed&mid_menu=1'
                )
            ));
        }

        $feed = new \Bitrix\Forum\Comments\Feed(
            $forumId,
            array(
                "type" => static::FORUM_ENTITY_TYPE,
                "id" => $entityId,
                "xml_id" => static::TOPIC_PREFIX . $entityId,
            )
        );

        $forum = $feed->getForum();
        $topic = $feed->getTopic();

        if(!$topic['ID']) {
            $topic['ID'] = static::addForumTopic($forum['ID'], static::TOPIC_PREFIX . $entityId);
        }

        $params = array(
            "POST_MESSAGE" => $message,
            "AUTHOR_ID" => $USER->getID(),
            "FORUM_ID" => $forum['ID'],
            "TOPIC_ID" => $topic['ID'],
            "AUTHOR_NAME" => $USER->getFullName(),
        );

        \CForumMessage::Add($params);
    }

    public static function createContractRelatedTask($taskParams, $executiveUserId, $creatorRoleId, $responsibleRoleId, $contractId, $taskTypeId, $groupId = null, $isMasterTask = false, $groupInstanceId = null)
    {
        $result = array();
        $newTask = \CTaskItem::add($taskParams, $executiveUserId);

        $newTaskId = $newTask->getId();

        if($newTaskId) {
            $result['TASK_ID'] = $newTaskId;
            $contractTaskId = ContractTask::createEmpty()
                ->setTaskId($newTaskId)
                ->setContractId($contractId)
                ->setMasterTask($isMasterTask)
                ->setCreatorRoleId($creatorRoleId)
                ->setResponsibleRoleId($responsibleRoleId)
                ->setTaskTypeId($taskTypeId)
                ->setGroupId($groupId)
                ->setGroupInstanceId($groupInstanceId)
                ->save();

            $result['CONTRACT_TASK_ID'] = $contractTaskId;
        }

        return $result;
    }

    public static function getTaskData($filter, $select = array())
    {
        if(!empty($select)) {
            $select = array_merge(array('ID'), $select);
        }

        $filter['CHECK_PERMISSIONS'] = 'N';

        $taskListData = array();
        $taskInstance = new \CTasks();
        $taskDbRes = $taskInstance->GetList(array(), $filter, $select);
        while($task = $taskDbRes->fetch()) {
            $taskListData[$task['ID']] = $task;
        }

        return $taskListData;
    }

    /**
     * Проверка на незавершенные задачи типа taskTypeId
     * @param $taskTypeId
     */
    public static function checkIncompleteTasks($taskTypeId)
    {
        if(!\Bitrix\Main\Loader::includeModule('tasks')) {
            throw new SystemException('C.UTILS.CHECK_INCOPMLETE_TASKS.INCLUDE_MODULE');
        }

        $incompleteTaskList = static::getTaskData(
            array(
                '!REAL_STATUS' => \CTasks::STATE_COMPLETED,
                \Intervolga\Sed\Entities\TaskTypeField::TASK_TYPE_FIELD_NAME => $taskTypeId
            ),
            array('ID')
        );

        if(!empty($incompleteTaskList)) {
            $idList = array();
            foreach ($incompleteTaskList as $task) {
                $idList[] = $task['ID'];
            }
            throw new SystemException('C.UTILS.CHECK_INCOPMLETE_TASKS.CHECK_INCOPMLETE_TASKS_FOUND', array('#ID_LIST#' => implode(', ', $idList)));
        }
    }

    /**
     * Если договор переведён в статус "согласовано" (обязательный статус каждого процесса), то
     * 1. Перевести главную задачу в статус "Согласовано" (при необходимости)
     * 2. Перевести все незавершенные задачи в статус "Не требует согласования"
     *
     * @param $contractId
     * @param $newProcessStatusId
     */
    public static function handleFinalProcessStatus($contractId, $newProcessStatusId)
    {
        $newProcessStatus = \Intervolga\Sed\Entities\ProcessStatus::getById($newProcessStatusId);
        if(
            $newProcessStatus->getCode() == \Intervolga\Sed\Entities\ProcessStatus::STATUS_CODE_APPROVED ||
            $newProcessStatus->getCode() == \Intervolga\Sed\Entities\ProcessStatus::STATUS_CODE_NOT_APPROVED
        ) {
            $contractTasks = \Intervolga\Sed\Entities\ContractTask::getListByFilter(array('CONTRACT_ID' => $contractId));
            if(count($contractTasks)) {

                $mainTaskId = null;
                $participantTaskIds = array();
                foreach ($contractTasks as $task) {
                    if($task->isMasterTask()) {
                        $mainTaskId = $task->getTaskId();
                    }
                    $participantTaskIds[] = $task->getTaskId();
                }

                if(count($participantTaskIds)) {
                    global $USER;
                    $taskData = \Intervolga\Sed\Tools\Utils::getTaskData(array('ID' => $participantTaskIds), array('ID', 'CREATED_BY', 'UF_*'));

                    $taskStatuses = array();
                    foreach ($taskData as $arTask) {

                        $taskTypeId = $arTask[TaskTypeField::TASK_TYPE_FIELD_NAME];
                        $taskStatusFieldName = TaskStatusField::getFieldNamePrefix() . $taskTypeId;

                        if(!is_array($taskStatuses[$arTask['ID']])) {
                            $taskStatuses[$arTask['ID']] = array();
                            $tmpStatuses = TaskStatusElement::getListAll($taskTypeId);

                            if($arTask['ID'] == $mainTaskId) {
                                foreach ($tmpStatuses as $tmpStatus) {
                                    if($tmpStatus->getCode() == \Intervolga\Sed\Entities\Process::INITIATOR_TSTATUS_CODE_APPROVED) {
                                        $taskStatuses[$arTask['ID']]['APPROVED'] = array('ID' => $tmpStatus->getId(), 'NATIVE_STATUS_ID' => $tmpStatus->getNativeTaskStatus());
                                    }
                                    elseif($tmpStatus->getCode() == \Intervolga\Sed\Entities\Process::INITIATOR_TSTATUS_CODE_NOT_APPROVED) {
                                        $taskStatuses[$arTask['ID']]['NOT_APPROVED'] = array('ID' => $tmpStatus->getId(), 'NATIVE_STATUS_ID' => $tmpStatus->getNativeTaskStatus());
                                    }
                                }
                            }
                            else {
                                foreach ($tmpStatuses as $tmpStatus) {
                                    if($tmpStatus->getCode() == \Intervolga\Sed\Entities\Process::PARTICIPANT_TSTATUS_CODE_NOT_RELEVANT) {
                                        $taskStatuses[$arTask['ID']]['NOT_RELEVANT'] = array('ID' => $tmpStatus->getId(), 'NATIVE_STATUS_ID' => $tmpStatus->getNativeTaskStatus());
                                    }
                                    elseif($tmpStatus->getCode() == \Intervolga\Sed\Entities\Process::PARTICIPANT_TSTATUS_CODE_NOT_APPROVED) {
                                        $taskStatuses[$arTask['ID']]['NOT_APPROVED'] = array('ID' => $tmpStatus->getId(), 'NATIVE_STATUS_ID' => $tmpStatus->getNativeTaskStatus());
                                    }
                                    elseif($tmpStatus->getCode() == \Intervolga\Sed\Entities\Process::PARTICIPANT_TSTATUS_CODE_APPROVED) {
                                        $taskStatuses[$arTask['ID']]['APPROVED'] = array('ID' => $tmpStatus->getId(), 'NATIVE_STATUS_ID' => $tmpStatus->getNativeTaskStatus());
                                    }
                                }
                            }
                        }

                        if($arTask['ID'] == $mainTaskId) {
                            if(
                                $newProcessStatus->getCode() == \Intervolga\Sed\Entities\ProcessStatus::STATUS_CODE_APPROVED &&
                                $arTask[$taskStatusFieldName] != $taskStatuses[$arTask['ID']]['APPROVED']['ID']
                            ) {
                                \Intervolga\Sed\Tools\Utils::updateTaskItemStatus(
                                    $arTask['ID'],
                                    $USER->GetID(),
                                    $taskStatusFieldName,
                                    $taskStatuses[$arTask['ID']]['APPROVED']['ID'],
                                    $taskStatuses[$arTask['ID']]['APPROVED']['NATIVE_STATUS_ID']
                                );
                            }
                            elseif(
                                $newProcessStatus->getCode() == \Intervolga\Sed\Entities\ProcessStatus::STATUS_CODE_NOT_APPROVED &&
                                $arTask[$taskStatusFieldName] != $taskStatuses[$arTask['ID']]['NOT_APPROVED']['ID']
                            ) {
                                \Intervolga\Sed\Tools\Utils::updateTaskItemStatus(
                                    $arTask['ID'],
                                    $USER->GetID(),
                                    $taskStatusFieldName,
                                    $taskStatuses[$arTask['ID']]['NOT_APPROVED']['ID'],
                                    $taskStatuses[$arTask['ID']]['NOT_APPROVED']['NATIVE_STATUS_ID']
                                );
                            }
                        }
                        elseif(
                            $arTask[$taskStatusFieldName] != $taskStatuses[$arTask['ID']]['APPROVED']['ID'] &&
                            $arTask[$taskStatusFieldName] != $taskStatuses[$arTask['ID']]['NOT_APPROVED']['ID']
                        ) {
                            \Intervolga\Sed\Tools\Utils::updateTaskItemStatus(
                                $arTask['ID'],
                                $USER->GetID(),
                                $taskStatusFieldName,
                                $taskStatuses[$arTask['ID']]['NOT_RELEVANT']['ID'],
                                $taskStatuses[$arTask['ID']]['NOT_RELEVANT']['NATIVE_STATUS_ID']
                            );
                        }
                    }
                }
            }
        }
    }
}