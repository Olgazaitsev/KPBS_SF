<?php namespace Intervolga\Sed\Admin;

use Bitrix\Main\Localization\Loc;
use Intervolga\Sed\Entities\TaskStatusTrigger;
use Intervolga\Sed\Entities\ParticipantRole;
use Intervolga\Sed\Entities\ProcessStatus;
use Intervolga\Sed\Entities\ProcessTaskType;

use Intervolga\Sed\Entities\TaskStatusElement as TStatus;
use Intervolga\Sed\Entities\TaskTypeElement as TType;

Loc::loadMessages(__FILE__);

class ListTaskTrigger extends SettingsList
{
    /** @var TaskStatusTrigger[] data */
    protected $data;
    /** @var ParticipantRole[] $processRoles */
    protected $processRoles;
    /** @var ProcessStatus[] $processStatuses */
    protected $processStatuses;
    /** @var int $processId */
    protected $processId;

    /** @var array $taskStatusOptions */
    protected $taskStatusOptions;
    /** @var array $arTaskStatusInfo */
    protected $arTaskStatusInfo;


    protected function prepareParams()
    {
        parent::prepareParams();
        $this->initProcessId();
    }

    protected function initProcessId()
    {
        $this->arTaskStatusInfo = array();
        $this->taskStatusOptions = array();
        $this->processRoles = array();
        $this->processStatuses = array();

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
            $this->errors[] = Loc::getMessage('SED.ADMIN_TASK_TRIGGER_LIST.NO_PROCESS_ID');
        }
    }

    protected function getAddBtnLabel()
    {
        return Loc::getMessage('SED.ADMIN_TASK_TRIGGER_LIST.ADD_BTN_LABEL');
    }

    protected function initFields()
    {
        parent::initFields();

        $this->fields['RESPONSIBLE_ROLE_ID'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_TASK_TRIGGER_LIST.RESP_FIELD_LABEL'),
            'TYPE' => 'SELECT',
            'OPTIONS' => array(),
            'USED_BY_FILTER' => 'Y'
        );

        $this->fields['ORIGINATOR_ROLE_ID'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_TASK_TRIGGER_LIST.ORIG_FIELD_LABEL'),
            'TYPE' => 'SELECT',
            'OPTIONS' => array(),
            'USED_BY_FILTER' => 'Y'
        );

        $this->fields['PROCESS_STATUS_ID'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_TASK_TRIGGER_LIST.PROCESS_STATUS_FIELD_LABEL'),
            'TYPE' => 'SELECT',
            'OPTIONS' => array(),
            'USED_BY_FILTER' => 'Y'
        );

        $this->fields['OLD_UF_STATUS_ID'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_TASK_TRIGGER_LIST.OLD_TASK_STATUS_FIELD_LABEL'),
            'TYPE' => 'SELECT',
            'OPTIONS' => array(),
            'USED_BY_FILTER' => 'Y'
        );

        $this->fields['NEW_UF_STATUS_ID'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_TASK_TRIGGER_LIST.NEW_TASK_STATUS_FIELD_LABEL'),
            'TYPE' => 'SELECT',
            'OPTIONS' => array(),
            'USED_BY_FILTER' => 'Y'
        );
    }

    protected function getDataCountByFilter($filter)
    {
        return TaskStatusTrigger::getCountByFilter($filter);
    }

    protected function getData()
    {
        if($this->processId) {
            $filter = array_merge($this->filter, array('PROCESS_ID' => $this->processId));

            $this->initPagination($filter);

            $this->data = TaskStatusTrigger::getListByFilter(
                $filter,
                array($this->sort['BY'] => $this->sort['ORDER']),
                array(),
                $this->pagination['LIMIT'],
                $this->pagination['OFFSET']
            );

            $this->processRoles = ParticipantRole::getListByFilter(array('PROCESS_ID' => $this->processId));
            $this->processStatuses = ProcessStatus::getListByFilter(array('PROCESS_ID' => $this->processId));

            $this->initStatusData($this->processId);
            $this->initOptions();
        }
    }

    protected function initOptions()
    {
        foreach ($this->processRoles as $role) {
            $this->fields['RESPONSIBLE_ROLE_ID']['OPTIONS'][$role->getId()] = array(
                'VALUE' => $role->getId(),
                'NAME' => $role->getName()
            );
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

        $this->fields['OLD_UF_STATUS_ID']['OPTIONS'] = $this->taskStatusOptions;
        $this->fields['NEW_UF_STATUS_ID']['OPTIONS'] = $this->taskStatusOptions;
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
                    'NAME' => Loc::getMessage('SED.ADMIN_TASK_TRIGGER_LIST.TTYPE_OPTION', array('#TTYPE_NAME#' => $ttype->getName())),
                    'DISABLED' => 'Y'
                );

                foreach ($statuses as $status) {
//                    $this->arTaskStatusInfo[$status->getId()] = $status;
                    $this->arTaskStatusInfo[$status->getId()] = array(
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
                    'RESPONSIBLE_ROLE_ID' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => $this->fields['RESPONSIBLE_ROLE_ID']['OPTIONS'][$trigger->getResponsibleRoleId()]['NAME']
                    ),
                    'ORIGINATOR_ROLE_ID' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => $this->fields['ORIGINATOR_ROLE_ID']['OPTIONS'][$trigger->getOriginatorRoleId()]['NAME']
                    ),
                    'PROCESS_STATUS_ID' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => $this->fields['PROCESS_STATUS_ID']['OPTIONS'][$trigger->getProcessStatusId()]['NAME']
                    ),
                    'OLD_UF_STATUS_ID' => array(
                        'TYPE' => 'TEXT',
//                        'VALUE' => $trigger->getOldUfStatusId(),
                    ),
                    'NEW_UF_STATUS_ID' => array(
                        'TYPE' => 'TEXT',
//                        'VALUE' => $trigger->getNewUfStatusId(),
                    )
                );

                $tmpStatus = $this->arTaskStatusInfo[$trigger->getOldUfStatusId()];
                $resultItem['OLD_UF_STATUS_ID']['VALUE'] = static::taskStatusNameFormat($tmpStatus);

                $tmpStatus = $this->arTaskStatusInfo[$trigger->getNewUfStatusId()];
                $resultItem['NEW_UF_STATUS_ID']['VALUE'] = static::taskStatusNameFormat($tmpStatus);


                $result[] = $resultItem;
            }
        }

        return $result;
    }

    protected static function taskStatusNameFormat($statusInfo)
    {
        if(is_array($statusInfo) && strlen($statusInfo['TYPE_NAME']) && strlen($statusInfo['STATUS_NAME'])) {
            return Loc::getMessage('SED.ADMIN_TASK_TRIGGER_LIST.TASK_STATUS_NAME_FORMAT', array(
                '#TYPE#' => $statusInfo['TYPE_NAME'],
                '#STATUS#' => $statusInfo['STATUS_NAME'],
            ));
        }
        else {
            return '';
        }
    }
}