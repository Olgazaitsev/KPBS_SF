<?php namespace Intervolga\Sed\Admin;

use Bitrix\Main\Localization\Loc;
use Intervolga\Sed\Entities\ProcessTaskType;
use Intervolga\Sed\Entities\TaskStatusTransition;

use Intervolga\Sed\Entities\TaskStatusElement as TStatus;
use Intervolga\Sed\Entities\TaskTypeElement as TType;

Loc::loadMessages(__FILE__);

class ListTransition extends SettingsList
{
    /** @var TaskStatusTransition[] $data */
    protected $data;
    /** @var int $processId */
    protected $processId;

    /** @var array $taskStatusOptions */
    protected $taskStatusOptions;
    /** @var array $arTaskStatusInfo */
    protected $arTaskStatusInfo;

    /** @var array $taskTypeOptions */
    protected $taskTypeOptions;
    /** @var TType[] $taskTypeInstances */
    protected $taskTypeInstances;


    protected function prepareParams()
    {
        parent::prepareParams();

        $this->arTaskStatusInfo = array();
        $this->taskStatusOptions = array();

        $this->taskTypeInstances = array();
        $this->taskTypeOptions = array();

        $this->processId = $this->request->getQuery('PROCESS');

        if ($this->processId) {
            $this->params['DETAIL_PAGE_URL'] .= '?PROCESS=' . $this->processId;
            $this->extraFormInputs = array(
                array(
                    'NAME' => 'PROCESS',
                    'VALUE' => $this->processId
                )
            );
        } else {
            $this->errors[] = Loc::getMessage('SED.ADMIN_TRANSITION_LIST.NO_PROCESS_ID');
        }
    }

    protected function getAddBtnLabel()
    {
        return null;
    }

    protected function initFields()
    {
        parent::initFields();

        $this->fields['TASK_TYPE'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_TRANSITION_LIST.TTYPE_FIELD_LABEL'),
            'TYPE' => 'SELECT',
            'OPTIONS' => array(),
            'USED_BY_FILTER' => 'Y'
        );

        $this->fields['SOURCE_STATUS'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_TRANSITION_LIST.SRC_STATUS_FIELD_LABEL'),
            'TYPE' => 'SELECT',
            'OPTIONS' => array(),
            'USED_BY_FILTER' => 'Y'
        );

        $this->fields['DEST_STATUS'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_TRANSITION_LIST.DEST_STATUS_FIELD_LABEL'),
            'TYPE' => 'SELECT',
            'OPTIONS' => array(),
            'USED_BY_FILTER' => 'Y'
        );

        $this->fields['USER_ROLE'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_TRANSITION_LIST.USER_ROLE_FIELD_LABEL'),
            'TYPE' => 'SELECT',
            'OPTIONS' => array(
                TaskStatusTransition::ORIGINATOR_CODE => array(
                    'VALUE' => TaskStatusTransition::ORIGINATOR_CODE,
                    'NAME' => Loc::getMessage('SED.ADMIN_TRANSITION_LIST.USER_ROLE_FIELD_ORIG')
                ),
                TaskStatusTransition::RESPONSIBLE_CODE => array(
                    'VALUE' => TaskStatusTransition::RESPONSIBLE_CODE,
                    'NAME' => Loc::getMessage('SED.ADMIN_TRANSITION_LIST.USER_ROLE_FIELD_RESP')
                )
            ),
            'USED_BY_FILTER' => 'Y'
        );

        $this->fields['NEED_COMMENT'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_TRANSITION_LIST.NEED_COMMENT_FIELD_LABEL'),
            'TYPE' => 'SELECT',
            'OPTIONS' => array(
                0 => array(
                    'VALUE' => '0',
                    'NAME' => Loc::getMessage('SED.ADMIN_TRANSITION_LIST.FIELD_VALUE_FALSE')
                ),
                1 => array(
                    'VALUE' => '1',
                    'NAME' => Loc::getMessage('SED.ADMIN_TRANSITION_LIST.FIELD_VALUE_TRUE')
                )
            ),
            'USED_BY_FILTER' => 'Y'
        );

        $this->fields['BTN_LABEL'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_TRANSITION_LIST.BTN_TEXT_FIELD_LABEL'),
            'TYPE' => 'INPUT',
            'USED_BY_FILTER' => 'Y'
        );

        $this->fields['BTN_SORT'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_TRANSITION_LIST.BTN_SORT'),
            'TYPE' => 'INPUT',
            'USED_BY_FILTER' => 'Y'
        );

        $this->fields['BTN_COLOR'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_TRANSITION_LIST.BTN_COLOR'),
            'TYPE' => 'COLOR',
            'USED_BY_FILTER' => 'N'
        );

        $this->fields['BTN_TEXT_COLOR'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_TRANSITION_LIST.BTN_TEXT_COLOR'),
            'TYPE' => 'COLOR',
            'USED_BY_FILTER' => 'N'
        );

        $this->fields['BTN_HOVER_MODE'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_TRANSITION_LIST.BTN_HOVER_MODE'),
            'TYPE' => 'SELECT',
            'OPTIONS' => array(
                array(
                    'VALUE' => '0',
                    'NAME' => Loc::getMessage('SED.ADMIN_TRANSITION_LIST.FIELD_VALUE_HIGHLIGHT')
                ),
                array(
                    'VALUE' => '1',
                    'NAME' => Loc::getMessage('SED.ADMIN_TRANSITION_LIST.FIELD_VALUE_SHADE')
                )
            ),
            'USED_BY_FILTER' => 'Y'
        );

        $this->fields['TRANSITION_ALLOWED'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_TRANSITION_LIST.TRANSITION_ALLOWED_FIELD_LABEL'),
            'TYPE' => 'SELECT',
            'OPTIONS' => array(
                array(
                    'VALUE' => '0',
                    'NAME' => Loc::getMessage('SED.ADMIN_TRANSITION_LIST.FIELD_VALUE_FALSE')
                ),
                array(
                    'VALUE' => '1',
                    'NAME' => Loc::getMessage('SED.ADMIN_TRANSITION_LIST.FIELD_VALUE_TRUE')
                )
            ),
            'USED_BY_FILTER' => 'Y'
        );
    }

    protected function getDataCountByFilter($filter)
    {
        return TaskStatusTransition::getCountByFilter($filter);
    }

    protected function getData()
    {
        if ($this->processId) {
            /** @var ProcessTaskType[] $processTaskTypes */
            $processTaskTypes = ProcessTaskType::getListByFilter(array('PROCESS_ID' => $this->processId));
            $ttypeIds = ProcessTaskType::getTTypeIdsByEntityList($processTaskTypes);

            if (!empty($ttypeIds)) {
                $filter = empty($this->filter['TASK_TYPE']) ? array_merge($this->filter, array('TASK_TYPE' => $ttypeIds)) : $this->filter;

                $this->initPagination($filter);

                $this->data = TaskStatusTransition::getListByFilter(
                    $filter,
                    array($this->sort['BY'] => $this->sort['ORDER']),
                    array(),
                    $this->pagination['LIMIT'],
                    $this->pagination['OFFSET']
                );

                $this->initStatusData($ttypeIds);
                $this->initOptions();
            }
        } else {
            $this->errors[] = Loc::getMessage('SED.ADMIN_TRANSITION_LIST.NO_PROCESS_ID');
        }
    }

    protected function initStatusData($ttypeIds)
    {
        if (is_array($ttypeIds) && count($ttypeIds)) {
            $this->taskTypeInstances = TType::makeIdsAsArrayKeys(TType::getListByFilter(array('ID' => $ttypeIds)));

            foreach ($this->taskTypeInstances as $ttype) {
                $statuses = TStatus::getListAll($ttype);

                $this->taskTypeOptions[] = array(
                    'VALUE' => (string)$ttype->getId(),
                    'NAME' => $ttype->getName()
                );

                $this->taskStatusOptions[] = array(
                    'VALUE' => (string)$ttype->getId(),
                    'NAME' => Loc::getMessage('SED.ADMIN_TRANSITION_LIST.TTYPE_OPTION', array('#TTYPE_NAME#' => $ttype->getName())),
                    'DISABLED' => 'Y'
                );

                foreach ($statuses as $status) {
                    $this->arTaskStatusInfo[$status->getId()] = array(
                        'STATUS_NAME' => $status->getName(),
                        'TYPE_NAME' => $ttype->getName()
                    );
                    $this->taskStatusOptions[] = array('VALUE' => (string)$status->getId(), 'NAME' => '&nbsp.&nbsp.&nbsp' . $status->getName());
                }
            }
        }
    }

    protected function initOptions()
    {
        $this->fields['TASK_TYPE']['OPTIONS'] = $this->taskTypeOptions;
        $this->fields['SOURCE_STATUS']['OPTIONS'] = $this->taskStatusOptions;
        $this->fields['DEST_STATUS']['OPTIONS'] = $this->taskStatusOptions;
    }

    protected function initTableData()
    {
        $result = array();

        if (!empty($this->data)) {
            foreach ($this->data as $transition) {
                $resultItem = array(
                    'ID' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => $transition->getId()
                    ),
                    'TASK_TYPE' => array(
                        'TYPE' => 'URL',
                        'URL' => $this->params['TASK_TYPE_DETAIL_PAGE_URL'] . '?' . $this->params['TASK_TYPE_DETAIL_PAGE_PARAM'] . '=' . $transition->getTaskType()
                    ),
                    'SOURCE_STATUS' => array(
                        'TYPE' => 'URL',
                        'URL' => $this->params['TASK_STATUS_DETAIL_PAGE_URL'] . '?' . $this->params['TASK_TYPE_DETAIL_PAGE_PARAM'] . '=' . $transition->getTaskType() . '&' . $this->params['TASK_STATUS_DETAIL_PAGE_PARAM'] . '=' . $transition->getSourceStatus()
                    ),
                    'DEST_STATUS' => array(
                        'TYPE' => 'URL',
                        'URL' => $this->params['TASK_STATUS_DETAIL_PAGE_URL'] . '?' . $this->params['TASK_TYPE_DETAIL_PAGE_PARAM'] . '=' . $transition->getTaskType() . '&' . $this->params['TASK_STATUS_DETAIL_PAGE_PARAM'] . '=' . $transition->getDestStatus()
                    ),
                    'USER_ROLE' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => $this->fields['USER_ROLE']['OPTIONS'][$transition->getUserRole()]['NAME']
                    ),
                    'NEED_COMMENT' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => $this->fields['NEED_COMMENT']['OPTIONS'][(int)$transition->isCommentNeeded()]['NAME']
                    ),
                    'BTN_LABEL' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => $transition->getButtonLabel()
                    ),
                    'BTN_SORT' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => $transition->getButtonSort()
                    ),
                    'BTN_COLOR' => array(
                        'TYPE' => 'COLOR',
                        'VALUE' => $transition->getButtonColor()
                    ),
                    'BTN_TEXT_COLOR' => array(
                        'TYPE' => 'COLOR',
                        'VALUE' => $transition->getButtonTextColor()
                    ),
                    'BTN_HOVER_MODE' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => $this->fields['BTN_HOVER_MODE']['OPTIONS'][$transition->getButtonHoverMode()]['NAME']
                    ),
                    'TRANSITION_ALLOWED' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => $this->fields['TRANSITION_ALLOWED']['OPTIONS'][(int)$transition->isTransitionAllowed()]['NAME']
                    )
                );

                $taskType = $this->taskTypeInstances[$transition->getTaskType()];
                $srcStatusInfo = $this->arTaskStatusInfo[$transition->getSourceStatus()];
                $destStatusInfo = $this->arTaskStatusInfo[$transition->getDestStatus()];

                $resultItem['TASK_TYPE']['VALUE'] = ($taskType instanceof TType) ? $taskType->getName() : null;
                $resultItem['SOURCE_STATUS']['VALUE'] = (is_array($srcStatusInfo)) ? $srcStatusInfo['STATUS_NAME'] : null;
                $resultItem['DEST_STATUS']['VALUE'] = (is_array($destStatusInfo)) ? $destStatusInfo['STATUS_NAME'] : null;
                if (strlen($resultItem['BTN_COLOR']['VALUE'])) {
                    $resultItem['BTN_COLOR']['VALUE'] = '#' . $resultItem['BTN_COLOR']['VALUE'];
                }
                if (strlen($resultItem['BTN_TEXT_COLOR']['VALUE'])) {
                    $resultItem['BTN_TEXT_COLOR']['VALUE'] = '#' . $resultItem['BTN_TEXT_COLOR']['VALUE'];
                }

                $result[] = $resultItem;
            }
        }

        return $result;
    }
}