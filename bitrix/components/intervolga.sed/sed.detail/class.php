<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Disk\Configuration;
use Bitrix\Disk\Document\Contract\FileCreatable;
use Bitrix\Disk\Driver;
use Bitrix\Disk\Uf\LocalDocumentController;
use Bitrix\Disk\Ui\FileAttributes;
use Bitrix\Disk\UrlManager;
use Bitrix\Main\UI\Viewer\Renderer\Renderer;
use Intervolga\Sed\Entities as SedEntities;
use Intervolga\Sed\Tools as SedTools;

use Intervolga\Sed\Entities as CSEntities;
use Intervolga\Sed\Tools as CSTools;
use Intervolga\Sed\Tables\ContractUserFieldsTable;

use Bitrix\Main\SystemException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;

class SedDetailComponent extends CBitrixComponent
{
    const REQUEST_PDF_PARAM_NAME = 'pdf';
    const REQUEST_PDF_PARAM_VALUE = 'Y';

    const ERR_CODE_NO_ACCESS_TO_TASK = 1048584;


    /** @var SedEntities\Contract|null $contract */
    protected $contract;
    /** @var \Bitrix\Disk\UrlManager $urlManager */
    protected $urlManager;
    /** @var CSEntities\TaskTypeElement[] $allTaskTypes */
    protected $allTaskTypes = array();
    /** @var \Bitrix\Disk\File $contractFile */
    protected $contractFile;

    protected $taskDetailUrlTemplate;
    protected $userDetailUrlTemplate;

    protected $initiatorId;
    protected $participantIds;

    protected $taskStatusCssClasses = array();
    protected $fileUploadForm = array();

    /** @var SedEntities\ContractTask $contract */
    protected $mainTask;

    protected $canPause = false;
    protected $canResume = false;
    protected $canUpdateFile = false;
    protected $contractTaskTable = array();
    protected $forums = array();

    public function executeComponent()
    {
        try {
            $this->prepareParams();
            $this->getData();
            $this->checkFileUpdateRequest();
            $this->initForums();

            if ($this->checkPdfRequest()) {
                $this->fillPdfResult();

                global $APPLICATION;
                $APPLICATION->RestartBuffer();
                ob_start();
                $this->includeComponentTemplate('pdf');
                $dompdf = new SedEntities\PDF(false);
                $dompdf->loadHtml(ob_get_clean());
                $dompdf->stream();
                die;
            } else {
                $this->fillResult();
                $this->includeComponentTemplate();
                $this->includeJS();
            }

        } catch (\Bitrix\Main\SystemException $e) {
            if ($e->getCode() == self::ERR_CODE_NO_ACCESS_TO_TASK) {
                echo SedTools\Html::getWarningHtml(Loc::getMessage('SED_DETAIL_COMPONENT.WARNING_NO_ACCESS_TO_TASKS'));
            } else {
                echo SedTools\Html::getErrorHtml($e->getMessage());
            }
        }

        return null;
    }

    protected function getContractFile()
    {
        if (!($this->contractFile instanceof \Bitrix\Disk\File) && $this->contract instanceof SedEntities\Contract) {
            $this->contractFile = \Bitrix\Disk\File::getById($this->contract->getFileId());
        }

        return $this->contractFile;
    }

    protected function prepareParams()
    {
        $this->urlManager = \Bitrix\Disk\Driver::getInstance()->getUrlManager();
        $this->allTaskTypes = CSEntities\TaskTypeElement::makeIdsAsArrayKeys(CSEntities\TaskTypeElement::getListAll());
        $this->taskDetailUrlTemplate = \Bitrix\Main\Config\Option::get('tasks', 'paths_task_user_action');
        $this->userDetailUrlTemplate = \Bitrix\Main\Config\Option::get('intranet', 'path_user');
        $this->taskStatusCssClasses = array(
            \CTasks::STATE_NEW => 'new',
            \CTasks::STATE_PENDING => 'accepted',
            \CTasks::STATE_IN_PROGRESS => 'progress',
            \CTasks::STATE_SUPPOSEDLY_COMPLETED => 'completed',
            \CTasks::STATE_COMPLETED => 'completed',
            \CTasks::STATE_DEFERRED => 'deferred',
            \CTasks::STATE_DECLINED => 'declined',
        );
        $this->fileUploadForm = array(
            'TITLE' => Loc::getMessage('SED_DETAIL_COMPONENT.UPDATE_CONTRACT_FILE'),
            'ID' => 'update-file-form',
            'COMMENT' => array(
                'NAME' => 'comment',
                'ID' => 'update-file-form__comment',
                'PLACEHOLDER' => Loc::getMessage('SED_DETAIL_COMPONENT.UPDATE_CONTRACT_FILE.COMMENT_PLACEHOLDER')
            ),
            'FILE_SELECT_BTN' => array(
                'ID' => 'update-file-form__file-select',
                'TEXT' => Loc::getMessage('SED_DETAIL_COMPONENT.UPDATE_CONTRACT_FILE.SELECT_BTN')
            ),
            'FILE_UPLOAD_BTN' => array(
                'ID' => 'update-file-form__file-upload',
                'TEXT' => Loc::getMessage('SED_DETAIL_COMPONENT.UPDATE_CONTRACT_FILE.UPLOAD_BTN')
            ),
            'HIDDEN_FILE_INPUT' => array(
                'ID' => 'update-file-form__file',
                'NAME' => 'fileInput'
            ),
            'HIDDEN_ACTION_INPUT' => array(
                'NAME' => 'action',
                'VALUE' => 'updateFile'
            )
        );

        \Bitrix\Main\Loader::includeModule('forum');
    }

    protected function includeJS()
    {
        $jsParams = array(
            'form' => $this->fileUploadForm['ID'],
            'comment' => $this->fileUploadForm['COMMENT']['ID'],
            'fileSelectBtn' => $this->fileUploadForm['FILE_SELECT_BTN']['ID'],
            'fileUploadBtn' => $this->fileUploadForm['FILE_UPLOAD_BTN']['ID'],
            'fileInput' => $this->fileUploadForm['HIDDEN_FILE_INPUT']['ID']
        );

        $file = $this->getContractFile();
        $jsParams['fileExt'] = $file->getExtension();

        \Bitrix\Main\Page\Asset::getInstance()->addString('<script>BX.ready(function () {var SedDetailFileUploadInstance = new SedDetailFileUpload(' . json_encode($jsParams) . ');});</script>');
    }

    protected function checkFileUpdateRequest()
    {
        if (check_bitrix_sessid() && $this->request->getPost($this->fileUploadForm['HIDDEN_ACTION_INPUT']['NAME']) == $this->fileUploadForm['HIDDEN_ACTION_INPUT']['VALUE']) {

            global $USER;
            global $APPLICATION;

            /** @var array $arFile */
            $arFile = $this->request->getFile($this->fileUploadForm['HIDDEN_FILE_INPUT']['NAME']);

            if ($arFile['size'] > SedTools\Utils::CONTRACT_FILE_MAX_SIZE_MB * 1024 * 1024) {
                $this->arResult['ERRORS'][] = SedTools\Html::getErrorHtml(Loc::getMessage('SED_DETAIL_COMPONENT.FILE_MAX_SIZE', array('#SIZE#' => SedTools\Utils::CONTRACT_FILE_MAX_SIZE_MB)));
                return;
            }

            $fileInstance = $this->getContractFile();

            // !!! отключаем проверку на расширение файла договора
            /*
            if (SedTools\Utils::getFileExtByName($arFile['name']) != $fileInstance->getExtension()) {
                $this->arResult['ERRORS'][] = SedTools\Html::getErrorHtml(Loc::getMessage('SED_DETAIL_COMPONENT.FILE_EXT_ERROR', array('#EXT#' => $fileInstance->getExtension())));
                return;
            }
            */

            $version = SedTools\Utils::uploadFileVersion($fileInstance, $arFile, $USER->GetID());

            if (!$version) {
                $this->arResult['ERRORS'][] = SedTools\Html::getErrorHtml(Loc::getMessage('SED_DETAIL_COMPONENT.FILE_LOAD_ERROR'));
                return;
            }

            SedTools\Utils::addForumFileMessage($this->contract->getId(), Loc::getMessage('SED_DETAIL_COMPONENT.FILE_UPDATE_COMMENT', array(
                '#FILE_NAME#' => $version->getName(),
                '#URL#' => $this->urlManager->getUrlForDownloadVersion($version),
                '#COMMENT#' => $this->request->getPost($this->fileUploadForm['COMMENT']['NAME'])
            )));

            if ($ex = $APPLICATION->GetException()) {
                $this->arResult['ERRORS'][] = SedTools\Html::getErrorHtml($ex->GetString());
            }
        }
    }

    protected function checkPdfRequest()
    {
        return (extension_loaded('dom') && ($this->request->getQuery(static::REQUEST_PDF_PARAM_NAME) == static::REQUEST_PDF_PARAM_VALUE));
    }

    protected function fillPdfResult()
    {
        $this->arResult['CONTRACT_INFO'] = array(
            'HEADER' => Loc::getMessage('SED_DETAIL_COMPONENT.APPROVAL_SHEET') . date('d.m.Y'),
            'NAME' => $this->contract->getName(),
            'STATUS' => $this->contract->getReferenceProcessStatusName(),
            'PARTICIPANTS' => $this->contractTaskTable
        );

        if ($this->initiatorId) {
            $dbUserRes = \Bitrix\Main\UserTable::getList(array(
                'select' => array('ID', 'NAME', 'SECOND_NAME', 'LAST_NAME'),
                'filter' => array('ID' => $this->initiatorId)
            ));
            if ($arUserRes = $dbUserRes->fetch()) {
                $this->arResult['CONTRACT_INFO']['INITIATOR_FULL_NAME'] = \CUser::FormatName(
                    \CSite::GetNameFormat(),
                    $arUserRes,
                    true,
                    false
                );
            }
        }

        $mainTaskInfo = CSTools\Utils::getTaskData(
            array('ID' => $this->mainTask->getTaskId()),
            array('ID', 'DATE_START')
        );
        $mainTaskInfo = array_shift($mainTaskInfo);
        if (!empty($mainTaskInfo['DATE_START'])) {
            $this->arResult['CONTRACT_INFO']['HARMONIZATION_START'] = FormatDate('SHORT', MakeTimeStamp($mainTaskInfo['DATE_START']));
        }
    }

    /**
     * @throws \Bitrix\Main\SystemException
     */
    protected function getData()
    {
        $contractId = (int)$this->arParams['VARIABLES']['CONTRACT'];
        if ($contractId < 1) {
            throw new \Bitrix\Main\SystemException(Loc::getMessage('SED_DETAIL_COMPONENT.NO_CONTRACT'));
        }

        try {
            $this->contract = SedEntities\Contract::getOneByFilter(
                array(
                    'ID' => $contractId,
                    'PARTICIPANT_ROLE.IS_INITIATOR' => true,
                    'PARTICIPANT_ROLE.PARICIPANT.CONTRACT_ID' => $contractId
                ),
                array(),
                array('PROCESS_ID', 'PROCESS_STATUS.CODE', 'PROCESS_STATUS.NAME', 'PROCESS_STATUS.CODE', 'UF_*')
            );
        } catch (\Bitrix\Main\ObjectNotFoundException $e) {
            throw new \Bitrix\Main\SystemException(Loc::getMessage('SED_DETAIL_COMPONENT.NO_CONTRACT'));
        }

        try {
            $this->mainTask = SedEntities\ContractTask::getOneByFilter(array(
                'CONTRACT_ID' => $contractId,
                'IS_MASTER' => true
            ));
        } catch (\Bitrix\Main\ObjectNotFoundException $e) {
            throw new \Bitrix\Main\SystemException(Loc::getMessage('SED_DETAIL_COMPONENT.NO_MAIN_TASK'));
        }

        $this->initContractTable();
        $this->initRights();
    }

    /**
     * @return bool
     */
    protected function checkAccessRights()
    {
        global $USER;
        return ($USER->isAdmin() || ($this->initiatorId == $USER->GetId()) || in_array($USER->GetId(), $this->participantIds));
    }

    protected function initRights()
    {
        global $USER;

        if (
            ($this->contract instanceof SedEntities\Contract) &&
            ($USER->isAdmin() || $this->initiatorId == $USER->GetId() || in_array($USER->GetId(), $this->participantIds)) &&
            (($this->contract->getReferenceProcessStatusCode() != SedEntities\ProcessStatus::STATUS_CODE_NOT_APPROVED) &&
                ($this->contract->getReferenceProcessStatusCode() != SedEntities\ProcessStatus::STATUS_CODE_APPROVED))
        ) {
            $this->canUpdateFile = true;

            if ($this->initiatorId == $USER->GetId()) {
                if ($this->contract->getReferenceProcessStatusCode() == SedEntities\ProcessStatus::STATUS_CODE_PAUSED) {
                    $this->canResume = true;
                } elseif ($this->contract->getReferenceProcessStatusCode() != SedEntities\ProcessStatus::STATUS_CODE_NEW) {
                    $this->canPause = true;
                }
            }
        }
    }

    protected function initContractTable()
    {
        $participants = SedEntities\Participant::getListByFilter(
            array(
                'CONTRACT_ID' => $this->contract->getId(),
//                'ROLE.IS_INITIATOR' => false
            ),
            array(),
            array('ROLE.ID', 'ROLE.NAME', 'ROLE.IS_INITIATOR', 'CONTRACT.TASK.TASK_ID', 'CONTRACT.TASK.RESP_ROLE_ID')
        );

        // uasort($participants, 'cmp');

        $this->participantIds = array();
        $taskIds = array();
        $taskMap = array();
        $roleInfo = array();
        foreach ($participants as $participant) {
            if ($participant->getReferenceRoleIsInitiator()) {
                $this->initiatorId = $participant->getUserId();
            }

            $roleInfo[$participant->getRoleId()] = array(
//                'ROLE_ID' => $participant['ROLE_ID'],
                'USER_ID' => $participant->getUserId(),
                'ROLE_NAME' => $participant->getReferenceRoleName()
            );
            $this->participantIds[] = $participant->getUserId();


//            if ($this->mainTask->getTaskId() != $participant->getReferenceTaskId()) {
                $taskIds[] = $participant->getReferenceTaskId();
//            }

            if ($participant->getRoleId() == $participant->getReferenceTaskRespRoleId()) {
                $taskMap[$participant->getRoleId()] = $participant->getReferenceTaskId();
            }
        }

        $taskInfo = $this->getTaskInfo($taskIds);

        $taskResponsibleIds = array();
        foreach ($taskInfo as $task) {
            $taskResponsibleIds[] = $task['RESPONSIBLE_ID'];
        }
        $taskResponsibleIds = array_unique($taskResponsibleIds);
        $contractParticipantIds = array_merge($this->participantIds, $taskResponsibleIds);
        $contractParticipantIds = array_unique($contractParticipantIds);

        $dbUserRes = \Bitrix\Main\UserTable::getList(array(
            'select' => array('ID', 'NAME', 'SECOND_NAME', 'LAST_NAME'),
            'filter' => array('ID' => $contractParticipantIds)
        ));
        $usersInfo = array();
        while ($arUserRes = $dbUserRes->fetch()) {
            $arUserRes['FULL_NAME'] = \CUser::FormatName(
                \CSite::GetNameFormat(),
                $arUserRes,
                true,
                false
            );
            $usersInfo[$arUserRes['ID']] = $arUserRes;
        }

        $this->contractTaskTable = array();
        //asort($roleInfo);
        // uasort($roleInfo, 'roleInfoCmp'); Пока отключаем сортировку

        foreach ($roleInfo as $roleId => $role) {
            if (!$taskMap[$roleId] || empty($taskInfo[$taskMap[$roleId]])) {
                // на пользователя ещё не назначена задача
                $this->contractTaskTable[] = array(
                    'USER' => array(
                        'ID' => $role['USER_ID'],
                        'ROLE_NAME' => $role['ROLE_NAME'],
                        'DETAIL_URL' => \CComponentEngine::makePathFromTemplate($this->userDetailUrlTemplate, array("USER_ID" => $role['USER_ID'])),
                        'FULL_NAME' => $usersInfo[$role['USER_ID']]['FULL_NAME'],
                    ),
                    'TASK' => array(
                        'STATUS_NAME' => Loc::getMessage('SED_DETAIL_COMPONENT.TABLE_ITEM_TEXT_WAITING'),
                        'STATUS_CLASS' => 'default'
                    ),
                );
            } else {
                $userId = $taskInfo[$taskMap[$roleId]]['RESPONSIBLE_ID'];

                $contractTaskTableRow = array(
                    'USER' => array(
                        'ID' => $userId,
                        'ROLE_NAME' => $role['ROLE_NAME'],
                        'DETAIL_URL' => \CComponentEngine::makePathFromTemplate($this->userDetailUrlTemplate, array("USER_ID" => $userId)),
                        'FULL_NAME' => $usersInfo[$userId]['FULL_NAME'],
                    ),
                    'TASK' => array(
                        'ID' => $taskMap[$roleId],
                        'STATUS_NAME' => $taskInfo[$taskMap[$roleId]]['STATUS_NAME'],
                        'STATUS_CLASS' => $this->taskStatusCssClasses[$taskInfo[$taskMap[$roleId]]['REAL_STATUS']],
                        'DETAIL_URL' => $taskInfo[$taskMap[$roleId]]['DETAIL_URL'],
                        'DETAIL_URL_LABEL' => Loc::getMessage('SED_DETAIL_COMPONENT.CONTRACT_TABLE_TASK_DETAIL'),
                    ),
                );
                if (!empty($taskInfo[$taskMap[$roleId]]['STATUS_CHANGED_DATE'])) {
                    $contractTaskTableRow['TASK']['STATUS_CHANGED_DATE'] = FormatDate('d.m.Y, H:i', MakeTimeStamp($taskInfo[$taskMap[$roleId]]["STATUS_CHANGED_DATE"]));
                }

                $this->contractTaskTable[] = $contractTaskTableRow;
            }
        }
    }

    protected function getFileUrlForStartEdit()
    {
        $url = '';

        $file = $this->getContractFile();
        $viewerCode = 'office365'; // TODO: Implement non-constant viewer code.
        if ($file && ($this->urlManager instanceof UrlManager)) {
            $url = $this->urlManager->getUrlForStartEditFile($file->getId(), $viewerCode);
        }

        return $url;
    }

    protected function getFileViewerAttributes()
    {
        $file = $this->getContractFile();
        $fileViewerAttrs = FileAttributes::buildByFileId(
            $file->getFileId(),
            $this->urlManager->getUrlForDownloadFile($file)
        )
            ->setObjectId($file->getId())
            ->addAction(array(
                'type' => 'download'
            ));

        $viewerType = $fileViewerAttrs->getAttribute('data-viewer-type');
        if ($viewerType == Renderer::getJsType()) {
            $fileViewerAttrs->setAttribute('data-viewer-type', 'document');
        }

        $documentName = \CUtil::JSEscape($file->getName());
        $items = array();
        foreach ($this->getDocumentHandlersForEditingFile() as $handlerData) {
            $items[] = array(
                'text' => $handlerData['name'],
                'onclick' => 'BX.Disk.Viewer.Actions.runActionEdit({name: \'' .$documentName . '\', objectId: \'' . $file->getId() . '\', serviceCode: \'' . $handlerData['code'] . '\'})',
            );
        }
        $fileViewerAttrs->addAction(array(
            'type' => 'edit',
            'buttonIconClass' => ' ',
            'action' => 'BX.Disk.Viewer.Actions.runActionDefaultEdit',
            'params' => array(
                'objectId' => $file->getId(),
                'name' => $documentName,
            ),
            'items' => $items,
        ));

        return $fileViewerAttrs;
    }

    private function getDocumentHandlersForEditingFile()
    {
        $handlers = array();
        foreach ($this->listCloudHandlersForCreatingFile() as $handler) {
            $handlers[] = array(
                'code' => $handler::getCode(),
                'name' => $handler::getName(),
            );
        }

        return array_merge($handlers, array(array(
            'code' => LocalDocumentController::getCode(),
            'name' => LocalDocumentController::getName(),
        )));
    }

    /**
     * @return \Bitrix\Disk\Document\DocumentHandler[]
     */
    private function listCloudHandlersForCreatingFile()
    {
        if (!Configuration::canCreateFileByCloud()) {
            return array();
        }

        $list = array();
        $documentHandlersManager = Driver::getInstance()->getDocumentHandlersManager();
        foreach ($documentHandlersManager->getHandlers() as $handler) {
            if ($handler instanceof FileCreatable) {
                $list[] = $handler;
            }
        }

        return $list;
    }

    protected function getTaskInfo($taskIds)
    {
        if (empty($taskIds)) {
            return array();
        }

        $taskInfo = CSTools\Utils::getTaskData(
            array('ID' => $taskIds),
            array('ID', 'CREATED_BY', 'RESPONSIBLE_ID', 'REAL_STATUS', 'STATUS_CHANGED_DATE', 'UF_*')
        );

        if (empty($taskInfo)) {
            return array();
        }

        $taskStatusIds = array();
        foreach ($taskInfo as $task) {
            $taskTypeId = $task[CSEntities\TaskTypeField::TASK_TYPE_FIELD_NAME];
            $taskStatusIds[$taskTypeId][] = $task[CSEntities\TaskStatusField::getFieldNamePrefix() . $taskTypeId];
        }

        $taskStatuses = array();
        foreach ($taskStatusIds as $typeId => $statusIds) {
            $statusList = CSEntities\TaskStatusElement::getListByFilter(array('ID' => $statusIds), $this->allTaskTypes[$typeId]);
            $statusList = CSEntities\TaskStatusElement::makeIdsAsArrayKeys($statusList);
            $taskStatuses[$typeId] = $statusList;
        }

        foreach ($taskInfo as $taskId => $task) {
            $taskTypeId = $task[CSEntities\TaskTypeField::TASK_TYPE_FIELD_NAME];
            $taskStatusId = $task[CSEntities\TaskStatusField::getFieldNamePrefix() . $taskTypeId];
            $taskInfo[$taskId]['STATUS_NAME'] = $taskStatuses[$taskTypeId][$taskStatusId]->getName();
            $taskInfo[$taskId]['DETAIL_URL'] = \CComponentEngine::makePathFromTemplate($this->taskDetailUrlTemplate, array(
                "user_id" => $GLOBALS['USER']->GetID(),
                "task_id" => $taskId,
                "action" => "view"
            ));
        }

        return $taskInfo;
    }

    protected function initForums()
    {
        //region Комментарии к согласованию
        $this->forums[] = array_merge(
            $this->initForumParams(
                $this->contract->getId(),
                SedTools\Utils::FORUM_ENTITY_TYPE,
                SedTools\Utils::TOPIC_PREFIX
            ),
            array('TITLE' => Loc::getMessage('SED_DETAIL_COMPONENT.CONTRACT_FORUM_TITLE'))
        );
        //endregion

        if (Option::get('intervolga.sed', 'intervolga_sed_show_task_forums_on_detail_page') == 'Y') {
            //region Комментарии Основной задачи
            $this->forums[] = array_merge(
                $this->initForumParams(
                    $this->mainTask->getTaskId(),
                    \Bitrix\Forum\Comments\TaskEntity::ENTITY_TYPE,
                    \Bitrix\Forum\Comments\TaskEntity::XML_ID_PREFIX
                ),
                array('TITLE' => Loc::getMessage('SED_DETAIL_COMPONENT.MAIN_TASK_FORUM_TITLE'))
            );
            //endregion

            //region Комментарии подзадач
            foreach ($this->contractTaskTable as $contractTaskTableRow) {
                // Выводим форумы только тех ролей, у которых есть задачи
                if (array_key_exists('ID', $contractTaskTableRow['TASK'])) {
                    $taskId = $contractTaskTableRow['TASK']['ID'];
                    $this->forums[] = array_merge(
                        $this->initForumParams(
                            $taskId,
                            \Bitrix\Forum\Comments\TaskEntity::ENTITY_TYPE,
                            \Bitrix\Forum\Comments\TaskEntity::XML_ID_PREFIX
                        ),
                        array('TITLE' => $contractTaskTableRow['USER']['ROLE_NAME'])
                    );
                }
            }
            //endregion
        }
    }

    /**
     * @param $entityId
     * @param $entityType
     * @param $xmlIdPrefix
     * @throws SystemException
     */
    protected function initForumParams($entityId, $entityType, $xmlIdPrefix)
    {
        if ($entityId) {
            if (!\Bitrix\Main\Loader::includeModule('forum')) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage('SED_DETAIL_COMPONENT.FORUM_NOT_INSTALLED'));
            }

            $forumId = Option::get('intervolga.sed', 'INTERVOLGA_SED_FORUM_ID');
            if (
                intval($forumId) <= 0 ||
                !\CForumNew::getByIDEx($forumId, SITE_ID)
            ) {
                throw new SystemException(Loc::getMessage(
                    'SED_DETAIL_COMPONENT.WRONG_FORUM_TABLE_ID',
                    array(
                        '#URL#' => '/bitrix/admin/settings.php?mid=intervolga.sed&mid_menu=1'
                    )
                ));
            }

            $xmlId = $xmlIdPrefix . $entityId;
            $feed = new \Bitrix\Forum\Comments\Feed(
                $forumId,
                array(
                    "type" => $entityType,
                    "id" => $entityId,
                    "xml_id" => $xmlId,
                )
            );

            $forum = $feed->getForum();
            $topic = $feed->getTopic();

            if (!$topic['ID']) {
                $topic['ID'] = SedTools\Utils::addForumTopic($forum['ID'], $xmlId);
            }

            return array(
                'ENTITY_ID' => $entityId,
                'ENTITY_XML_ID' => $xmlId,
                'FORUM_ID' => $forum['ID'],
                'ENTITY_TYPE' => $entityType
            );
        }
    }

    protected function fillResult()
    {
        global $USER_FIELD_MANAGER;

        $this->arResult['CONTRACT_LIST_BTN'] = SedTools\Html::getPageTitleButton(
            Loc::getMessage('SED_DETAIL_COMPONENT.CONTRACT_LIST_BTN'),
            $this->arParams['SEF_FOLDER'] . $this->arParams['URL_TEMPLATES']['list'],
            'blue'
        );

        if ($this->canPause) {
            $this->arResult['CONTRACT_PAUSE_BTN'] = SedTools\Html::getPageTitleButton(Loc::getMessage('SED_DETAIL_COMPONENT.CONTRACT_PAUSE_BTN'), null, null, 'contract-pause-btn');
        } elseif ($this->canResume) {
            $this->arResult['CONTRACT_RESUME_BTN'] = SedTools\Html::getPageTitleButton(Loc::getMessage('SED_DETAIL_COMPONENT.CONTRACT_RESUME_BTN'), null, 'accept', 'contract-pause-btn');
        }

        $this->arResult['CAN_UPDATE_FILE'] = $this->canUpdateFile;
        $this->arResult['FORUMS'] = $this->forums;
        $this->arResult['FILE_UPLOAD_FORM'] = $this->fileUploadForm;
        $this->arResult['CONTRACT_TASK_TABLE'] = $this->contractTaskTable;

        $this->arResult['CONTRACT_INFO'] = array(
            'NAME' => $this->contract->getName(),
            'STATUS' => array(
                'NAME' => $this->contract->getReferenceProcessStatusName(),
                'CSS_CLASS' => mb_strtolower($this->contract->getReferenceProcessStatusCode())// $this->contract->getProcessStatusId()
            ),
            'DAYS_TO_HARMONIZE' => $this->contract->getDaysToHarmonize(),
            'CONTRACT_DEADLINE' => $this->getContractDeadLine(),
            'MAIN_TASK_URL' => \CComponentEngine::makePathFromTemplate($this->taskDetailUrlTemplate, array(
                "user_id" => $GLOBALS['USER']->GetID(),
                "task_id" => $this->mainTask->getTaskId(),
                "action" => "view"
            )),
            'FILE_VIEWER_ATTRIBUTES' => $this->getFileViewerAttributes(),
            'FILE_DOWNLOAD_URL' => $this->getFileUrlForStartEdit()
        );

        if (extension_loaded('dom')) {
            $this->arResult['CONTRACT_INFO']['PDF_REQUEST_LINK'] = '?' . static::REQUEST_PDF_PARAM_NAME . '=' . static::REQUEST_PDF_PARAM_VALUE;
        }

        if ($this->initiatorId) {
            $dbUserRes = \Bitrix\Main\UserTable::getList(array(
                'select' => array('ID', 'NAME', 'SECOND_NAME', 'LAST_NAME'),
                'filter' => array('ID' => $this->initiatorId)
            ));
            if ($arUserRes = $dbUserRes->fetch()) {
                $this->arResult['CONTRACT_INFO']['INITIATOR']['FULL_NAME'] = \CUser::FormatName(
                    \CSite::GetNameFormat(),
                    $arUserRes,
                    true,
                    false
                );
            }
            $this->arResult['CONTRACT_INFO']['INITIATOR']['DETAIL_URL'] = \CComponentEngine::makePathFromTemplate($this->userDetailUrlTemplate, array("USER_ID" => $this->initiatorId));
        }

        $mainTaskInfo = CSTools\Utils::getTaskData(
            array('ID' => $this->mainTask->getTaskId()),
            array('ID', 'DATE_START')
        );
        $mainTaskInfo = array_shift($mainTaskInfo);
        if (!empty($mainTaskInfo['DATE_START'])) {
            $this->arResult['CONTRACT_INFO']['HARMONIZATION_START'] = FormatDate('SHORT', MakeTimeStamp($mainTaskInfo['DATE_START']));
        }

        $dbContractUserFieldsResult = ContractUserFieldsTable::getList([
            'filter' => [
                'PROCESS_ID' => $this->contract->getProcessId()
            ]
        ]);
        $arContractUserFields = [];
        while ($arContractUserFieldsResult = $dbContractUserFieldsResult->fetch()) {
            $arContractUserFields[$arContractUserFieldsResult['FIELD_NAME']] = $arContractUserFieldsResult;
        }

        $arUserFields = $USER_FIELD_MANAGER->GetUserFields(
            \Intervolga\Sed\Tables\ContractTable::getUfId(),
            0,
            LANGUAGE_ID
        );

        foreach ($arUserFields as $key => $value) {
            if (is_array($arContractUserFields) && array_key_exists($key, $arContractUserFields)) {
                $arUserFields[$key]['SORT'] = $arContractUserFields[$key]['SORT'];
                $arUserFields[$key]['MANDATORY'] = $arContractUserFields[$key]['REQUIRED'];
                $arUserFields[$key]['SHOW'] = $arContractUserFields[$key]['SHOW'];
            } else {
                $arUserFields[$key]['SHOW'] = 'Y';
            }
        }
        foreach ($arUserFields as $key => $value) {
            if ($value['SHOW'] == 'Y') {
                $arUserFields[$key]['VALUE'] = $this->contract->getUserFieldValue($key);
                $this->arResult['CONTRACT_INFO']['USER_FIELDS'][$key] = $arUserFields[$key];
            }
        }
        usort($this->arResult['CONTRACT_INFO']['USER_FIELDS'], function ($a, $b) {
            return ($a['SORT'] <=> $b['SORT']);
        });
    }

    protected function getContractDeadLine(){
        $connection = Bitrix\Main\Application::getConnection();
        $sql = "
CREATE TABLE IF NOT EXISTS `intervolga_sed_contract_deadline` (
    `contract_id` int(11) NOT NULL,
    `deadline` date NOT NULL,
    PRIMARY KEY (`contract_id`)
);        ";
        $connection->queryExecute($sql);

        $sql = "SELECT deadline FROM intervolga_sed_contract_deadline WHERE CONTRACT_ID = ".$this->contract->getId();
        $val = $connection->queryScalar($sql);

        if($val != null)
            return $val->format('d.m.Y');

        return $val;
    }
}

function cmp($particiant1, $particiant2){
    $result = $particiant1->getFieldValue('ROLE_ID') > $particiant2->getFieldValue('ROLE_ID');
    return $result;
}

function roleInfoCmp($r1, $r2){
    $result = $r1['ROLE_NAME'] > $r2['ROLE_NAME'];
    return $result;
}
