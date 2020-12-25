<?php namespace Intervolga\Sed\Admin;

use Bitrix\Main\Localization\Loc;
use Intervolga\Sed\Entities\ProcessTaskGroup;
use Intervolga\Sed\Entities\TaskGroupStatusTrigger;
use Intervolga\Sed\Entities\ParticipantRole;
use Intervolga\Sed\Entities\ProcessStatus;
use Intervolga\Sed\Entities\ProcessTaskType;

use Intervolga\Sed\Entities\TaskStatusElement as TStatus;
use Intervolga\Sed\Entities\TaskTypeElement as TType;

Loc::loadMessages(__FILE__);

class ListTaskGroupTrigger extends SettingsList
{
    /** @var TaskGroupStatusTrigger[] data */
    protected $data;
    /** @var ParticipantRole[] $processRoles */
    protected $processRoles;
    /** @var ProcessStatus[] $processStatuses */
    protected $processStatuses;
    /** @var ProcessTaskGroup[] $processTaskGroups */
    protected $processTaskGroups;
    /** @var int $processId */
    protected $processId;

    /** @var array $statuses */
    protected $taskStatusOptions;
    /** @var TStatus[] $taskStatusInstances */
    protected $taskStatusInstances;


    protected function prepareParams()
    {
        parent::prepareParams();
        $this->initProcessId();
    }

    protected function initProcessId()
    {
        $this->taskStatusInstances = array();
        $this->taskStatusOptions = array();
        $this->processRoles = array();
        $this->processStatuses = array();
        $this->processTaskGroups = array();

        $this->processId = $this->request->getQuery('PROCESS');

        if($this->processId) {
            $this->params['DETAIL_PAGE_URL'] .= '?PROCESS=' . $this->request->getQuery('PROCESS');
            $this->extraFormInputs = array(
                array(
                    'NAME' => 'PROCESS',
                    'VALUE' => $this->request->getQuery('PROCESS')
                )
            );
        }
        else {
            $this->errors[] = Loc::getMessage('SED.ADMIN_TASK_GROUP_TRIGGER_LIST.NO_PROCESS_ID');
        }
    }

    protected function getAddBtnLabel()
    {
        return Loc::getMessage('SED.ADMIN_TASK_GROUP_TRIGGER_LIST.ADD_BTN_LABEL');
    }

    protected function initFields()
    {
        parent::initFields();

        $this->fields['GROUP_ID'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_TASK_GROUP_TRIGGER_LIST.GROUP_FIELD_LABEL'),
            'TYPE' => 'SELECT',
            'OPTIONS' => array(),
            'USED_BY_FILTER' => 'Y'
        );

        $this->fields['ORIGINATOR_ROLE_ID'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_TASK_GROUP_TRIGGER_LIST.ORIG_FIELD_LABEL'),
            'TYPE' => 'SELECT',
            'OPTIONS' => array(),
            'USED_BY_FILTER' => 'Y'
        );

        $this->fields['PROCESS_STATUS_ID'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_TASK_GROUP_TRIGGER_LIST.PROCESS_STATUS_FIELD_LABEL'),
            'TYPE' => 'SELECT',
            'OPTIONS' => array(),
            'USED_BY_FILTER' => 'Y'
        );

        $this->fields['ALL_IN_STATUS'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_TASK_GROUP_TRIGGER_LIST.ALL_IN_STATUS_FIELD_LABEL'),
            'TYPE' => 'SELECT',
            'OPTIONS' => array(),
//            'USED_BY_FILTER' => 'Y'
        );

        $this->fields['ALL_OUT_OF_STATUSES'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_TASK_GROUP_TRIGGER_LIST.ALL_OUT_OF_STATUSES_FIELD_LABEL'),
            'TYPE' => 'SELECT',
            'MULTIPLE' => 'Y',
            'OPTIONS' => array(),
//            'USED_BY_FILTER' => 'Y'
        );

        $this->fields['ANYONE_IN_STATUS'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_TASK_GROUP_TRIGGER_LIST.ANYONE_IN_STATUS_FIELD_LABEL'),
            'TYPE' => 'SELECT',
            'OPTIONS' => array(),
//            'USED_BY_FILTER' => 'Y'
        );

        $this->fields['ANYONE_OUT_OF_STATUSES'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_TASK_GROUP_TRIGGER_LIST.ANYONE_OUT_OF_STATUSES_FIELD_LABEL'),
            'TYPE' => 'SELECT',
            'MULTIPLE' => 'Y',
            'OPTIONS' => array(),
//            'USED_BY_FILTER' => 'Y'
        );
    }

    protected function getDataCountByFilter($filter)
    {
        return TaskGroupStatusTrigger::getCountByFilter($filter);
    }

    protected function getData()
    {
        if($this->processId) {
            $filter = array_merge($this->filter, array('PROCESS_ID' => $this->processId));

            $this->initPagination($filter);

            $this->data = TaskGroupStatusTrigger::getListByFilter(
                $filter,
                array($this->sort['BY'] => $this->sort['ORDER']),
                array(),
                $this->pagination['LIMIT'],
                $this->pagination['OFFSET']
            );

            $this->processRoles = ParticipantRole::getListByFilter(array('PROCESS_ID' => $this->processId));
            $this->processStatuses = ProcessStatus::getListByFilter(array('PROCESS_ID' => $this->processId));
            $this->processTaskGroups = ProcessTaskGroup::getListByFilter(array('PROCESS_ID' => $this->processId));

            $this->initStatusData($this->processId);
            $this->initOptions();
        }
    }

    protected function initOptions()
    {
        foreach ($this->processRoles as $role) {
            $this->fields['ORIGINATOR_ROLE_ID']['OPTIONS'][$role->getId()] = array(
                'VALUE' => $role->getId(),
                'NAME' => $role->getName()
            );
        }

        foreach ($this->processStatuses as $status) {
            $this->fields['PROCESS_STATUS_ID']['OPTIONS'][$status->getId()] = array(
                'VALUE' => $status->getId(),
                'NAME' => $status->getName()
            );
        }

        foreach ($this->processTaskGroups as $group) {
            $this->fields['GROUP_ID']['OPTIONS'][$group->getId()] = array(
                'VALUE' => $group->getId(),
                'NAME' => $group->getName()
            );
        }

        $this->fields['ALL_IN_STATUS']['OPTIONS'] = $this->taskStatusOptions;
        $this->fields['ALL_OUT_OF_STATUSES']['OPTIONS'] = $this->taskStatusOptions;
        $this->fields['ANYONE_IN_STATUS']['OPTIONS'] = $this->taskStatusOptions;
        $this->fields['ANYONE_OUT_OF_STATUSES']['OPTIONS'] = $this->taskStatusOptions;
    }

    protected function initStatusData($processId)
    {
        /** @var ProcessTaskType[] $processTTypes */
        $processTTypes = ProcessTaskType::getListByFilter(array('PROCESS_ID' => $processId));
        $ttypeIds = ProcessTaskType::getTTypeIdsByEntityList($processTTypes);

        if(count($ttypeIds)) {
            $ttypes = TType::getListByFilter(array('ID' => $ttypeIds));

            foreach ($ttypes as $ttype) {
                $statuses = TStatus::getListAll($ttype);

                $this->taskStatusOptions[] = array(
                    'VALUE' => $ttype->getId(),
                    'NAME' => Loc::getMessage('SED.ADMIN_TASK_GROUP_TRIGGER_LIST.TTYPE_OPTION', array('#TTYPE_NAME#' => $ttype->getName())),
                    'DISABLED' => 'Y'
                );

                foreach ($statuses as $status) {
//                    $this->taskStatusInstances[$status->getId()] = $status;
                    $this->taskStatusInstances[$status->getId()] = array(
                        'STATUS_NAME' => $status->getName(),
                        'TYPE_NAME' => $ttype->getName()
                    );
                    $this->taskStatusOptions[] = array('VALUE' => $status->getId(), 'NAME' => '&nbsp.&nbsp.&nbsp' . $status->getName());
                }
            }
        }
    }

    protected function initTableData()
    {
        $result = array();

        if(!empty($this->data)) {
            foreach ($this->data as $trigger) {

                $resultItem = array(
                    'ID' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => $trigger->getId()
                    ),
                    'GROUP_ID' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => $this->fields['GROUP_ID']['OPTIONS'][$trigger->getGroupId()]['NAME']
                    ),
                    'ORIGINATOR_ROLE_ID' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => $this->fields['ORIGINATOR_ROLE_ID']['OPTIONS'][$trigger->getOriginatorRoleId()]['NAME']
                    ),
                    'PROCESS_STATUS_ID' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => $this->fields['PROCESS_STATUS_ID']['OPTIONS'][$trigger->getProcessStatusId()]['NAME']
                    ),
                    'ALL_IN_STATUS' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => static::taskStatusNameFormat($this->taskStatusInstances[$trigger->getAllInStatus()])
                    ),
                    'ALL_OUT_OF_STATUSES' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => $this->taskStatusListNameFormat($trigger->getAllOutOfStatuses())
                    ),
                    'ANYONE_IN_STATUS' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => static::taskStatusNameFormat($this->taskStatusInstances[$trigger->getAnyOneInStatus()])
                    ),
                    'ANYONE_OUT_OF_STATUSES' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => $this->taskStatusListNameFormat($trigger->getAnyOneOutOfStatuses())
                    ),
                );

                $result[] = $resultItem;
            }
        }

        return $result;
    }

    protected function taskStatusListNameFormat($statusIds)
    {
        $result = '';

        if(is_array($statusIds) && count($statusIds)) {
            $typeName = '';
            $statusNames = array();

            foreach ($statusIds as $statusId) {
                $statusInfo = $this->taskStatusInstances[$statusId];
                if(is_array($statusInfo) && strlen($statusInfo['TYPE_NAME']) && strlen($statusInfo['STATUS_NAME'])) {
                    $typeName = $statusInfo['TYPE_NAME'];
                    $statusNames[] = $statusInfo['STATUS_NAME'];
                }
            }

            $result = Loc::getMessage('SED.ADMIN_TASK_GROUP_TRIGGER_LIST.TASK_STATUSES_NAME_FORMAT', array(
                '#TYPE#' => $typeName,
                '#STATUS#' => '[' . implode(', ', $statusNames) . ']',
            ));
        }

        return $result;
    }

    protected static function taskStatusNameFormat($statusInfo)
    {
        if(is_array($statusInfo) && strlen($statusInfo['TYPE_NAME']) && strlen($statusInfo['STATUS_NAME'])) {
            return Loc::getMessage('SED.ADMIN_TASK_GROUP_TRIGGER_LIST.TASK_STATUS_NAME_FORMAT', array(
                '#TYPE#' => $statusInfo['TYPE_NAME'],
                '#STATUS#' => $statusInfo['STATUS_NAME'],
            ));
        }
        else {
            return '';
        }
    }
}