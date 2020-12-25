<?php namespace Intervolga\Sed\Admin;

use Bitrix\Main\Localization\Loc;
use Intervolga\Sed\Entities\Process;
use Intervolga\Sed\Entities\ProcessTaskGroup;
use Intervolga\Sed\Entities\TaskStatusTrigger;
use Intervolga\Sed\Entities\ParticipantRole;
use Intervolga\Sed\Entities\ProcessStatus;
use Intervolga\Sed\Entities\ProcessTaskType;
use Intervolga\Sed\Entities\ActionParam;
use Intervolga\Sed\Entities\TriggerEffect;

use Intervolga\Sed\Entities\TaskStatusElement as TStatus;
use Intervolga\Sed\Entities\TaskTypeElement as TType;

Loc::loadMessages(__FILE__);

class DetailTaskTrigger extends SettingsDetail
{
    const PARAM_INPUT_PREFIX = 'param_';
    const ACTION_INPUT_PREFIX = 'action_';


    protected $savedDataId;
    /** @var Process $process */
    protected $process;

    /** @var ParticipantRole[] $processRoles */
    protected $processRoles;
    /** @var ProcessStatus[] $processStatuses */
    protected $processStatuses;
    /** @var TType[] $taskTypes */
    protected $taskTypes;
    /** @var ProcessTaskGroup[] $taskGroups */
    protected $taskGroups;

    /** @var array $statuses */
    protected $taskStatusOptions;
    /** @var TStatus[] $taskStatusInstances */
    protected $taskStatusInstances;

    /** @var array $actions */
    protected $actions;
    /** @var array $actions */
    protected $actionParamsOptions;
    /** @var array $paramType */
    protected $paramType;
    /** @var array $triggerEffects */
    protected $triggerEffects;



    protected function prepareParams()
    {
        parent::prepareParams();

        $this->processRoles = array();
        $this->processStatuses = array();
        $this->taskTypes = array();
        $this->taskGroups = array();

        $this->actions = array();
        $this->triggerEffects = array();

        $this->initProcess();
    }

    /**
     * @return \Intervolga\Sed\Entities\AbstractTrigger
     */
    protected static function getTriggerOrmEntity()
    {
        return TaskStatusTrigger::class;
    }

    public function getActions()
    {
        return $this->actions;
    }

    public function getSelectedActions()
    {
        $result = array();

        if(count($this->triggerEffects)) {
            foreach ($this->triggerEffects as $actionId => $action) {
                if(!is_array($result[$actionId])) {
                    $result[$actionId] = array();
                }

                /** @var TriggerEffect $effect */
                foreach ($action as $paramId => $effect) {
                    $result[$actionId][$paramId] = $effect->getParamValue();
                }
            }
        }

        return $result;
    }

    /*
     * Инициализирует массив из вариантов значений для параметров различного типа
     */
    public function initActionParamOptions()
    {
        $this->actionParamsOptions = array(
            ActionParam::PARAM_TYPE_ROLE => array(),
            ActionParam::PARAM_TYPE_TASK_TYPE => array(),
            ActionParam::PARAM_TYPE_PROCESS_STATUS => array(),
            ActionParam::PARAM_TYPE_TASK_GROUP => array()
        );

        if(count($this->processRoles)) {
            foreach ($this->processRoles as $role) {
                $this->actionParamsOptions[ActionParam::PARAM_TYPE_ROLE][$role->getId()] = $role->getName();
            }
        }

        if(count($this->taskTypes)) {
            foreach ($this->taskTypes as $taskType) {
                $this->actionParamsOptions[ActionParam::PARAM_TYPE_TASK_TYPE][$taskType->getId()] = $taskType->getName();
            }
        }

        if(count($this->processStatuses)) {
            foreach ($this->processStatuses as $status) {
                $this->actionParamsOptions[ActionParam::PARAM_TYPE_PROCESS_STATUS][$status->getId()] = $status->getName();
            }
        }

        if(count($this->taskGroups)) {
            foreach ($this->taskGroups as $group) {
                $this->actionParamsOptions[ActionParam::PARAM_TYPE_TASK_GROUP][$group->getId()] = $group->getName();
            }
        }
    }

    protected function getListButtonLabel()
    {
        return Loc::getMessage('SED.ADMIN_TASK_TRIGGER_DETAIL.LIST_BTN');
    }

    protected function getPageHeader()
    {
        return Loc::getMessage('SED.ADMIN_TASK_TRIGGER_DETAIL.PAGE_HEADER');
    }

    protected function getDetailEntityPageUrl()
    {
        return $this->params['DETAIL_PAGE_URL'] . '?PROCESS=' . $this->process->getId() . '&' . $this->params['UNIQUE_ENTITY_CODE'] . '=' . $this->savedDataId;
    }

    protected function initProcess()
    {
        $processId = $this->request->getQuery('PROCESS');
        if($processId) {
            try {
                $this->process = Process::getById($processId);
                $this->params['LIST_PAGE_URL'] .= '?PROCESS=' . $processId;
            }
            catch (\Bitrix\Main\ObjectNotFoundException $e) {
                $this->errors[] = Loc::getMessage('SED.ADMIN_TASK_TRIGGER_DETAIL.PROCESS_NOT_FOUND');
            }
        }
        else {
            $this->errors[] = Loc::getMessage('SED.ADMIN_TASK_TRIGGER_DETAIL.EMPTY_PROCESS_PARAM');
        }
    }

    protected function initFields()
    {
        $this->fields = array(
            'RESPONSIBLE_ROLE_ID' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_TASK_TRIGGER_DETAIL.FIELDS.RESP'),
                'EDITABLE' => 'Y',
                'REQUIRED' => 'Y',
                'TYPE' => 'SELECT',
                'OPTIONS' => array()
            ),
            'ORIGINATOR_ROLE_ID' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_TASK_TRIGGER_DETAIL.FIELDS.ORIG'),
                'EDITABLE' => 'Y',
                'TYPE' => 'SELECT',
                'OPTIONS' => array()
            ),
            'PROCESS_STATUS_ID' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_TASK_TRIGGER_DETAIL.FIELDS.PROCESS_STATUS'),
                'EDITABLE' => 'Y',
                'TYPE' => 'SELECT',
                'OPTIONS' => array()
            ),
            'OLD_UF_STATUS_ID' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_TASK_TRIGGER_DETAIL.FIELDS.OLD_TASK_STATUS'),
                'EDITABLE' => 'Y',
                'TYPE' => 'SELECT',
                'OPTIONS' => array()
            ),
            'NEW_UF_STATUS_ID' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_TASK_TRIGGER_DETAIL.FIELDS.NEW_TASK_STATUS'),
                'EDITABLE' => 'Y',
                'TYPE' => 'SELECT',
                'OPTIONS' => array()
            )
        );
    }

    protected function initActions()
    {
        $actionParams = ActionParam::getListByFilter(array(), array(), array('ACTION.NAME'));
//        $actionParams = ActionParam::getListByFilter(array('!ACTION.CODE' => 'UpdateTask'), array(), array('ACTION.NAME'));

        if(count($actionParams)) {
            foreach ($actionParams as $param) {
                if(!is_array($this->actions[$param->getActionId()])) {
                    $this->actions[$param->getActionId()] = array(
                        'LABEL' => $param->getReferenceActionName(),
                        'PARAMS' => array()
                    );
                }

                $this->actions[$param->getActionId()]['PARAMS'][$param->getId()] = array(
                    'LABEL' => $param->getName(),
                    'TYPE' => $param->getType(),
                    'IS_REQUIRED' => $param->isRequired(),
                    'IS_MULTIPLE' => $param->isMultiple(),
                    'HAS_OPTIONS' => $param->hasOptions(),
                    'OPTIONS' => $this->actionParamsOptions[$param->getType()]
                );
            }
        }
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

            $result['RESPONSIBLE_ROLE_ID']['VALUE'] = $this->data->getResponsibleRoleId();
            $result['ORIGINATOR_ROLE_ID']['VALUE'] = $this->data->getOriginatorRoleId();
            $result['PROCESS_STATUS_ID']['VALUE'] = $this->data->getProcessStatusId();
            $result['OLD_UF_STATUS_ID']['VALUE'] = $this->data->getOldUfStatusId();
            $result['NEW_UF_STATUS_ID']['VALUE'] = $this->data->getNewUfStatusId();

            $result = $idField + $result;
        }

        return $result;
    }

    protected function initStatusData()
    {
        if(count($this->taskTypes)) {
            foreach ($this->taskTypes as $taskType) {
                $statuses = TStatus::getListAll($taskType);

                $this->taskStatusOptions[] = array(
                    'VALUE' => $taskType->getId(),
                    'NAME' => Loc::getMessage('SED.ADMIN_TASK_TRIGGER_DETAIL.TTYPE_OPTION', array('#TTYPE_NAME#' => $taskType->getName())),
                    'DISABLED' => 'Y'
                );

                foreach ($statuses as $status) {
                    $this->taskStatusInstances[$status->getId()] = $status;
                    $this->taskStatusOptions[] = array('VALUE' => $status->getId(), 'NAME' => '&nbsp.&nbsp.&nbsp' . $status->getName());
                }
            }
        }
    }

    protected function initOptions()
    {
        $defaultOption = array('VALUE' => null, 'NAME' => Loc::getMessage('SED.ADMIN_TASK_TRIGGER_DETAIL.DEFAULT_OPTION'));

        $this->fields['OLD_UF_STATUS_ID']['OPTIONS'] = array_merge(array($defaultOption), $this->taskStatusOptions);
        $this->fields['NEW_UF_STATUS_ID']['OPTIONS'] = array_merge(array($defaultOption), $this->taskStatusOptions);

        $this->fields['ORIGINATOR_ROLE_ID']['OPTIONS'][] = $defaultOption;
        $this->fields['PROCESS_STATUS_ID']['OPTIONS'][] = $defaultOption;

//        $this->fields['OLD_UF_STATUS_ID']['OPTIONS'] = $this->taskStatusOptions;
//        $this->fields['NEW_UF_STATUS_ID']['OPTIONS'] = $this->taskStatusOptions;

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
    }

    protected function getData()
    {
        if($this->process instanceof Process && $this->process->getId()) {

            $this->processRoles = ParticipantRole::getListByFilter(array('PROCESS_ID' => $this->process->getId()));
            $this->processStatuses = ProcessStatus::getListByFilter(array('PROCESS_ID' => $this->process->getId()));
            $this->taskGroups = ProcessTaskGroup::getListByFilter(array('PROCESS_ID' => $this->process->getId()));

            /** @var ProcessTaskType[] $processTTypes */
            $processTTypes = ProcessTaskType::getListByFilter(array('PROCESS_ID' => $this->process->getId()));
            $ttypeIds = ProcessTaskType::getTTypeIdsByEntityList($processTTypes);

            if(count($ttypeIds)) {
                $this->taskTypes = TType::getListByFilter(array('ID' => $ttypeIds));
            }

            $this->initStatusData();
            $this->initOptions();
            $this->initActionParamOptions();
            $this->initActions();
            $this->initTrigger();
        }
    }

    protected function initTrigger()
    {
        $taskTriggerId = $this->request->getQuery($this->params['UNIQUE_ENTITY_CODE']);
        if($taskTriggerId) {
            try {
                $triggerEntity = static::getTriggerOrmEntity();
                $this->data = $triggerEntity::getOneByFilter(array('ID' => $taskTriggerId, 'PROCESS_ID' =>  $this->process->getId()));
                $this->initTriggerEffects();
            }
            catch (\Bitrix\Main\ObjectNotFoundException $e) {
                $this->errors[] = Loc::getMessage('SED.ADMIN_TASK_TRIGGER_DETAIL.TASK_TRIGGER_NOT_FOUND');
            }
        }
    }

    protected function initTriggerEffects()
    {
        $triggerOrmEntity = static::getTriggerOrmEntity();
        $effects = TriggerEffect::getListByFilter(array(
            'TRIGGER_ID' => $this->data->getId(),
            'TRIGGER_TYPE' => $triggerOrmEntity::getType()
        ));

        if(count($effects)) {
            foreach ($effects as $effect) {
                if(!is_array($this->triggerEffects[$effect->getActionId()])) {
                    $this->triggerEffects[$effect->getActionId()] = array();
                }
                $this->triggerEffects[$effect->getActionId()][$effect->getParamId()] = $effect;
            }
        }
    }

    protected function checkRequest()
    {
        if($this->request->isPost() && check_bitrix_sessid()) {
            $this->checkRequiredFields();
            $this->checkActionsRequest();

            if(!count($this->errors) && !count($this->notifications)) {
                $this->saveOrUpdate();
            }
        }
    }

    protected function checkActionsRequest()
    {
        $postList = $this->request->getPostList();

        foreach($this->actions as $actionId => $action) {
            if(isset($postList[static::ACTION_INPUT_PREFIX . $actionId])) {
                foreach ($action['PARAMS'] as $paramId => $param) {
                    if(!!$param['IS_REQUIRED'] && empty($postList[static::PARAM_INPUT_PREFIX . $paramId])) {
                        $this->notifications[] = \Bitrix\Main\Localization\Loc::getMessage('SED.ADMIN_TASK_TRIGGER_DETAIL.REQUIRED_ACTION_PARAM', array(
                            '#PARAM#' => $param['LABEL'],
                            '#ACTION#' => $action['LABEL'],
                        ));
                    }
                }
            }
        }
    }

    protected function saveOrUpdate()
    {
        $triggerEntity = static::getTriggerOrmEntity();
        if (!($this->data instanceof $triggerEntity)) {
            $this->data = $triggerEntity::createEmpty();
        }

        $roleBeforeSave = clone $this->data;

        try {
            $this->saveOrUpdateTrigger();
            $this->saveOrUpdateEffects();

            if (!($roleBeforeSave->getId())) {
                $this->elementCreated = true;
            } else {
                $this->elementUpdated = true;
            }
        } catch (\Exception $e) {
            $this->data = $roleBeforeSave;
            $this->errors[] = Loc::getMessage('SED.ADMIN_TASK_TRIGGER_DETAIL.SAVE_ERROR', array('#ERROR_DESC#' => $e->getMessage()));
        }
    }

    protected function saveOrUpdateTrigger()
    {
        $this->savedDataId = $this->data->setProcessId($this->process->getId())
            ->setResponsibleRoleId($this->request->getPost('RESPONSIBLE_ROLE_ID'))
            ->setOriginatorRoleId($this->request->getPost('ORIGINATOR_ROLE_ID'))
            ->setProcessStatusId($this->request->getPost('PROCESS_STATUS_ID'))
            ->setOldUfStatusId($this->request->getPost('OLD_UF_STATUS_ID'))
            ->setNewUfStatusId($this->request->getPost('NEW_UF_STATUS_ID'))
            ->save();
    }

    protected function saveOrUpdateEffects()
    {
        $postList = $this->request->getPostList();
        $triggerOrmEntity = static::getTriggerOrmEntity();

        foreach ($this->actions as $actionId => $action) {
            if(isset($postList[static::ACTION_INPUT_PREFIX . $actionId])) {
                // Обновляем или создаем элементы (необязательные параметры могут отсутствовать). Наличие обязательных параметров проверено ранее
                /** @var TriggerEffect[] $this->triggerEffects[$actionId] */
                foreach ($action['PARAMS'] as $paramId => $param) {

                    if(!isset($this->triggerEffects[$actionId][$paramId])) {
                        $this->triggerEffects[$actionId][$paramId] = TriggerEffect::createEmpty($triggerOrmEntity::getType())
                            ->setTriggerId($this->savedDataId)
                            ->setActionId($actionId)
                            ->setParamId($paramId);
                    }

                    $this->triggerEffects[$actionId][$paramId]->setParamValue($postList[static::PARAM_INPUT_PREFIX . $paramId]);
                    $this->triggerEffects[$actionId][$paramId]->save();

                }
            }
            elseif(isset($this->triggerEffects[$actionId])) {
                // Удалить элементы
                foreach($this->triggerEffects[$actionId] as $paramId => $effect) {
                    $effect->deleteSelf();
                }

                unset($this->triggerEffects[$actionId]);
            }
        }
    }
}