<?php namespace Intervolga\Sed\Admin;

use Bitrix\Main\Localization\Loc;
use Intervolga\Sed\Entities\Process;

use Intervolga\Sed\Entities\ProcessTaskType;
use Intervolga\Sed\Entities\TaskStatusTransition;

use Intervolga\Sed\Entities\TaskStatusElement as TStatus;
use Intervolga\Sed\Entities\TaskTypeElement as TType;

Loc::loadMessages(__FILE__);

class DetailTransition extends SettingsDetail
{
    /** @var Process $process */
    protected $process;
    /** @var TStatus[] $taskStatuses */
    protected $taskStatuses;
    /** @var TType[] $taskTypes */
    protected $taskTypes;


    protected function getListButtonLabel()
    {
        return Loc::getMessage('SED.ADMIN_TRANSITION_DETAIL.LIST_BTN');
    }

    protected function getPageHeader()
    {
        return Loc::getMessage('SED.ADMIN_TRANSITION_DETAIL.PAGE_HEADER');
    }

    protected function prepareParams()
    {
        parent::prepareParams();
        $this->initProcess();
    }

    protected function getDetailEntityPageUrl()
    {
        return $this->params['DETAIL_PAGE_URL'] . '?PROCESS=' . $this->process->getId() . '&TRANSITION=' . $this->data->getId();
    }

    protected function initProcess()
    {
        $processId = $this->request->getQuery('PROCESS');
        if ($processId) {
            try {
                $this->process = Process::getById($processId);
                $this->params['LIST_PAGE_URL'] .= '?PROCESS=' . $processId;
            } catch (\Bitrix\Main\ObjectNotFoundException $e) {
                $this->errors[] = Loc::getMessage('SED.ADMIN_TRANSITION_DETAIL.PROCESS_NOT_FOUND');
            }
        } else {
            $this->errors[] = Loc::getMessage('SED.ADMIN_TRANSITION_DETAIL.EMPTY_PROCESS_PARAM');
        }
    }

    protected function initFields()
    {
        $this->fields = array(
            'TASK_TYPE' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_TRANSITION_DETAIL.FIELDS.TTYPE')
            ),
            'SOURCE_STATUS' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_TRANSITION_DETAIL.FIELDS.SRC_STATUS')
            ),
            'DEST_STATUS' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_TRANSITION_DETAIL.FIELDS.DEST_STATUS')
            ),
            'USER_ROLE' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_TRANSITION_DETAIL.FIELDS.USER_ROLE')
            ),
            'NEED_COMMENT' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_TRANSITION_DETAIL.FIELDS.NEED_COMMENT'),
                'EDITABLE' => 'Y',
                'REQUIRED' => 'Y',
                'TYPE' => 'SELECT',
                'OPTIONS' => array(
                    array(
                        'VALUE' => 0,
                        'NAME' => Loc::getMessage('SED.ADMIN_TRANSITION_DETAIL.NO')
                    ),
                    array(
                        'VALUE' => 1,
                        'NAME' => Loc::getMessage('SED.ADMIN_TRANSITION_DETAIL.YES')
                    )
                )
            ),
            'BTN_LABEL' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_TRANSITION_DETAIL.FIELDS.BTN_LABEL'),
                'EDITABLE' => 'Y',
                'TYPE' => 'INPUT'
            ),
            'BTN_SORT' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_TRANSITION_DETAIL.FIELDS.BTN_SORT'),
                'EDITABLE' => 'Y',
                'TYPE' => 'INPUT'
            ),
            'BTN_COLOR' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_TRANSITION_DETAIL.FIELDS.BTN_COLOR'),
                'EDITABLE' => 'Y',
                'TYPE' => 'COLOR'
            ),
            'BTN_TEXT_COLOR' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_TRANSITION_DETAIL.FIELDS.BTN_TEXT_COLOR'),
                'EDITABLE' => 'Y',
                'TYPE' => 'COLOR'
            ),
            'BTN_HOVER_MODE' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_TRANSITION_DETAIL.FIELDS.BTN_HOVER_MODE'),
                'EDITABLE' => 'Y',
                'TYPE' => 'SELECT',
                'OPTIONS' => array(
                    array(
                        'VALUE' => '0',
                        'NAME' => Loc::getMessage('SED.ADMIN_TRANSITION_DETAIL.FIELDS.FIELD_VALUE_HIGHLIGHT')
                    ),
                    array(
                        'VALUE' => '1',
                        'NAME' => Loc::getMessage('SED.ADMIN_TRANSITION_DETAIL.FIELDS.FIELD_VALUE_SHADE')
                    )
                ),
                'USED_BY_FILTER' => 'Y'
            ),
            'TRANSITION_ALLOWED' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_TRANSITION_DETAIL.FIELDS.TRANSITION_ALLOWED'),
                'EDITABLE' => 'Y',
                'REQUIRED' => 'Y',
                'TYPE' => 'SELECT',
                'OPTIONS' => array(
                    array(
                        'VALUE' => 0,
                        'NAME' => Loc::getMessage('SED.ADMIN_TRANSITION_DETAIL.NO')
                    ),
                    array(
                        'VALUE' => 1,
                        'NAME' => Loc::getMessage('SED.ADMIN_TRANSITION_DETAIL.YES')
                    )
                )
            )
        );
    }

    protected function getFieldsDisplayData()
    {
        $result = $this->fields;

        if ($this->data instanceof TaskStatusTransition && $this->data->getId()) {

            $idField = array(
                'ID' => array(
                    'LABEL' => 'ID',
                    'VALUE' => $this->data->getId()
                )
            );

            if ($this->taskTypes[$this->data->getTaskType()] instanceof TType) {
                $result['TASK_TYPE']['VALUE'] = $this->taskTypes[$this->data->getTaskType()]->getName();
            }
            if ($this->taskStatuses[$this->data->getSourceStatus()] instanceof TStatus) {
                $result['SOURCE_STATUS']['VALUE'] = $this->taskStatuses[$this->data->getSourceStatus()]->getName();
            }
            if ($this->taskStatuses[$this->data->getDestStatus()] instanceof TStatus) {
                $result['DEST_STATUS']['VALUE'] = $this->taskStatuses[$this->data->getDestStatus()]->getName();
            }

            if ($this->data->getUserRole() == TaskStatusTransition::ORIGINATOR_CODE) {
                $result['USER_ROLE']['VALUE'] = Loc::getMessage('SED.ADMIN_TRANSITION_DETAIL.AUTHOR');
            } elseif ($this->data->getUserRole() == TaskStatusTransition::RESPONSIBLE_CODE) {
                $result['USER_ROLE']['VALUE'] = Loc::getMessage('SED.ADMIN_TRANSITION_DETAIL.RESPONSIBLE');
            }

            $result['NEED_COMMENT']['VALUE'] = $this->data->isCommentNeeded();
            $result['BTN_LABEL']['VALUE'] = $this->data->getButtonLabel();
            $result['BTN_SORT']['VALUE'] = $this->data->getButtonSort();
            $result['BTN_COLOR']['VALUE'] = $this->data->getButtonColor();
            if (strlen($result['BTN_COLOR']['VALUE'])) {
                $result['BTN_COLOR']['VALUE'] = '#' . $result['BTN_COLOR']['VALUE'];
            }
            $result['BTN_TEXT_COLOR']['VALUE'] = $this->data->getButtonTextColor();
            if (strlen($result['BTN_TEXT_COLOR']['VALUE'])) {
                $result['BTN_TEXT_COLOR']['VALUE'] = '#' . $result['BTN_TEXT_COLOR']['VALUE'];
            }
            $result['BTN_HOVER_MODE']['VALUE'] = $this->data->getButtonHoverMode();
            $result['TRANSITION_ALLOWED']['VALUE'] = $this->data->isTransitionAllowed();

            $result = $idField + $result;
        }

        return $result;
    }

    protected function getData()
    {
        if ($this->process instanceof Process) {
            $transitionId = $this->request->getQuery('TRANSITION');
            if ($transitionId) {
                /** @var ProcessTaskType[] $processTaskTypes */
                $processTaskTypes = ProcessTaskType::getListByFilter(array('PROCESS_ID' => $this->process->getId()));
                $ttypeIds = ProcessTaskType::getTTypeIdsByEntityList($processTaskTypes);

                try {
                    $this->data = TaskStatusTransition::getOneByFilter(array('ID' => $transitionId, 'TASK_TYPE' => $ttypeIds));
                    $this->initStatusData($ttypeIds);
                } catch (\Bitrix\Main\ObjectNotFoundException $e) {
                    $this->errors[] = Loc::getMessage('SED.ADMIN_TRANSITION_DETAIL.TRANSITION_NOT_FOUND');
                }
            } else {
                $this->errors[] = Loc::getMessage('SED.ADMIN_TRANSITION_DETAIL.EMPTY_TRANSITION_PARAM');
            }
        }
    }

    protected function initStatusData($ttypeIds)
    {
        if (is_array($ttypeIds) && count($ttypeIds)) {
            $this->taskTypes = TType::makeIdsAsArrayKeys(TType::getListByFilter(array('ID' => $ttypeIds)));

            foreach ($this->taskTypes as $ttype) {
                $statuses = TStatus::getListAll($ttype);

                foreach ($statuses as $status) {
                    $this->taskStatuses[$status->getId()] = $status;
                }
            }
        }
    }

    protected function saveOrUpdate()
    {
        if ($this->data instanceof TaskStatusTransition) {
            $transitionBeforeSave = clone $this->data;
            try {
                $this->data->setCommentIsNeeded((bool)$this->request->getPost('NEED_COMMENT'));
                $this->data->setButtonLabel($this->request->getPost('BTN_LABEL'));
                $this->data->setButtonSort($this->request->getPost('BTN_SORT'));
                $this->data->setButtonColor(ltrim($this->request->getPost('BTN_COLOR'), '#'));
                $this->data->setButtonTextColor(ltrim($this->request->getPost('BTN_TEXT_COLOR'), '#'));
                $this->data->setButtonHoverMode($this->request->getPost('BTN_HOVER_MODE'));
                $this->data->setTransitionAllowed((bool)$this->request->getPost('TRANSITION_ALLOWED'));
                $this->data->save();
                $this->elementUpdated = true;
            } catch (\Exception $e) {
                $this->data = $transitionBeforeSave;
                $this->errors[] = Loc::getMessage('SED.ADMIN_TRANSITION_DETAIL.SAVE_ERROR', array('#ERROR_DESC#' => $e->getMessage()));
            }
        }
    }
}