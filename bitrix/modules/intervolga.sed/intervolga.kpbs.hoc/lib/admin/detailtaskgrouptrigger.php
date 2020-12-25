<?php namespace Intervolga\Sed\Admin;

use Bitrix\Main\Localization\Loc;
use Intervolga\Sed\Entities\TaskGroupStatusTrigger;

Loc::loadMessages(__FILE__);

class DetailTaskGroupTrigger extends DetailTaskTrigger
{
    protected static function getTriggerOrmEntity()
    {
        return TaskGroupStatusTrigger::class;
    }

    protected function initFields()
    {
        $this->fields = array(
            'GROUP_ID' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_TASK_GROUP_TRIGGER_DETAIL.FIELDS.GROUP'),
                'EDITABLE' => 'Y',
                'REQUIRED' => 'Y',
                'TYPE' => 'SELECT',
                'OPTIONS' => array()
            ),
            'PROCESS_STATUS_ID' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_TASK_GROUP_TRIGGER_DETAIL.FIELDS.PROCESS_STATUS'),
                'EDITABLE' => 'Y',
                'REQUIRED' => 'Y',
                'TYPE' => 'SELECT',
                'OPTIONS' => array()
            ),
            'ORIGINATOR_ROLE_ID' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_TASK_GROUP_TRIGGER_DETAIL.FIELDS.ORIG'),
                'EDITABLE' => 'Y',
                'TYPE' => 'SELECT',
                'OPTIONS' => array()
            ),
            'ALL_IN_STATUS' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_TASK_GROUP_TRIGGER_DETAIL.FIELDS.ALL_IN'),
                'EDITABLE' => 'Y',
                'TYPE' => 'SELECT',
                'OPTIONS' => array()
            ),
            'ANYONE_IN_STATUS' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_TASK_GROUP_TRIGGER_DETAIL.FIELDS.ANYONE_IN'),
                'EDITABLE' => 'Y',
                'TYPE' => 'SELECT',
                'OPTIONS' => array()
            ),
            'ALL_OUT_OF_STATUSES' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_TASK_GROUP_TRIGGER_DETAIL.FIELDS.ALL_OUT'),
                'EDITABLE' => 'Y',
                'TYPE' => 'SELECT',
                'IS_MULTIPLE' => 'Y',
                'OPTIONS' => array()
            ),
            'ANYONE_OUT_OF_STATUSES' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_TASK_GROUP_TRIGGER_DETAIL.FIELDS.ANYONE_OUT'),
                'EDITABLE' => 'Y',
                'TYPE' => 'SELECT',
                'IS_MULTIPLE' => 'Y',
                'OPTIONS' => array()
            )
        );
    }

    protected function getFieldsDisplayData()
    {
        $result = $this->fields;

        $triggerEntity = static::getTriggerOrmEntity();
        if($this->data instanceof $triggerEntity && $this->data->getId()) {

            $idField = array(
                'ID' => array(
                    'LABEL' => 'ID',
                    'VALUE' => $this->data->getId()
                )
            );

            $result['GROUP_ID']['VALUE'] = $this->data->getGroupId();
            $result['PROCESS_STATUS_ID']['VALUE'] = $this->data->getProcessStatusId();
            $result['ORIGINATOR_ROLE_ID']['VALUE'] = $this->data->getOriginatorRoleId();
            $result['ALL_IN_STATUS']['VALUE'] = $this->data->getAllInStatus();
            $result['ALL_OUT_OF_STATUSES']['VALUE'] = $this->data->getAllOutOfStatuses();
            $result['ANYONE_IN_STATUS']['VALUE'] = $this->data->getAnyOneInStatus();
            $result['ANYONE_OUT_OF_STATUSES']['VALUE'] = $this->data->getAnyOneOutOfStatuses();

            $result = $idField + $result;
        }

        return $result;
    }

    protected function initOptions()
    {
        $defaultOption = array('VALUE' => null, 'NAME' => Loc::getMessage('SED.ADMIN_TASK_TRIGGER_DETAIL.DEFAULT_OPTION'));
        $taskStatusOptionList = array_merge(array($defaultOption), $this->taskStatusOptions);

        $this->fields['ALL_IN_STATUS']['OPTIONS'] = $taskStatusOptionList;
        $this->fields['ANYONE_IN_STATUS']['OPTIONS'] = $taskStatusOptionList;
        $this->fields['ALL_OUT_OF_STATUSES']['OPTIONS'] = $this->taskStatusOptions;
        $this->fields['ANYONE_OUT_OF_STATUSES']['OPTIONS'] = $this->taskStatusOptions;

        $this->fields['ORIGINATOR_ROLE_ID']['OPTIONS'][] = $defaultOption;

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

        foreach ($this->taskGroups as $taskGroup) {
            $this->fields['GROUP_ID']['OPTIONS'][$taskGroup->getId()] = array(
                'VALUE' => $taskGroup->getId(),
                'NAME' => $taskGroup->getName()
            );
        }
    }

    protected function saveOrUpdateTrigger()
    {
        $this->savedDataId = $this->data->setProcessId($this->process->getId())
            ->setGroupId($this->request->getPost('GROUP_ID'))
            ->setProcessStatusId($this->request->getPost('PROCESS_STATUS_ID'))
            ->setOriginatorRoleId($this->request->getPost('ORIGINATOR_ROLE_ID'))
            ->setAllInStatus($this->request->getPost('ALL_IN_STATUS'))
            ->setAllOutOfStatuses($this->request->getPost('ALL_OUT_OF_STATUSES'))
            ->setAnyOneInStatus($this->request->getPost('ANYONE_IN_STATUS'))
            ->setAnyOneOutOfStatuses($this->request->getPost('ANYONE_OUT_OF_STATUSES'))
            ->save();
    }
}