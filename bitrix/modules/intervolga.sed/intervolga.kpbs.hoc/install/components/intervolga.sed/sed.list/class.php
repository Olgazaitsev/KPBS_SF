<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

use Intervolga\Sed\Subscription\Subscriptions;
use Intervolga\Sed\Tables\ContractTable;

use Intervolga\Sed\Entities as SedEntities;
use Intervolga\Sed\Tools as SedTools;

use Intervolga\Sed\Entities as CSEntities;
use Intervolga\Sed\Tools as CSTools;

class SedListComponent extends CBitrixComponent
{
    const DEFAULT_PAGE_SIZE = 10;
    const GRID_ID_PREFIX = 'sed_grid_';
    const FILTER_BY_LOGIC = [
        '=' => ['PARTICIPANT.USER_ID'],
        '%' => ['NAME'],
        '@' => ['PROCESS_STATUS_ID']
    ];

    protected $gridDataList;
    protected $processList;
    protected $taskTypeList;


    public function executeComponent()
    {
        global $APPLICATION;

        if (!Subscriptions::checkForUiComponent()) {
            return null;
        }

        try {
            //            if($this->startResultCache()) {
            $this->prepareParams()
                ->getData()
                ->fillResult();
//                    ->endResultCache();
//            }

            $bodyClass = $APPLICATION->GetPageProperty('BodyClass');
            $APPLICATION->SetPageProperty('BodyClass', ($bodyClass ? $bodyClass . ' ' : '') . 'pagetitle-toolbar-field-view');

            $this->includeComponentTemplate();

        } catch (\Bitrix\Main\SystemException $e) {
            echo SedTools\Html::getErrorHtml($e->getMessage());
        }

        return null;
    }

    protected function prepareParams()
    {
        $this->arResult['ERRORS'] = array();
        $this->gridDataList = array();
        $this->taskTypeList = CSEntities\TaskTypeElement::makeIdsAsArrayKeys(CSEntities\TaskTypeElement::getListAll());

        return $this;
    }

    protected static function getGridHeader($roleNameList)
    {
        global $USER_FIELD_MANAGER;

        $header = array(
            'NAME' => array(
                'id' => 'NAME',
                'name' => Loc::getMessage('SED_LIST_COMP.HDR_NAME'),
                'sort' => 'NAME',
                'default' => true,
                'editable' => false
            ),
            'ID' => array(
                'id' => 'ID',
                'name' => 'ID',
                'sort' => 'ID',
                'default' => true,
                'editable' => false
            ),
            'PROCESS_STATUS_ID' => array(
                'id' => 'PROCESS_STATUS_ID',
                'name' => Loc::getMessage('SED_LIST_COMP.HDR_STATUS'),
                'sort' => 'PROCESS_STATUS_ID',
                'default' => true,
                'editable' => false
            ),
        );

        ksort($roleNameList, SORT_NUMERIC);
        foreach ($roleNameList as $roleId => $roleName) {
            $header[$roleId] = array(
                'id' => 'ROLE_' . $roleId,
                'name' => $roleName,
//                'name' => SedTools\Html::getContractListTableItem($roleName, 0, 'header'),
                'default' => true,
                'editable' => false
            );
        }

        $arUserFields = $USER_FIELD_MANAGER->GetUserFields(
            ContractTable::getUfId(),
            0,
            LANGUAGE_ID
        );
        foreach ($arUserFields as $fieldName => $arUserField) {
            if ($arUserField['SHOW_IN_LIST'] == 'Y') {
                $header[] = array(
                    'id' => $fieldName,
                    'name' => htmlspecialcharsbx($arUserField['LIST_COLUMN_LABEL'] ? $arUserField['LIST_COLUMN_LABEL'] : $arUserField['FIELD_NAME']),
                    'sort' => $arUserField['MULTIPLE'] == 'N' ? $fieldName : false,
                );
            }
        }

        return $header;
    }

    protected static function getGridFilter(SedEntities\Process $process, $roleNameList)
    {
        /**
         * TODO: implement filtering by userfields
         * @see /bitrix/components/bitrix/crm.deal.list/filter.ajax.php for implementation details
         */
        $arProcessRes = \Intervolga\Sed\Tables\ProcessStatusTable::getList([
            'select' => ['ID', 'NAME'],
            'filter' => ['PROCESS_ID' => $process->getId()],
            'order' => ['ID']
        ])->fetchAll();
        $arProcessList = [];
        foreach ($arProcessRes as $arProcess) {
            $arProcessList[$arProcess['ID']] = $arProcess['NAME'];
        }
        $gridFilter = [
            [
                'id' => 'NAME',
                'name' => Loc::getMessage('SED_LIST_COMP.HDR_NAME'),
                'default' => true
            ],
            [
                'id' => 'PROCESS_STATUS_ID',
                'name' => Loc::getMessage('SED_LIST_COMP.HDR_STATUS'),
                'type' => 'list',
                'params' => ['multiple' => 'Y'],
                'items' => $arProcessList,
                'default' => true
            ],
            [
                'id' => 'PARTICIPANT.USER_ID',
                'name' => Loc::getMessage('SED_LIST_COMP.PARTICIPANT'),
                'type' => 'dest_selector',
                'params' => array(
                    'multiple' => 'Y',
                    'context' => 'PARTICIPANT.USER_ID',
                    'fieldName' => 'PARTICIPANT.USER_ID',
                    'contextCode' => 'U',
                    'enableAll' => 'N',
                    'enableSonetgroups' => 'N',
                    'allowEmailInvitation' => 'N',
                    'allowSearchEmailUsers' => 'N',
                    'departmentSelectDisable' => 'N',
                    'isNumeric' => 'Y',
                    'prefix' => 'U',
                ),
                'selector' => array(
                    'TYPE' => 'user',
                    'DATA' => array(
                        'ID' => 'PARTICIPANT.USER_ID',
                        'FIELD_ID' => 'PARTICIPANT.USER_ID'
                    )
                ),
                'default' => true,
            ]
        ];

        foreach ($roleNameList as $roleId => $roleName) {
            $gridFilter[] = array(
                'id' => 'ROLE_' . $roleId,
                'name' => $roleName,
                'type' => 'dest_selector',
                'params' => array(
                    'multiple' => 'Y',
                    'context' => 'ROLE_' . $roleId,
                    'fieldName' => 'ROLE_' . $roleId,
                    'contextCode' => 'U',
                    'enableAll' => 'N',
                    'enableSonetgroups' => 'N',
                    'allowEmailInvitation' => 'N',
                    'allowSearchEmailUsers' => 'N',
                    'departmentSelectDisable' => 'N',
                    'isNumeric' => 'Y',
                    'prefix' => 'U',
                ),
                'selector' => array(
                    'TYPE' => 'user',
                    'DATA' => array(
                        'ID' => 'ROLE_' . $roleId,
                        'FIELD_ID' => 'ROLE_' . $roleId
                    )
                ),
                'default' => true,
            );
        }

        return $gridFilter;
    }

    /**
     * @return $this
     * @throws \Bitrix\Main\SystemException
     */
    protected function getData()
    {
        $processList = SedEntities\Process::getListAll();
        if (empty($processList)) {
            throw new \Bitrix\Main\SystemException(\Bitrix\Main\Localization\Loc::getMessage('SED_LIST_COMPONENT.EMPTY_PROCESS_LIST'));
        } else {
            foreach ($processList as $process) {
                if (
                    $this->request->isAjaxRequest() &&
                    $this->request->getQuery('grid_action') !== null &&
                    $this->request->getQuery('grid_id') != static::GRID_ID_PREFIX . $process->getId() ||
                    !SedEntities\Contract::getCountByFilter(array('PROCESS_ID' => $process->getId()))
                ) {
                    continue;
                }

                $this->gridDataList[$process->getId()] = $this->getGridData($process);
            }
        }

        return $this;
    }

    /**
     * @param SedEntities\Process $process
     * @param int $contractCount
     * @return array
     */
    protected function getGridData(SedEntities\Process $process)
    {
        global $USER, $USER_FIELD_MANAGER;

        $userFullName = '';
        $initiatorRole = CSEntities\ParticipantRole::getProcessInitiator($process->getId());
        $initiatorRoleId = $initiatorRole->getId();
        $dbUserRes = \Bitrix\Main\UserTable::getList(array(
            'select' => array('ID', 'NAME', 'SECOND_NAME', 'LAST_NAME'),
            'filter' => array('ID' => $USER->GetID())
        ));
        if ($arUserRes = $dbUserRes->fetch()) {
            $userFullName = \CUser::FormatName(
                \CSite::GetNameFormat(),
                $arUserRes,
                true,
                false
            );
        }

        $filter = ['PROCESS_ID' => $process->getId()];
        $filterPresets = array(
            'filter_i_initiator' => array(
                'name' => \Bitrix\Main\Localization\Loc::getMessage('SED_LIST_COMP.FILTER_PRESET.I_INITIATOR'),
                'fields' => array(
                    "ROLE_{$initiatorRoleId}_label" => $userFullName,
                    "ROLE_{$initiatorRoleId}" => $USER->GetID(),
                )
            ),
            'filter_i_participant' => array(
                'name' => \Bitrix\Main\Localization\Loc::getMessage('SED_LIST_COMP.FILTER_PRESET.I_PARTICIPANT'),
                'fields' => array(
                    'PARTICIPANT.USER_ID_label' => $userFullName,
                    'PARTICIPANT.USER_ID' => $USER->GetID(),
                )
            ),
        );
        $filterOption = new \Bitrix\Main\UI\Filter\Options(static::GRID_ID_PREFIX . $process->getId(), $filterPresets);
        $filterData = $filterOption->getFilter();
        foreach ($filterData as $key => $value) {
            if (empty($value))
                continue;

            if (substr($key, -5) == '_from') {
                $op = '>=';
                $new_key = substr($key, 0, -5);
            } else if (substr($key, -3) == '_to') {
                $op = '<=';
                $new_key = substr($key, 0, -3);
                $newKey = substr($key, 0, -3);

                if (in_array($newKey, array('TIMESTAMP_X', 'DATE_CREATE'))) {
                    global $DB;
                    $dateFormat = $DB->dateFormatToPHP(Csite::getDateFormat());
                    $dateParse = date_parse_from_format($dateFormat, $value);
                    if (!strlen($dateParse['hour']) && !strlen($dateParse['minute']) && !strlen($dateParse['second'])) {
                        $timeFormat = $DB->dateFormatToPHP(CSite::getTimeFormat());
                        $value .= ' ' . date($timeFormat, mktime(23, 59, 59, 0, 0, 0));
                    }
                }
            } else if (preg_match('/^ROLE_(\d+)$/i' . BX_UTF_PCRE_MODIFIER, $key, $arMatch)) {
                $filter[] = array(
                    'LOGIC' => 'AND',
                    '@PARTICIPANT.ROLE_ID' => $arMatch[1],
                    '=PARTICIPANT.USER_ID' => $value,
                );
                continue;
            } else if (in_array($key, self::FILTER_BY_LOGIC['='])) {
                $op = '=';
                $new_key = $key;
            } else if (in_array($key, self::FILTER_BY_LOGIC['%'])) {
                $op = '%';
                $new_key = $key;
            } else if (in_array($key, self::FILTER_BY_LOGIC['@'])) {
                $op = '@';
                $new_key = $key;
            } else if ($key == 'FIND') {
                $filter[] = array(
                    'LOGIC' => 'OR',
                    '%NAME' => $value,
                );
                continue;
            } else {
                continue;
            }

            $filter[$op . $new_key] = $value;
        }

        $event = new \Bitrix\Main\Event(
            'intervolga.sed',
            'OnBuildContractListFilter',
            [
                'filter' => &$filter
            ]
        );
        $event->send();

        $gridOptions = new \Bitrix\Main\Grid\Options(static::GRID_ID_PREFIX . $process->getId(), $filterPresets);
        $gridOptions->GetFilter(array());

        $gridOptionsSort = $gridOptions->getSorting(array(
            'sort' => array('ID' => 'desc'),
            'vars' => array('by' => 'by', 'order' => 'order')
        ));

        $gridOptionsNav = $gridOptions->GetNavParams(array(
            'nPageSize' => ($this->arParams['PAGE_SIZE']) ?
                $this->arParams['PAGE_SIZE'] :
                static::DEFAULT_PAGE_SIZE
        ));

        $currentPage = $this->request->offsetExists('page') ? $this->request->get('page') : 1;
        $pageNavigation = new \Bitrix\Main\UI\PageNavigation('nav-more-contracts-' . $process->getId());
        $contractCount = ContractTable::getList([
            'select' => [
                new \Bitrix\Main\ORM\Fields\ExpressionField(
                    'CNT',
                    'COUNT(DISTINCT intervolga_sed_tables_contract.ID)'
                )
            ],
            'filter' => $filter,
        ])->fetch()['CNT'];

        $pageNavigation->allowAllRecords(true)
            ->setPageSize($gridOptionsNav['nPageSize'])
            ->setRecordCount($contractCount)
            ->setCurrentPage($currentPage > 0 ? $currentPage : $pageNavigation->getPageCount());

        $contractList = SedEntities\Contract::getListByGetList(
            array(
                '*',
                'PROCESS_STATUS.NAME',
//                'PROCESS.NAME'
            ),
            $filter,
            ['ID'],
            $gridOptionsSort['sort'],
            $pageNavigation->getLimit(),
            $pageNavigation->getOffset()
        );

        $participantList = SedEntities\Participant::getListByFilter(
            array(
                'CONTRACT_ID' => SedEntities\Contract::getIdsByEntityList($contractList),
//                'ROLE.IS_INITIATOR' => false
            ),
            array(),
            array('ROLE.NAME', 'ROLE.IS_INITIATOR', 'CONTRACT.TASK.TASK_ID', 'CONTRACT.TASK.RESP_ROLE_ID')
        );

        // матрица, содержащая id задач для соответствующих пар (id договора, id роли исполнителя)
        $taskMap = array();
        $roleNameList = array();
        $taskIdList = array();
        $initiatorIdList = array();

        foreach ($participantList as $participant) {
            $roleNameList[$participant->getRoleId()] = $participant->getReferenceRoleName();
            if ($participant->getReferenceRoleIsInitiator()) {
                $initiatorIdList[$participant->getContractId()] = $participant->getUserId();
            }
            if ($participant->getRoleId() == $participant->getReferenceTaskRespRoleId()) {
                $taskMap[$participant->getContractId()][$participant->getRoleId()] = $participant->getReferenceTaskId();
                $taskIdList[] = $participant->getReferenceTaskId();
            }
        }

        $taskListData = SedTools\Utils::getTaskData(array('ID' => $taskIdList, '!' . CSEntities\TaskTypeField::TASK_TYPE_FIELD_NAME => false), array('ID', 'CREATED_BY', 'RESPONSIBLE_ID', 'REAL_STATUS', 'UF_*'));

        $taskStatusIdList = array();
        foreach ($taskListData as $task) {
            $taskTypeId = $task[CSEntities\TaskTypeField::TASK_TYPE_FIELD_NAME];
            $taskStatusIdList[$taskTypeId][] = $task[CSEntities\TaskStatusField::getFieldNamePrefix() . $taskTypeId];
        }

        $taskStatusList = array();
        foreach ($taskStatusIdList as $typeId => $statusIds) {
            $statusList = CSEntities\TaskStatusElement::getListByFilter(array('ID' => $statusIds), $this->taskTypeList[$typeId]);
            $statusList = CSEntities\TaskStatusElement::makeIdsAsArrayKeys($statusList);
            $taskStatusList[$typeId] = $statusList;
        }

        foreach ($taskListData as &$task) {
            $taskTypeId = $task[CSEntities\TaskTypeField::TASK_TYPE_FIELD_NAME];
            $taskStatusId = $task[CSEntities\TaskStatusField::getFieldNamePrefix() . $taskTypeId];
            $task['STATUS_NAME'] = $taskStatusList[$taskTypeId][$taskStatusId]->getName();
        }
        unset($task);

        $gridData = array();
        $gridData['FILTER'] = static::getGridFilter($process, $roleNameList);
        $gridData['FILTER_PRESETS'] = $filterPresets;
        $gridData['PROCESS_NAME'] = $process->getName();
        $gridData['GRID_ID'] = static::GRID_ID_PREFIX . $process->getId();
        $gridData['HEADER'] = static::getGridHeader($roleNameList);
        $gridData['SORT'] = $gridOptionsSort['sort'];
        $gridData['SORT_VARS'] = $gridOptionsSort['vars'];
        $gridData['PAGINATION'] = array(
            'PAGE_NUM' => $pageNavigation->getCurrentPage(),
            'ENABLE_NEXT_PAGE' => $pageNavigation->getCurrentPage() < $pageNavigation->getPageCount(),
            'URL' => $this->request->getRequestedPage(),
        );
        $gridData['NAV_OBJECT'] = $pageNavigation;

        $roleIdList = array_keys($roleNameList);
        $gridData['ROWS'] = array();
        foreach ($contractList as $contract) {
            $cols = array(
                'NAME' => $contract->getName(),
                'ID' => $contract->getId(),
//                'PROCESS_ID' => $contract->getReferenceProcessName(),
//                'PROCESS_STATUS_ID' => $contract->getReferenceProcessStatusName(),
                'PROCESS_STATUS_ID' => SedTools\Html::getContractListTableItem(
                    $contract->getReferenceProcessStatusName(),
                    SedTools\Html::TABLE_ITEM_TYPE_PROCESS_STATUS,
                    $contract->getProcessStatusId()
                )
            );
            $columnClasses = [];

            foreach ($roleIdList as $roleId) {
                $contactTaskIdList = $taskMap[$contract->getId()];
                if (empty($contactTaskIdList[$roleId]) || empty($taskListData[$contactTaskIdList[$roleId]]['STATUS_NAME'])) {
                    $cols['ROLE_' . $roleId] = \Bitrix\Main\Localization\Loc::getMessage('SED_LIST_COMPONENT.TABLE_ITEM_TEXT_WAITING');
                } else {
                    $cols['ROLE_' . $roleId] = $taskListData[$contactTaskIdList[$roleId]]['STATUS_NAME'];
                }
                $columnClasses['ROLE_' . $roleId] = SedTools\Html::TABLE_ITEM_TYPE_ROLE_TASK_STATUS . $taskListData[$contactTaskIdList[$roleId]]['REAL_STATUS'];
            }

            // region UserFields

            $arUserFields = $USER_FIELD_MANAGER->GetUserFields(
                ContractTable::getUfId(),
                $contract->getId(),
                LANGUAGE_ID
            );

            foreach ($arUserFields as $key => $arUserField) {
                $cols[$key] = $USER_FIELD_MANAGER->GetPublicView($arUserField);
            }

            // endregion

            $url = \CComponentEngine::makePathFromTemplate(
                $this->arParams['SEF_FOLDER'] . $this->arParams['URL_TEMPLATES']['detail'],
                array('CONTRACT' => $contract->getId())
            );
            $actions = array(
                array(
                    'ICONCLASS' => 'edit',
                    'TEXT' => Loc::getMessage('SED_LIST_COMP.ACT_OPEN'),
                    'ONCLICK' => 'jsUtils.Redirect(arguments, "' . $url . '")',
                    'DEFAULT' => true
                )
            );

            global $USER;
            if ($USER->IsAdmin() || $USER->GetID() == $initiatorIdList[$contract->getId()]) {
                $actions[] = array(
                    "ICONCLASS" => "delete",
                    "TEXT" => Loc::getMessage('SED_LIST_COMP.ACT_DELETE'),
                    "ONCLICK" => "BX.CustomTaskStatuses.removeContractItem(" . $contract->getId() . ", '" . bitrix_sessid() . "')",
                    "DEFAULT" => true
                );
            }

            $gridData['ROWS'][$contract->getId()] = array(
                'actions' => $actions,
                'columns' => $cols,
                'columnClasses' => $columnClasses,
                'editable' => false
            );
        }

        return $gridData;
    }

    protected function fillResult()
    {
        $this->arResult['PAGE_TITLE_BTN'] = SedTools\Html::getPageTitleButton(\Bitrix\Main\Localization\Loc::getMessage('SED_LIST_COMPONENT.SED_ADD_BTN'), $this->arParams['SEF_FOLDER'] . $this->arParams['URL_TEMPLATES']['add']);

        $this->arResult['GRID_DATA'] = $this->gridDataList;
        if (empty($this->arResult['GRID_DATA'])) {
            $this->arResult['ERRORS'][] = SedTools\Html::getErrorHtml(\Bitrix\Main\Localization\Loc::getMessage('SED_LIST_COMPONENT.EMPTY_CONTRACT_LIST'));
        }

        return $this;
    }
}
