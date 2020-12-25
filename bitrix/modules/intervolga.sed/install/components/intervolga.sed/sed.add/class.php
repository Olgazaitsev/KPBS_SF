<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Application;
use Intervolga\Sed\Entities as SedEntities;
use Intervolga\Sed\Tools as SedTools;
use Intervolga\Sed\Tables\ContractUserFieldsTable;

use \Bitrix\Main\Localization\Loc;

class SedAddComponent extends CBitrixComponent
{
    const REQUEST_BTN_VALUE = 'add';
    const REQUEST_BTN_NAME = 'action';
    const PARTICIPANT_INPUT_PREFIX = 'participant_id_';

    /** @var array $inputNameList */
    protected $inputNameList;
    /** @var SedEntities\Process[] $processList */
    protected $processList = array();
    /** @var SedEntities\ParticipantRole[] $processList */
    protected $roleList = array();
    /** @var array $userFields */
    protected $userFields = array();
    /** @var array $fileInfo */
    protected $fileInfo = array();
    /** @var null|int $contractId */
    protected $contractId = null;


    public function executeComponent()
    {
        try {
            $this->prepareParams()
                ->initProcessList()
                ->initUserFields();

            if ($this->checkRequest()) {
                LocalRedirect($this->arParams['SEF_FOLDER'] . $this->arParams['URL_TEMPLATES']['list'] . $this->contractId . '/');
            } else {
                $this->initRoleList()
                    ->fillResult()
                    ->includeComponentTemplate();
            }
        } catch (\Bitrix\Main\SystemException $e) {
            echo SedTools\Html::getErrorHtml($e->getMessage());
        }

        return null;
    }

    /**
     * @return $this
     */
    protected function prepareParams()
    {
        $this->arResult['ERRORS'] = array();

        $this->inputNameList = array(
            'PROCESS' => 'process_id',
            'CONTRACT_NAME' => 'contract_name',
            'FILE' => 'contract_file',
            'INITIATOR' => 'initiator_id',
            'DAYS_TO_HARMONIZE' => 'days_to_harmonize',
        );

        return $this;
    }

    /**
     * @return bool
     */
    protected function checkRequest()
    {
        if (check_bitrix_sessid() && $this->request->getPost(static::REQUEST_BTN_NAME) == static::REQUEST_BTN_VALUE) {
            if ($this->checkRequestFields()) {
                try {
                    $processStatus = SedEntities\ProcessStatus::getFirstStatusByProcessId($this->request->getPost($this->inputNameList['PROCESS']));
                    $contract = SedEntities\Contract::createEmpty()
                        ->setName($this->request->getPost($this->inputNameList['CONTRACT_NAME']))
                        ->setProcessId($this->request->getPost($this->inputNameList['PROCESS']))
                        ->setProcessStatusId($processStatus->getId())
                        ->setDaysToHarmonize($this->request->getPost($this->inputNameList['DAYS_TO_HARMONIZE']));

                    foreach ($this->userFields[$this->request->getPost($this->inputNameList['PROCESS'])] as $userField) {
                        if ($userField['SHOW'] == 'Y') {
                            switch ($userField['USER_TYPE_ID']) {
                                case 'file':
                                    $filesInfoSao = $this->request->getFile($userField['FIELD_NAME']);
                                    if (is_array($filesInfoSao['name'])) {
                                        $filesInfoAos = [];
                                        $ks = array_keys($filesInfoSao['name']);
                                        for ($i = 0; $i < count($ks); ++$i) {
                                            $ar = array();
                                            foreach ($filesInfoSao as $k1 => $v1) {
                                                $ar[$k1] = $v1[$ks[$i]];
                                            }
                                            $filesInfoAos[] = $ar;
                                        }
                                    } else {
                                        $filesInfoAos = $filesInfoSao;
                                    }
                                    $contract->setUserFieldValue($userField['FIELD_NAME'], $filesInfoAos);
                                    break;
                                default:
                                    $value = $this->request->getPost($userField['FIELD_NAME']);
                                    $contract->setUserFieldValue($userField['FIELD_NAME'], $value);
                                    break;
                            }
                        }
                    }

                    $this->contractId = $contract->save();

                    $fileEntity = SedTools\Utils::saveFileToStorage(
                        $this->fileInfo,
                        $this->request->getPost($this->inputNameList['INITIATOR']),
                        $this->contractId,
                        $this->request->getPost($this->inputNameList['CONTRACT_NAME'])
                    );

                    SedEntities\Contract::getById($this->contractId)
                        ->setFileId($fileEntity->getId())
                        ->save();

                    $initiatorRoleId = null;
                    $participantUserIds = array();
                    foreach ($this->roleList as $role) {

                        if ($role->isInitiator()) {
                            $initiatorRoleId = $role->getId();
                            $userPostKey = $this->inputNameList['INITIATOR'];
                        } else {
                            $userPostKey = static::PARTICIPANT_INPUT_PREFIX . $role->getId();
                            $participantUserIds[] = $this->request->getPost($userPostKey);
                        }

                        SedEntities\Participant::createEmpty()
                            ->setContractId($this->contractId)
                            ->setRoleId($role->getId())
                            ->setUserId($this->request->getPost($userPostKey))
                            ->save();
                    }

                    $initiatorId = (int)$this->request->getPost($this->inputNameList['INITIATOR']);
                    $initiatorTType = \Intervolga\Sed\Entities\TaskTypeElement::getByXmlId(SedEntities\Process::INITIATOR_TTYPE_CODE);
                    $initiatorTaskParams = array(
                        'TITLE' => Loc::getMessage('SED_ADD_COMP.TIT_CONTRACT_NEGOTIATION') . Loc::getMessage('SED_ADD_COMP.SEPARATOR') . $this->request->getPost($this->inputNameList['CONTRACT_NAME']),
                        'DESCRIPTION' => '<a href="' . $this->arParams['SEF_FOLDER'] . $this->arParams['URL_TEMPLATES']['list'] . $this->contractId . '/">' . Loc::getMessage('SED_ADD_COMP.TIT_NEGOTIATION_PROC') . '</a>',
                        'DESCRIPTION_IN_BBCODE' => 'N',
                        'RESPONSIBLE_ID' => $initiatorId,
                        'CREATED_BY' => $initiatorId,
                        'UF_TASK_TTYPE' => $initiatorTType->getId(),
                        'UF_TASK_WEBDAV_FILES' => array('n' . $fileEntity->getId()),
                        'AUDITORS' => $participantUserIds, //наблюдатели задачи
                    );

                    SedTools\Utils::createContractRelatedTask(
                        $initiatorTaskParams,
                        $initiatorId,
                        $initiatorRoleId,
                        $initiatorRoleId,
                        $this->contractId,
                        $initiatorTType->getId(),
                        null,
                        true
                    );

                    $this->addMessageToForum($this->contractId, $fileEntity);

                    return true;
                } catch (\Exception $e) {
                    if ($this->contractId) {
                        SedEntities\Contract::delete($this->contractId);
                    }
                    $this->arResult['ERRORS'][] = SedTools\Html::getErrorHtml($e->getMessage());
                }
            }
        }

        return false;
    }

    /**
     * @param int $entityId
     * @param \Bitrix\Disk\File $file
     */
    protected function addMessageToForum($entityId, $file)
    {
        $urlManager = \Bitrix\Disk\Driver::getInstance()->getUrlManager();

        SedTools\Utils::addForumFileMessage($entityId, \Bitrix\Main\Localization\Loc::getMessage('SED_ADD_COMP.NEW_FILE_UPLOAD_COMMENT', array(
            '#FILE_NAME#' => $file->getOriginalName(),
            '#URL#' => $urlManager->getUrlForDownloadVersion($file->getLastVersion())
        )));
    }

    protected function checkRequestFields()
    {
        foreach ($this->inputNameList as $key => $field) {
            if (!$this->request->getPost($field) && $field != $this->inputNameList['FILE']) {
                $this->arResult['ERRORS'][] = SedTools\Html::getErrorHtml(Loc::getMessage('SED_ADD_COMPONENT.EMPTY_REQUIRED_FIELDS'));
                return false;
            }
        }

        $this->roleList = SedEntities\ParticipantRole::getListByFilter(array('PROCESS_ID' => $this->request->getPost($this->inputNameList['PROCESS'])));
        foreach ($this->roleList as $role) {
            $userPostKey = ($role->isInitiator()) ? $this->inputNameList['INITIATOR'] : static::PARTICIPANT_INPUT_PREFIX . $role->getId();
            $userId = $this->request->getPost($userPostKey);
            if (!$userId) {
                $this->arResult['ERRORS'][] = SedTools\Html::getErrorHtml(Loc::getMessage('SED_ADD_COMPONENT.EMPTY_REQUIRED_FIELDS'));
                return false;
            }
        }

        if (!$this->checkFile()) {
            $this->arResult['ERRORS'][] = SedTools\Html::getErrorHtml(Loc::getMessage('SED_ADD_COMPONENT.FILE_ERROR'));
            return false;
        }

        return true;
    }

    protected function checkFile()
    {
        $srcFileInfo = $this->request->getFile($this->inputNameList['FILE']);

        $this->fileInfo = \CFile::MakeFileArray($srcFileInfo['tmp_name']);
        if (!$this->fileInfo) {
            return false;
        }
        $this->fileInfo['extension'] = SedTools\Utils::getFileExtByName($srcFileInfo['name']);

        return true;
    }

    protected function initProcessList()
    {
        $this->processList = SedEntities\Process::getListAll();
        if (empty($this->processList)) {
            throw new \Bitrix\Main\SystemException(Loc::getMessage('SED_ADD_COMPONENT.EMPTY_PROCESS_LIST'));
        }

        return $this;
    }

    protected function initRoleList()
    {
        $this->roleList = SedEntities\ParticipantRole::getListAll();

        return $this;
    }

    protected function initUserFields()
    {
        global $USER_FIELD_MANAGER;

        $requestUserFieldsMap = $this->getContractUserFieldValuesFromRequest();
        $dbContractUserFieldsResult = ContractUserFieldsTable::getList();
        $arContractsUserFields = [];
        while ($arContractUserFieldsResult = $dbContractUserFieldsResult->fetch()) {
            $arContractsUserFields[$arContractUserFieldsResult['PROCESS_ID']][$arContractUserFieldsResult['FIELD_NAME']] = $arContractUserFieldsResult;
        }

        $arUserFields = $USER_FIELD_MANAGER->GetUserFields(
            \Intervolga\Sed\Tables\ContractTable::getUfId(),
            0,
            LANGUAGE_ID
        );

        foreach ($this->processList as $process) {
            $processUserFields = &$this->userFields[$process->getId()];
            $arContractUserFields = &$arContractsUserFields[$process->getId()];
            $processUserFields = $arUserFields;

            foreach ($processUserFields as $key => $value) {
                if (is_array($arContractUserFields) && array_key_exists($key, $arContractUserFields)) {
                    $processUserFields[$key]['SORT'] = $arContractUserFields[$key]['SORT'];
                    $processUserFields[$key]['MANDATORY'] = $arContractUserFields[$key]['REQUIRED'];
                    $processUserFields[$key]['SHOW'] = $arContractUserFields[$key]['SHOW'];
                } else {
                    $processUserFields[$key]['SHOW'] = 'Y';
                }

                if (array_key_exists($key, $requestUserFieldsMap)) {
                    $processUserFields[$key]['VALUE'] = $requestUserFieldsMap[$key];
                }
            }

            usort($processUserFields, function ($a, $b) {
                return ($a['SORT'] <=> $b['SORT']);
            });

            unset($processUserFields);
            unset($arContractUserFields);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function fillResult()
    {
        global $USER;

        $userNameList = SedTools\Utils::getRealUserNamesByRoleList($this->roleList, true);
        $this->arResult['PROCESS_LIST'] = array();
        foreach ($this->processList as $process) {
            $inputList = array(
                array(
                    'TYPE' => 'HIDDEN',
                    'NAME' => $this->inputNameList['PROCESS'],
                    'DEFAULT' => $process->getId(),
                    //'REQUIRED' => true,
                ),
                array(
                    'TYPE' => 'TEXT',
                    'LABEL' => Loc::getMessage('SED_ADD_COMPONENT.CONTRACT_NAME'),
                    'NAME' => $this->inputNameList['CONTRACT_NAME'],
//                    'REQUIRED' => true,
                    'DEFAULT' => null,
                    'PLACEHOLDER' => Loc::getMessage('SED_ADD_COMPONENT.CONTRACT_NAME')
                ),
                array(
                    'TYPE' => 'FILE',
                    'LABEL' => Loc::getMessage('SED_ADD_COMPONENT.CONTRACT_FILE'),
                    'NAME' => $this->inputNameList['FILE'],
//                    'REQUIRED' => true,
                    'DEFAULT' => null,
                ),
            );

            foreach ($this->roleList as $role) {
                if ($role->getProcessId() == $process->getId()) {
                    $userInputData = array(
                        'TYPE' => 'USER',
                        'ROLE_ID' => $role->getId(),
//                        'REQUIRED' => true,
                        'NAME' => $role->isInitiator() ? $this->inputNameList['INITIATOR'] : static::PARTICIPANT_INPUT_PREFIX . $role->getId(),
                        'DEFAULT' => ($role->isInitiator()) ? $userNameList[$USER->GetId()] : $userNameList[(int)$role->getDefaultUserId()],
                    );
                    $userInputData['LABEL'] = $role->getName();
                    $inputList[] = $userInputData;
                }
            }

            $inputList[] = array(
                'TYPE' => 'NUMBER',
                'LABEL' => Loc::getMessage('SED_ADD_COMPONENT.DAYS_TO_HARMONIZE'),
                'NAME' => $this->inputNameList['DAYS_TO_HARMONIZE'],
                'DEFAULT' => SedEntities\Contract::DAYS_TO_HARMONIZE_DEFAULT,
                'CLASS' => 'row-days-harmonize',
//                'REQUIRED' => true,
            );

            $this->arResult['PROCESS_LIST'][$process->getId()] = array(
                'NAME' => $process->getName(),
                'INPUT_LIST' => $inputList,
                'USER_FIELDS_INPUT_LIST' => $this->userFields[$process->getId()]
            );
        }

        $this->arResult['PAGE_TITLE_BTN'] = SedTools\Html::getPageTitleButton(
            Loc::getMessage('SED_ADD_COMPONENT.SED_LIST_BTN'),
            $this->arParams['SEF_FOLDER'] . $this->arParams['URL_TEMPLATES']['list'],
            'blue'
        );

        $this->arResult['SUBMIT_BTN_INFO'] = array(
            'LABEL' => Loc::getMessage('SED_ADD_COMPONENT.START_HARMONIZATION'),
            'VALUE' => static::REQUEST_BTN_VALUE,
            'NAME' => static::REQUEST_BTN_NAME
        );

        reset($this->processList);
        $this->arResult['PROCESS_TO_DISPLAY'] = current($this->processList)->getId();

        return $this;
    }

    private function getContractUserFieldValuesFromRequest() {
        $request = Application::getInstance()->getContext()->getRequest();
        $contractRequestUserFieldCodesList = SedTools\Utils::getContractRequestUserFieldCodesList();
        $userFieldValuesFromRequest = array();
        foreach ($contractRequestUserFieldCodesList as $userFieldCode) {
            $userFieldValuesFromRequest[$userFieldCode] = $request->get($userFieldCode);
        }
        return $userFieldValuesFromRequest;
    }
}