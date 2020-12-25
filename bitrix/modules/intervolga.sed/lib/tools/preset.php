<?php

namespace Intervolga\Sed\Tools;

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;


use Intervolga\Sed\Entities\Process;
use Intervolga\Sed\Entities\ProcessStatus;
use Intervolga\Sed\Entities\Action;
use Intervolga\Sed\Entities\ActionParam;
use Intervolga\Sed\Entities\ParticipantRole;
use Intervolga\Sed\Entities\TaskStatusElement;
use Intervolga\Sed\Entities\TaskTypeElement;
use Intervolga\Sed\Entities\TaskStatusTransition;
use Intervolga\Sed\Entities\TriggerEffect;
use Intervolga\Sed\Entities\TaskStatusTrigger;


Loc::loadMessages(__FILE__);

class Preset
{
    // equip process
    const EQUIP_TTYPE_LINEAR = 'SED_LINEAR_TYPE';

    const EQUIP_PS_ACCEPTED_BY_HEAD = 'ACCEPTED_BY_HEAD';
    const EQUIP_PS_ACCEPTED_BY_ACCOUNTANT = 'ACCEPTED_BY_ACCOUNTANT';
    const EQUIP_PS_ACCEPTED_BY_DIRECTOR = 'ACCEPTED_BY_DIRECTOR';
    const EQUIP_PS_PAUSED_BY_HEAD = 'PAUSED_BY_HEAD';
    const EQUIP_PS_PAUSED_BY_ACCOUNTANT = 'PAUSED_BY_ACCOUNTANT';
    const EQUIP_PS_PAUSED_BY_DIRECTOR = 'PAUSED_BY_DIRECTOR';

    // rent process
    const RENT_TTYPE_LINEAR = 'SED_SIMPLE_STEP';
    const RENT_TTYPE_AUDITOR = 'SED_AUDITOR_STEP';

    const RENT_TTYPE_DECISION_MAKER = 'SED_DECISION_MAKER_TASK_TYPE';
    const RENT_TSTATUS_APPROVED_NOT_BIG = 'APPROVED_NOT_BIG';
    const RENT_TSTATUS_RETURN_BACK = 'RETURN_BACK';

    const RENT_PS_FINANCIER_APPROVEMENT = 'FINANCIER_APPROVEMENT';
    const RENT_PS_ACCOUNTANT_APPROVEMENT = 'ACCOUNTANT_APPROVEMENT';
    const RENT_PS_HEAD_APPROVEMENT = 'HEAD_APPROVEMENT';
    const RENT_PS_AUDITOR_APPROVEMENT = 'AUDITOR_APPROVEMENT';


    protected static $actionsIds = null;
    protected static $actionParamsIds = null;

    /**
     * Создает 2 маршрута по умолчанию
     * Проверка на необходимость установки данных должна быть осуществлена ранее
     */
    public static function installPresets()
    {
        if (!Loader::includeModule('tasks')) {
            return;
        }

        $conn = Application::getConnection();
        $conn->startTransaction();
        try {
            static::installEquipmentPreset();
            static::installRentPreset();
            $conn->commitTransaction();
        } catch (SystemException $e) {
            $conn->rollbackTransaction();
        }
    }

    /**
     * @return array
     */
    protected static function getActionsIds()
    {
        if (!is_array(static::$actionsIds)) {

            static::$actionsIds = array();
            $actions = Action::getListAll();

            foreach ($actions as $action) {
                static::$actionsIds[$action->getCode()] = $action->getId();
            }
        }

        return static::$actionsIds;
    }

    /**
     * @return array
     */
    protected static function getActionParamsIds()
    {
        if (!is_array(static::$actionParamsIds)) {

            static::$actionParamsIds = array();
            $actionParams = ActionParam::getListAll(array(), array('ACTION.CODE'));

            foreach ($actionParams as $actionParam) {
                static::$actionParamsIds[$actionParam->getReferenceActionCode()][$actionParam->getCode()] = $actionParam->getId();
            }
        }

        return static::$actionParamsIds;
    }

    /**
     * Создание маршрута "закупка оргтехники"
     */
    protected static function installEquipmentPreset()
    {
        $bPresetExist = true;
        try {
            Process::getOneByFilter(array('NAME' => Loc::getMessage('C.PRESET.EQUIP.PROCESS_NAME')));
        } catch (\Bitrix\Main\ObjectNotFoundException $e) {
            $bPresetExist = false;
        }

        if ($bPresetExist) {
            return;
        }
        /*
         * 1. Маршрут со статусами по умолчанию
         * 2. ТЗ для инициатора (если не был создан ранее)
         * 3, ТЗ для инициатора привязан с новому маршруту
         */
        $processId = Process::createEmpty()
            ->setName(Loc::getMessage('C.PRESET.EQUIP.PROCESS_NAME'))
            ->save();

        // ТЗ "участник линейного согласования"
        $linearTType = Process::createNonInitiatorTType($processId, Loc::getMessage('C.PRESET.EQUIP.TT_LINEAR'), static::EQUIP_TTYPE_LINEAR);
        // ТЗ "Инициатор процесса согласования"
        $initiatorTType = \Intervolga\Sed\Entities\TaskTypeElement::getByXmlId(Process::INITIATOR_TTYPE_CODE);

        // Статусы ТЗ "участник линейного согласования"
        $linearTStatuses = TaskStatusElement::getListAll($linearTType);
        // Статусы ТЗ "Инициатор процесса согласования"
        $initiatorTStatuses = TaskStatusElement::getListAll($initiatorTType);

        $linearTStatusIds = array();
        $initiatorTStatusIds = array();

        foreach ($linearTStatuses as $tstatus) {
            $linearTStatusIds[$tstatus->getCode()] = $tstatus->getId();
        }

        foreach ($initiatorTStatuses as $tstatus) {
            $initiatorTStatusIds[$tstatus->getCode()] = $tstatus->getId();
        }

        //region "Роли участников процесса"
        // Роль "Руководитель"
        $headRoleId = \Intervolga\Sed\Entities\ParticipantRole::createEmpty()
            ->setProcessId($processId)
            ->setName(Loc::getMessage('C.PRESET.EQUIP.ROLE_HEAD'))
            ->save();

        // Роль "Бухгалтер"
        $accountantRoleId = \Intervolga\Sed\Entities\ParticipantRole::createEmpty()
            ->setProcessId($processId)
            ->setName(Loc::getMessage('C.PRESET.EQUIP.ROLE_ACCOUNTANT'))
            ->save();

        // Роль "Директор"
        $directorRoleId = \Intervolga\Sed\Entities\ParticipantRole::createEmpty()
            ->setProcessId($processId)
            ->setName(Loc::getMessage('C.PRESET.EQUIP.ROLE_DIRECTOR'))
            ->save();

        $initiatorRole = ParticipantRole::getProcessInitiator($processId);
        $initiatorRoleId = $initiatorRole->getId();
        //endregion

        //region "Переходы между статусами"
        $transitionMap = array(
            static::getTransitionMapKey($linearTType->getId(),
                $linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_PAUSED],
                $linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_NOT_APPROVED],
                TaskStatusTransition::RESPONSIBLE_CODE
            ) => array(
                'NEED_COMMENT' => true,
                'BTN_LABEL' => Loc::getMessage('C.PRESET.EQUIP.BTN_NOT_APPROVED'),
            ),
            static::getTransitionMapKey($linearTType->getId(),
                $linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_ACCEPTED],
                $linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_NOT_APPROVED],
                TaskStatusTransition::RESPONSIBLE_CODE
            ) => array(
                'NEED_COMMENT' => true,
                'BTN_LABEL' => Loc::getMessage('C.PRESET.EQUIP.BTN_NOT_APPROVED'),
            ),
            static::getTransitionMapKey($linearTType->getId(),
                $linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_PAUSED],
                $linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_APPROVED],
                TaskStatusTransition::RESPONSIBLE_CODE
            ) => array(
                'NEED_COMMENT' => true,
                'BTN_LABEL' => Loc::getMessage('C.PRESET.EQUIP.BTN_APPROVED'),
            ),
            static::getTransitionMapKey($linearTType->getId(),
                $linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_ACCEPTED],
                $linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_PAUSED],
                TaskStatusTransition::RESPONSIBLE_CODE
            ) => array(
                'NEED_COMMENT' => true,
                'BTN_LABEL' => Loc::getMessage('C.PRESET.EQUIP.BTN_PAUSED')
            ),
            static::getTransitionMapKey($linearTType->getId(),
                $linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_ACCEPTED],
                $linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_APPROVED],
                TaskStatusTransition::RESPONSIBLE_CODE
            ) => array(
                'NEED_COMMENT' => false,
                'BTN_LABEL' => Loc::getMessage('C.PRESET.EQUIP.BTN_APPROVED')
            ),
            static::getTransitionMapKey($initiatorTType->getId(),
                $initiatorTStatusIds[Process::INITIATOR_TSTATUS_CODE_NEW],
                $initiatorTStatusIds[Process::INITIATOR_TSTATUS_CODE_PROGRESS],
                TaskStatusTransition::RESPONSIBLE_CODE
            ) => array(
                'NEED_COMMENT' => false,
                'BTN_LABEL' => Loc::getMessage('C.PRESET.EQUIP.BTN_START')
            ),
        );

        $transitions = TaskStatusTransition::getListByFilter(
            array(
                'USER_ROLE' => TaskStatusTransition::RESPONSIBLE_CODE,
                array(
                    'LOGIC' => 'OR',
                    array(
                        'TASK_TYPE' => $linearTType->getId(),
                        array(
                            'LOGIC' => 'OR',
                            array(
                                'SOURCE_STATUS' => $linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_PAUSED],
                                'DEST_STATUS' => $linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_NOT_APPROVED]
                            ),
                            array(
                                'SOURCE_STATUS' => $linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_ACCEPTED],
                                'DEST_STATUS' => $linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_NOT_APPROVED]
                            ),
                            array(
                                'SOURCE_STATUS' => $linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_PAUSED],
                                'DEST_STATUS' => $linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_APPROVED]
                            ),
                            array(
                                'SOURCE_STATUS' => $linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_ACCEPTED],
                                'DEST_STATUS' => $linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_PAUSED]
                            ),
                            array(
                                'SOURCE_STATUS' => $linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_ACCEPTED],
                                'DEST_STATUS' => $linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_APPROVED]
                            ),
                        )
                    ),
                    array(
                        'TASK_TYPE' => $initiatorTType->getId(),
                        'SOURCE_STATUS' => $initiatorTStatusIds[Process::INITIATOR_TSTATUS_CODE_NEW],
                        'DEST_STATUS' => $initiatorTStatusIds[Process::INITIATOR_TSTATUS_CODE_PROGRESS]
                    )
                )
            )
        );

        foreach ($transitions as $transition) {
            $mapItem = $transitionMap[static::getTransitionMapKey($transition->getTaskType(), $transition->getSourceStatus(), $transition->getDestStatus(), $transition->getUserRole())];
            if (isset($mapItem)) {
                /** @var TaskStatusTransition $transition */
                $transition->setTransitionAllowed(true)
                    ->setCommentIsNeeded($mapItem['NEED_COMMENT'])
                    ->setButtonLabel($mapItem['BTN_LABEL'])
                    ->save();
            }
        }
        //endregion

        //region статусы маршрута

        // необходимые стандартные статусы маршрута
        $processStatusesIds = array();
        $processStatuses = ProcessStatus::getListByProcessId($processId);

        foreach ($processStatuses as $processStatus) {
            $processStatusesIds[$processStatus->getCode()] = $processStatus->getId();
        }

        // новые статусы в маршруте

        $processStatusAcceptedByHeadId = ProcessStatus::createEmpty()
            ->setProcessId($processId)
            ->setCode(static::EQUIP_PS_ACCEPTED_BY_HEAD)
            ->setName(Loc::getMessage('C.PRESET.EQUIP.PS_ACCEPTED_BY_HEAD'))
            ->save();

        $processStatusAcceptedByAccountantId = ProcessStatus::createEmpty()
            ->setProcessId($processId)
            ->setCode(static::EQUIP_PS_ACCEPTED_BY_ACCOUNTANT)
            ->setName(Loc::getMessage('C.PRESET.EQUIP.PS_ACCEPTED_BY_ACCOUNTANT'))
            ->save();

        $processStatusAcceptedByDirectorId = ProcessStatus::createEmpty()
            ->setProcessId($processId)
            ->setCode(static::EQUIP_PS_ACCEPTED_BY_DIRECTOR)
            ->setName(Loc::getMessage('C.PRESET.EQUIP.PS_ACCEPTED_BY_DIRECTOR'))
            ->save();

        $processStatusPausedByHeadId = ProcessStatus::createEmpty()
            ->setProcessId($processId)
            ->setCode(static::EQUIP_PS_PAUSED_BY_HEAD)
            ->setName(Loc::getMessage('C.PRESET.EQUIP.PS_PAUSED_BY_HEAD'))
            ->save();

        $processStatusPausedByAccountantId = ProcessStatus::createEmpty()
            ->setProcessId($processId)
            ->setCode(static::EQUIP_PS_PAUSED_BY_ACCOUNTANT)
            ->setName(Loc::getMessage('C.PRESET.EQUIP.PS_PAUSED_BY_ACCOUNTANT'))
            ->save();

        $processStatusPausedByDirectorId = ProcessStatus::createEmpty()
            ->setProcessId($processId)
            ->setCode(static::EQUIP_PS_PAUSED_BY_DIRECTOR)
            ->setName(Loc::getMessage('C.PRESET.EQUIP.PS_PAUSED_BY_DIRECTOR'))
            ->save();


        $processStatusesIds[static::EQUIP_PS_ACCEPTED_BY_HEAD] = $processStatusAcceptedByHeadId;
        $processStatusesIds[static::EQUIP_PS_ACCEPTED_BY_ACCOUNTANT] = $processStatusAcceptedByAccountantId;
        $processStatusesIds[static::EQUIP_PS_ACCEPTED_BY_DIRECTOR] = $processStatusAcceptedByDirectorId;
        $processStatusesIds[static::EQUIP_PS_PAUSED_BY_HEAD] = $processStatusPausedByHeadId;
        $processStatusesIds[static::EQUIP_PS_PAUSED_BY_ACCOUNTANT] = $processStatusPausedByAccountantId;
        $processStatusesIds[static::EQUIP_PS_PAUSED_BY_DIRECTOR] = $processStatusPausedByDirectorId;

        //endregion


        $actionsIds = static::getActionsIds();
        $actionParamsIds = static::getActionParamsIds();
        $triggerTypeTask = TaskStatusTrigger::getType();

        //region запуск процесса инициатором
        $triggerId = TaskStatusTrigger::createEmpty()
            ->setProcessId($processId)
            ->setResponsibleRoleId($initiatorRoleId)
            ->setProcessStatusId($processStatusesIds[ProcessStatus::STATUS_CODE_NEW])
            ->setNewUfStatusId($initiatorTStatusIds[Process::INITIATOR_TSTATUS_CODE_PROGRESS])
            ->save();

        // создание задачи на руководителя
        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['RESPONSIBLE_ID'])
            ->setParamValue($headRoleId)
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['ORIGINATOR_ID'])
            ->setParamValue($initiatorRoleId)
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['UF_TASK_TTYPE'])
            ->setParamValue($linearTType->getId())
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['TASK_TITLE'])
            ->setParamValue(Loc::getMessage('C.PRESET.EQUIP.INIT_TO_HEAD_TITLE'))
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['TASK_DESC'])
            ->setParamValue(Loc::getMessage('C.PRESET.EQUIP.INIT_TO_HEAD_DESC'))
            ->save();

        // перевод процесса в статус "На согласовании у руководителя"
        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['UpdateContractStatus'])
            ->setParamId($actionParamsIds['UpdateContractStatus']['PROCESS_STATUS_ID'])
            ->setParamValue($processStatusesIds[static::EQUIP_PS_ACCEPTED_BY_HEAD])
            ->save();
        //endregion

        //region руководитель - "пауза"
        $triggerId = TaskStatusTrigger::createEmpty()
            ->setProcessId($processId)
            ->setResponsibleRoleId($headRoleId)
            ->setOriginatorRoleId($initiatorRoleId)
            ->setProcessStatusId($processStatusesIds[static::EQUIP_PS_ACCEPTED_BY_HEAD])
            ->setNewUfStatusId($linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_PAUSED])
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['UpdateContractStatus'])
            ->setParamId($actionParamsIds['UpdateContractStatus']['PROCESS_STATUS_ID'])
            ->setParamValue($processStatusesIds[static::EQUIP_PS_PAUSED_BY_HEAD])
            ->save();
        //endregion

        //region руководитель - "пауза" - "согласовано"
        $triggerId = TaskStatusTrigger::createEmpty()
            ->setProcessId($processId)
            ->setResponsibleRoleId($headRoleId)
            ->setOriginatorRoleId($initiatorRoleId)
            ->setProcessStatusId($processStatusesIds[static::EQUIP_PS_PAUSED_BY_HEAD])
            ->setNewUfStatusId($linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_APPROVED])
            ->save();

        // создание задачи на бухгалтера
        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['RESPONSIBLE_ID'])
            ->setParamValue($accountantRoleId)
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['ORIGINATOR_ID'])
            ->setParamValue($headRoleId)
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['UF_TASK_TTYPE'])
            ->setParamValue($linearTType->getId())
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['TASK_TITLE'])
            ->setParamValue(Loc::getMessage('C.PRESET.EQUIP.HEAD_TO_ACC_TITLE'))
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['TASK_DESC'])
            ->setParamValue(Loc::getMessage('C.PRESET.EQUIP.HEAD_TO_ACC_DESC'))
            ->save();

        // перевод процесса в статус "на согласовании у бухгалтера"
        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['UpdateContractStatus'])
            ->setParamId($actionParamsIds['UpdateContractStatus']['PROCESS_STATUS_ID'])
            ->setParamValue($processStatusesIds[static::EQUIP_PS_ACCEPTED_BY_ACCOUNTANT])
            ->save();

        //endregion

        //region руководитель "принята" - "согласовано"
        $triggerId = TaskStatusTrigger::createEmpty()
            ->setProcessId($processId)
            ->setResponsibleRoleId($headRoleId)
            ->setOriginatorRoleId($initiatorRoleId)
            ->setProcessStatusId($processStatusesIds[static::EQUIP_PS_ACCEPTED_BY_HEAD])
            ->setNewUfStatusId($linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_APPROVED])
            ->save();

        // создание задачи на бухгалтера
        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['RESPONSIBLE_ID'])
            ->setParamValue($accountantRoleId)
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['ORIGINATOR_ID'])
            ->setParamValue($headRoleId)
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['UF_TASK_TTYPE'])
            ->setParamValue($linearTType->getId())
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['TASK_TITLE'])
            ->setParamValue(Loc::getMessage('C.PRESET.EQUIP.HEAD_TO_ACC_TITLE'))
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['TASK_DESC'])
            ->setParamValue(Loc::getMessage('C.PRESET.EQUIP.HEAD_TO_ACC_DESC'))
            ->save();

        // перевод процесса в статус "на согласовании у бухгалтера"
        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['UpdateContractStatus'])
            ->setParamId($actionParamsIds['UpdateContractStatus']['PROCESS_STATUS_ID'])
            ->setParamValue($processStatusesIds[static::EQUIP_PS_ACCEPTED_BY_ACCOUNTANT])
            ->save();
        //endregion

        //region бухгалтер - "пауза"
        $triggerId = TaskStatusTrigger::createEmpty()
            ->setProcessId($processId)
            ->setResponsibleRoleId($accountantRoleId)
            ->setOriginatorRoleId($headRoleId)
            ->setProcessStatusId($processStatusesIds[static::EQUIP_PS_ACCEPTED_BY_ACCOUNTANT])
            ->setNewUfStatusId($linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_PAUSED])
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['UpdateContractStatus'])
            ->setParamId($actionParamsIds['UpdateContractStatus']['PROCESS_STATUS_ID'])
            ->setParamValue($processStatusesIds[static::EQUIP_PS_PAUSED_BY_ACCOUNTANT])
            ->save();
        //endregion

        //region бухгалтер "пауза" - "согласовано"
        $triggerId = TaskStatusTrigger::createEmpty()
            ->setProcessId($processId)
            ->setResponsibleRoleId($accountantRoleId)
            ->setOriginatorRoleId($headRoleId)
            ->setProcessStatusId($processStatusesIds[static::EQUIP_PS_PAUSED_BY_ACCOUNTANT])
            ->setNewUfStatusId($linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_APPROVED])
            ->save();

        // создание задачи на директора
        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['RESPONSIBLE_ID'])
            ->setParamValue($directorRoleId)
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['ORIGINATOR_ID'])
            ->setParamValue($accountantRoleId)
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['UF_TASK_TTYPE'])
            ->setParamValue($linearTType->getId())
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['TASK_TITLE'])
            ->setParamValue(Loc::getMessage('C.PRESET.EQUIP.ACC_TO_DIR_TITLE'))
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['TASK_DESC'])
            ->setParamValue(Loc::getMessage('C.PRESET.EQUIP.ACC_TO_DIR_DESC'))
            ->save();

        // перевод процесса в статус "на согласовании у директора"
        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['UpdateContractStatus'])
            ->setParamId($actionParamsIds['UpdateContractStatus']['PROCESS_STATUS_ID'])
            ->setParamValue($processStatusesIds[static::EQUIP_PS_ACCEPTED_BY_DIRECTOR])
            ->save();
        //endregion

        //region бухгалтер "принята" - "согласовано"
        $triggerId = TaskStatusTrigger::createEmpty()
            ->setProcessId($processId)
            ->setResponsibleRoleId($accountantRoleId)
            ->setOriginatorRoleId($headRoleId)
            ->setProcessStatusId($processStatusesIds[static::EQUIP_PS_ACCEPTED_BY_ACCOUNTANT])
            ->setNewUfStatusId($linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_APPROVED])
            ->save();

        // создание задачи на директора
        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['RESPONSIBLE_ID'])
            ->setParamValue($directorRoleId)
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['ORIGINATOR_ID'])
            ->setParamValue($accountantRoleId)
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['UF_TASK_TTYPE'])
            ->setParamValue($linearTType->getId())
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['TASK_TITLE'])
            ->setParamValue(Loc::getMessage('C.PRESET.EQUIP.ACC_TO_DIR_TITLE'))
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['TASK_DESC'])
            ->setParamValue(Loc::getMessage('C.PRESET.EQUIP.ACC_TO_DIR_DESC'))
            ->save();

        // перевод процесса в статус "на согласовании у директора"
        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['UpdateContractStatus'])
            ->setParamId($actionParamsIds['UpdateContractStatus']['PROCESS_STATUS_ID'])
            ->setParamValue($processStatusesIds[static::EQUIP_PS_ACCEPTED_BY_DIRECTOR])
            ->save();
        //endregion

        //region директор - "пауза"
        $triggerId = TaskStatusTrigger::createEmpty()
            ->setProcessId($processId)
            ->setResponsibleRoleId($directorRoleId)
            ->setProcessStatusId($processStatusesIds[static::EQUIP_PS_ACCEPTED_BY_DIRECTOR])
            ->setNewUfStatusId($linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_PAUSED])
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['UpdateContractStatus'])
            ->setParamId($actionParamsIds['UpdateContractStatus']['PROCESS_STATUS_ID'])
            ->setParamValue($processStatusesIds[static::EQUIP_PS_PAUSED_BY_DIRECTOR])
            ->save();
        //endregion

        //region директор - "пауза" - "согласовано"
        $triggerId = TaskStatusTrigger::createEmpty()
            ->setProcessId($processId)
            ->setResponsibleRoleId($directorRoleId)
            ->setProcessStatusId($processStatusesIds[static::EQUIP_PS_PAUSED_BY_DIRECTOR])
            ->setNewUfStatusId($linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_APPROVED])
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['UpdateContractStatus'])
            ->setParamId($actionParamsIds['UpdateContractStatus']['PROCESS_STATUS_ID'])
            ->setParamValue($processStatusesIds[ProcessStatus::STATUS_CODE_APPROVED])
            ->save();
        //endregion

        //region директор - "принята" - "согласовано"
        $triggerId = TaskStatusTrigger::createEmpty()
            ->setProcessId($processId)
            ->setResponsibleRoleId($directorRoleId)
            ->setProcessStatusId($processStatusesIds[static::EQUIP_PS_ACCEPTED_BY_DIRECTOR])
            ->setNewUfStatusId($linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_APPROVED])
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['UpdateContractStatus'])
            ->setParamId($actionParamsIds['UpdateContractStatus']['PROCESS_STATUS_ID'])
            ->setParamValue($processStatusesIds[ProcessStatus::STATUS_CODE_APPROVED])
            ->save();
        //endregion

        //region любой участник - "не согласовано"
        $triggerId = TaskStatusTrigger::createEmpty()
            ->setProcessId($processId)
            ->setNewUfStatusId($linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_NOT_APPROVED])
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['UpdateContractStatus'])
            ->setParamId($actionParamsIds['UpdateContractStatus']['PROCESS_STATUS_ID'])
            ->setParamValue($processStatusesIds[ProcessStatus::STATUS_CODE_NOT_APPROVED])
            ->save();
        //endregion
    }

    /**
     * Созадние маршрута "согласование договора аренды"
     */
    protected static function installRentPreset()
    {
        $bPresetExist = true;
        try {
            Process::getOneByFilter(array('NAME' => Loc::getMessage('C.PRESET.RENT.PROCESS_NAME')));
        } catch (\Bitrix\Main\ObjectNotFoundException $e) {
            $bPresetExist = false;
        }

        if ($bPresetExist) {
            return;
        }

        /*
         * 1. Маршрут со статусами по умолчанию
         * 2. ТЗ для инициатора (если не был создан ранее)
         * 3, ТЗ для инициатора привязан с новому маршруту
         */
        $processId = Process::createEmpty()
            ->setName(Loc::getMessage('C.PRESET.RENT.PROCESS_NAME'))
            ->save();

        // ТЗ "Задача согласующего (да-нет)"
        $linearTType = Process::createNonInitiatorTType($processId, Loc::getMessage('C.PRESET.RENT.TT_LINEAR'), static::RENT_TTYPE_LINEAR);
        // ТЗ "Задача аудитора  (да-нет-возврат)"
        $auditorTType = Process::createNonInitiatorTType($processId, Loc::getMessage('C.PRESET.RENT.TT_AUDITOR'), static::RENT_TTYPE_AUDITOR);
        // ТЗ "Задача руководителя-решателя"
        $decisionMakerTType = Process::createNonInitiatorTType($processId, Loc::getMessage('C.PRESET.RENT.TT_DECISION_MAKER'), static::RENT_TTYPE_DECISION_MAKER);
        // ТЗ "Инициатор процесса согласования"
        $initiatorTType = \Intervolga\Sed\Entities\TaskTypeElement::getByXmlId(Process::INITIATOR_TTYPE_CODE);

        // Статусы ТЗ "Задача согласующего (да-нет)"
        $linearTStatuses = TaskStatusElement::getListAll($linearTType);
        // Статусы ТЗ "Задача аудитора  (да-нет-возврат)"
        $auditorTStatuses = TaskStatusElement::getListAll($auditorTType);
        // Статусы ТЗ "Задача руководителя-решателя"
        $decisionMakerTStatuses = TaskStatusElement::getListAll($decisionMakerTType);
        // Статусы ТЗ "Инициатор процесса согласования"
        $initiatorTStatuses = TaskStatusElement::getListAll($initiatorTType);

        $linearTStatusIds = array();
        $auditorTStatusIds = array();
        $decisionMakerTStatusIds = array();
        $initiatorTStatusIds = array();

        foreach ($linearTStatuses as $tstatus) {
            $linearTStatusIds[$tstatus->getCode()] = $tstatus->getId();
        }

        foreach ($auditorTStatuses as $tstatus) {
            $auditorTStatusIds[$tstatus->getCode()] = $tstatus->getId();
        }

        foreach ($decisionMakerTStatuses as $tstatus) {
            /** @var TaskStatusElement $tstatus */
            $decisionMakerTStatusIds[$tstatus->getCode()] = $tstatus->getId();
            // необходимо переименовать статус APPROVED -- Согласовано (крупный)
            if ($tstatus->getCode() == Process::PARTICIPANT_TSTATUS_CODE_APPROVED) {
                $tstatus->setName(Loc::getMessage('C.PRESET.RENT.TS_APPROVED'))
                    ->save();
            }
        }

        foreach ($initiatorTStatuses as $tstatus) {
            $initiatorTStatusIds[$tstatus->getCode()] = $tstatus->getId();
        }


        // новый статус для ТЗ "Задача руководителя-решателя"
        $approvedNotBigTStatus = TaskStatusElement::createEmpty($decisionMakerTType)
            ->setCode(static::RENT_TSTATUS_APPROVED_NOT_BIG)
            ->setName(Loc::getMessage('C.PRESET.RENT.TS_APPROVED_NOT_BIG'))
            ->setNativeTaskStatus(\CTasks::STATE_COMPLETED)
            ->save();

        // новый статус для ТЗ "Задача аудитора  (да-нет-возврат)"
        $returnBackTStatus = TaskStatusElement::createEmpty($auditorTType)
            ->setCode(static::RENT_TSTATUS_RETURN_BACK)
            ->setName(Loc::getMessage('C.PRESET.RENT.TS_RETURN_BACK'))
            ->setNativeTaskStatus(\CTasks::STATE_COMPLETED)
            ->save();

        $decisionMakerTStatusIds[static::RENT_TSTATUS_APPROVED_NOT_BIG] = $approvedNotBigTStatus->getId();
        $auditorTStatusIds[static::RENT_TSTATUS_RETURN_BACK] = $returnBackTStatus->getId();


        //region "Роли участников процесса"
        // Роль "Руководитель-решатель"
        $decMakerRoleId = \Intervolga\Sed\Entities\ParticipantRole::createEmpty()
            ->setProcessId($processId)
            ->setName(Loc::getMessage('C.PRESET.RENT.ROLE_DECISION_MAKER'))
            ->save();

        // Роль "Бухгалтер"
        $accountantRoleId = \Intervolga\Sed\Entities\ParticipantRole::createEmpty()
            ->setProcessId($processId)
            ->setName(Loc::getMessage('C.PRESET.RENT.ROLE_ACCOUNTANT'))
            ->save();

        // Роль "Директор"
        $directorRoleId = \Intervolga\Sed\Entities\ParticipantRole::createEmpty()
            ->setProcessId($processId)
            ->setName(Loc::getMessage('C.PRESET.RENT.ROLE_DIRECTOR'))
            ->save();

        // Роль "Финдиректор"
        $finDirectorRoleId = \Intervolga\Sed\Entities\ParticipantRole::createEmpty()
            ->setProcessId($processId)
            ->setName(Loc::getMessage('C.PRESET.RENT.ROLE_FIN_DIRECTOR'))
            ->save();

        // Роль "Аудитор"
        $auditorRoleId = \Intervolga\Sed\Entities\ParticipantRole::createEmpty()
            ->setProcessId($processId)
            ->setName(Loc::getMessage('C.PRESET.RENT.ROLE_AUDITOR'))
            ->save();

        $initiatorRole = ParticipantRole::getProcessInitiator($processId);
        $initiatorRoleId = $initiatorRole->getId();
        //endregion


        //region "Переходы между статусами"
        $transitionMap = array(
            // руководитель-решатель
            static::getTransitionMapKey($decisionMakerTType->getId(),
                $decisionMakerTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_ACCEPTED],
                $decisionMakerTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_NOT_APPROVED],
                TaskStatusTransition::RESPONSIBLE_CODE
            ) => array(
                'NEED_COMMENT' => true,
                'BTN_LABEL' => Loc::getMessage('C.PRESET.RENT.BTN_REFUSE'),
            ),
            static::getTransitionMapKey($decisionMakerTType->getId(),
                $decisionMakerTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_ACCEPTED],
                $decisionMakerTStatusIds[static::RENT_TSTATUS_APPROVED_NOT_BIG],
                TaskStatusTransition::RESPONSIBLE_CODE
            ) => array(
                'NEED_COMMENT' => false,
                'BTN_LABEL' => Loc::getMessage('C.PRESET.RENT.BTN_NOT_BIG'),
            ),
            static::getTransitionMapKey($decisionMakerTType->getId(),
                $decisionMakerTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_ACCEPTED],
                $decisionMakerTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_APPROVED],
                TaskStatusTransition::RESPONSIBLE_CODE
            ) => array(
                'NEED_COMMENT' => false,
                'BTN_LABEL' => Loc::getMessage('C.PRESET.RENT.BTN_BIG'),
            ),
            // согласующий (да-нет)
            static::getTransitionMapKey($linearTType->getId(),
                $linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_ACCEPTED],
                $linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_NOT_APPROVED],
                TaskStatusTransition::RESPONSIBLE_CODE
            ) => array(
                'NEED_COMMENT' => true,
                'BTN_LABEL' => Loc::getMessage('C.PRESET.RENT.BTN_REFUSE'),
            ),
            static::getTransitionMapKey($linearTType->getId(),
                $linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_ACCEPTED],
                $linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_APPROVED],
                TaskStatusTransition::RESPONSIBLE_CODE
            ) => array(
                'NEED_COMMENT' => false,
                'BTN_LABEL' => Loc::getMessage('C.PRESET.RENT.BTN_APPROVE'),
            ),
            // аудитор (да-нет возврат)
            static::getTransitionMapKey($auditorTType->getId(),
                $auditorTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_ACCEPTED],
                $auditorTStatusIds[static::RENT_TSTATUS_RETURN_BACK],
                TaskStatusTransition::RESPONSIBLE_CODE
            ) => array(
                'NEED_COMMENT' => true,
                'BTN_LABEL' => Loc::getMessage('C.PRESET.RENT.BTN_BACK_TO_FIN_DIR'),
            ),
            static::getTransitionMapKey($auditorTType->getId(),
                $auditorTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_ACCEPTED],
                $auditorTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_NOT_APPROVED],
                TaskStatusTransition::RESPONSIBLE_CODE
            ) => array(
                'NEED_COMMENT' => true,
                'BTN_LABEL' => Loc::getMessage('C.PRESET.RENT.BTN_REFUSE'),
            ),
            static::getTransitionMapKey($auditorTType->getId(),
                $auditorTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_ACCEPTED],
                $auditorTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_APPROVED],
                TaskStatusTransition::RESPONSIBLE_CODE
            ) => array(
                'NEED_COMMENT' => false,
                'BTN_LABEL' => Loc::getMessage('C.PRESET.RENT.BTN_APPROVE'),
            ),
        );

        $transitions = TaskStatusTransition::getListByFilter(array(
            'USER_ROLE' => TaskStatusTransition::RESPONSIBLE_CODE,
            array(
                'LOGIC' => 'OR',
                array(
                    'TASK_TYPE' => $linearTType->getId(),
                    'SOURCE_STATUS' => $linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_ACCEPTED],
                    'DEST_STATUS' => array(
                        $linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_APPROVED],
                        $linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_NOT_APPROVED],
                    )
                ),
                array(
                    'TASK_TYPE' => $decisionMakerTType->getId(),
                    'SOURCE_STATUS' => $decisionMakerTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_ACCEPTED],
                    'DEST_STATUS' => array(
                        $decisionMakerTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_APPROVED],
                        $decisionMakerTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_NOT_APPROVED],
                        $decisionMakerTStatusIds[static::RENT_TSTATUS_APPROVED_NOT_BIG],
                    )
                ),
                array(
                    'TASK_TYPE' => $auditorTType->getId(),
                    'SOURCE_STATUS' => $auditorTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_ACCEPTED],
                    'DEST_STATUS' => array(
                        $auditorTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_APPROVED],
                        $auditorTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_NOT_APPROVED],
                        $auditorTStatusIds[static::RENT_TSTATUS_RETURN_BACK],
                    )
                )
            )
        ));

        foreach ($transitions as $transition) {
            $mapItem = $transitionMap[static::getTransitionMapKey($transition->getTaskType(), $transition->getSourceStatus(), $transition->getDestStatus(), $transition->getUserRole())];
            if (isset($mapItem)) {
                /** @var TaskStatusTransition $transition */
                $transition->setTransitionAllowed(true)
                    ->setCommentIsNeeded($mapItem['NEED_COMMENT'])
                    ->setButtonLabel($mapItem['BTN_LABEL'])
                    ->save();
            }
        }
        //endregion


        //region статусы маршрута

        // необходимые стандартные статусы маршрута
        $processStatusesIds = array();
        $processStatuses = ProcessStatus::getListByProcessId($processId);

        foreach ($processStatuses as $processStatus) {
            $processStatusesIds[$processStatus->getCode()] = $processStatus->getId();
        }

        // новые статусы в маршруте
        $processStatusAccountantId = ProcessStatus::createEmpty()
            ->setProcessId($processId)
            ->setCode(static::RENT_PS_ACCOUNTANT_APPROVEMENT)
            ->setName(Loc::getMessage('C.PRESET.RENT.PS_ACCOUNTANT_APPROVEMENT'))
            ->save();

        $processStatusAuditorId = ProcessStatus::createEmpty()
            ->setProcessId($processId)
            ->setCode(static::RENT_PS_AUDITOR_APPROVEMENT)
            ->setName(Loc::getMessage('C.PRESET.RENT.PS_AUDITOR_APPROVEMENT'))
            ->save();

        $processStatusFinDirectorId = ProcessStatus::createEmpty()
            ->setProcessId($processId)
            ->setCode(static::RENT_PS_FINANCIER_APPROVEMENT)
            ->setName(Loc::getMessage('C.PRESET.RENT.PS_FINANCIER_APPROVEMENT'))
            ->save();

        $processStatusDirectorId = ProcessStatus::createEmpty()
            ->setProcessId($processId)
            ->setCode(static::RENT_PS_HEAD_APPROVEMENT)
            ->setName(Loc::getMessage('C.PRESET.RENT.PS_HEAD_APPROVEMENT'))
            ->save();


        $processStatusesIds[static::RENT_PS_ACCOUNTANT_APPROVEMENT] = $processStatusAccountantId;
        $processStatusesIds[static::RENT_PS_AUDITOR_APPROVEMENT] = $processStatusAuditorId;
        $processStatusesIds[static::RENT_PS_FINANCIER_APPROVEMENT] = $processStatusFinDirectorId;
        $processStatusesIds[static::RENT_PS_HEAD_APPROVEMENT] = $processStatusDirectorId;

        //endregion

        $actionsIds = static::getActionsIds();
        $actionParamsIds = static::getActionParamsIds();
        $triggerTypeTask = TaskStatusTrigger::getType();

        //region запуск процесса инициатором
        $triggerId = TaskStatusTrigger::createEmpty()
            ->setProcessId($processId)
            ->setResponsibleRoleId($initiatorRoleId)
            ->setProcessStatusId($processStatusesIds[ProcessStatus::STATUS_CODE_NEW])
            ->setNewUfStatusId($initiatorTStatusIds[Process::INITIATOR_TSTATUS_CODE_PROGRESS])
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['RESPONSIBLE_ID'])
            ->setParamValue($decMakerRoleId)
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['ORIGINATOR_ID'])
            ->setParamValue($initiatorRoleId)
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['UF_TASK_TTYPE'])
            ->setParamValue($decisionMakerTType->getId())
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['TASK_TITLE'])
            ->setParamValue(Loc::getMessage('C.PRESET.RENT.INIT_TO_DEC_MAKER_TITLE'))
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['TASK_DESC'])
            ->setParamValue(Loc::getMessage('C.PRESET.RENT.INIT_TO_DEC_MAKER_DESC'))
            ->save();
        //endregion

        //region решатель - "отказ"
        $triggerId = TaskStatusTrigger::createEmpty()
            ->setProcessId($processId)
            ->setResponsibleRoleId($decMakerRoleId)
            ->setNewUfStatusId($decisionMakerTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_NOT_APPROVED])
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['UpdateContractStatus'])
            ->setParamId($actionParamsIds['UpdateContractStatus']['PROCESS_STATUS_ID'])
            ->setParamValue($processStatusesIds[ProcessStatus::STATUS_CODE_NOT_APPROVED])
            ->save();
        //endregion

        //region решатель - "крупный"
        $triggerId = TaskStatusTrigger::createEmpty()
            ->setProcessId($processId)
            ->setResponsibleRoleId($decMakerRoleId)
            ->setNewUfStatusId($decisionMakerTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_APPROVED])
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['RESPONSIBLE_ID'])
            ->setParamValue($finDirectorRoleId)
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['ORIGINATOR_ID'])
            ->setParamValue($decMakerRoleId)
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['UF_TASK_TTYPE'])
            ->setParamValue($linearTType->getId())
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['TASK_TITLE'])
            ->setParamValue(Loc::getMessage('C.PRESET.RENT.DEC_MAKER_TO_ACC_TITLE'))
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['UpdateContractStatus'])
            ->setParamId($actionParamsIds['UpdateContractStatus']['PROCESS_STATUS_ID'])
            ->setParamValue($processStatusesIds[static::RENT_PS_FINANCIER_APPROVEMENT])
            ->save();
        //endregion

        //region решатель - "не крупный"
        $triggerId = TaskStatusTrigger::createEmpty()
            ->setProcessId($processId)
            ->setResponsibleRoleId($decMakerRoleId)
            ->setNewUfStatusId($decisionMakerTStatusIds[static::RENT_TSTATUS_APPROVED_NOT_BIG])
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['RESPONSIBLE_ID'])
            ->setParamValue($accountantRoleId)
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['ORIGINATOR_ID'])
            ->setParamValue($decMakerRoleId)
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['UF_TASK_TTYPE'])
            ->setParamValue($linearTType->getId())
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['UpdateContractStatus'])
            ->setParamId($actionParamsIds['UpdateContractStatus']['PROCESS_STATUS_ID'])
            ->setParamValue($processStatusesIds[static::RENT_PS_ACCOUNTANT_APPROVEMENT])
            ->save();
        //endregion

        //region бухгалтер - "согласовано"
        $triggerId = TaskStatusTrigger::createEmpty()
            ->setProcessId($processId)
            ->setResponsibleRoleId($accountantRoleId)
            ->setProcessStatusId($processStatusesIds[static::RENT_PS_ACCOUNTANT_APPROVEMENT])
            ->setNewUfStatusId($linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_APPROVED])
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['RESPONSIBLE_ID'])
            ->setParamValue($directorRoleId)
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['ORIGINATOR_ID'])
            ->setParamValue($accountantRoleId)
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['UF_TASK_TTYPE'])
            ->setParamValue($linearTType->getId())
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['UpdateContractStatus'])
            ->setParamId($actionParamsIds['UpdateContractStatus']['PROCESS_STATUS_ID'])
            ->setParamValue($processStatusesIds[static::RENT_PS_HEAD_APPROVEMENT])
            ->save();
        //endregion

        //region бухгалтер - "не согласовано"
        $triggerId = TaskStatusTrigger::createEmpty()
            ->setProcessId($processId)
            ->setResponsibleRoleId($accountantRoleId)
            ->setProcessStatusId($processStatusesIds[static::RENT_PS_ACCOUNTANT_APPROVEMENT])
            ->setNewUfStatusId($linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_NOT_APPROVED])
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['UpdateContractStatus'])
            ->setParamId($actionParamsIds['UpdateContractStatus']['PROCESS_STATUS_ID'])
            ->setParamValue($processStatusesIds[ProcessStatus::STATUS_CODE_NOT_APPROVED])
            ->save();
        //endregion

        //region аудитор - "согласовано"
        $triggerId = TaskStatusTrigger::createEmpty()
            ->setProcessId($processId)
            ->setResponsibleRoleId($auditorRoleId)
            ->setProcessStatusId($processStatusesIds[static::RENT_PS_AUDITOR_APPROVEMENT])
            ->setNewUfStatusId($auditorTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_APPROVED])
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['RESPONSIBLE_ID'])
            ->setParamValue($directorRoleId)
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['ORIGINATOR_ID'])
            ->setParamValue($auditorRoleId)
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['UF_TASK_TTYPE'])
            ->setParamValue($linearTType->getId())
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['UpdateContractStatus'])
            ->setParamId($actionParamsIds['UpdateContractStatus']['PROCESS_STATUS_ID'])
            ->setParamValue($processStatusesIds[static::RENT_PS_HEAD_APPROVEMENT])
            ->save();
        //endregion

        //region аудитор - "не согласовано"
        $triggerId = TaskStatusTrigger::createEmpty()
            ->setProcessId($processId)
            ->setResponsibleRoleId($auditorRoleId)
            ->setProcessStatusId($processStatusesIds[static::RENT_PS_AUDITOR_APPROVEMENT])
            ->setNewUfStatusId($auditorTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_NOT_APPROVED])
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['UpdateContractStatus'])
            ->setParamId($actionParamsIds['UpdateContractStatus']['PROCESS_STATUS_ID'])
            ->setParamValue($processStatusesIds[ProcessStatus::STATUS_CODE_NOT_APPROVED])
            ->save();
        //endregion

        //region аудитор - "возврат"
        $triggerId = TaskStatusTrigger::createEmpty()
            ->setProcessId($processId)
            ->setResponsibleRoleId($auditorRoleId)
            ->setProcessStatusId($processStatusesIds[static::RENT_PS_AUDITOR_APPROVEMENT])
            ->setNewUfStatusId($auditorTStatusIds[static::RENT_TSTATUS_RETURN_BACK])
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['RESPONSIBLE_ID'])
            ->setParamValue($finDirectorRoleId)
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['ORIGINATOR_ID'])
            ->setParamValue($auditorRoleId)
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['UF_TASK_TTYPE'])
            ->setParamValue($linearTType->getId())
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['TASK_TITLE'])
            ->setParamValue(Loc::getMessage('C.PRESET.RENT.AUD_TO_FIN_TITLE'))
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['TASK_DESC'])
            ->setParamValue(Loc::getMessage('C.PRESET.RENT.AUD_TO_FIN_DESC'))
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['UpdateContractStatus'])
            ->setParamId($actionParamsIds['UpdateContractStatus']['PROCESS_STATUS_ID'])
            ->setParamValue($processStatusesIds[static::RENT_PS_FINANCIER_APPROVEMENT])
            ->save();
        //endregion

        //region финдиректор - "согласовано"
        $triggerId = TaskStatusTrigger::createEmpty()
            ->setProcessId($processId)
            ->setResponsibleRoleId($finDirectorRoleId)
            ->setProcessStatusId($processStatusesIds[static::RENT_PS_FINANCIER_APPROVEMENT])
            ->setNewUfStatusId($linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_APPROVED])
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['RESPONSIBLE_ID'])
            ->setParamValue($auditorRoleId)
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['ORIGINATOR_ID'])
            ->setParamValue($finDirectorRoleId)
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['UF_TASK_TTYPE'])
            ->setParamValue($auditorTType->getId())
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['CreateTask'])
            ->setParamId($actionParamsIds['CreateTask']['TASK_TITLE'])
            ->setParamValue(Loc::getMessage('C.PRESET.RENT.FIN_TO_AUD_TITLE'))
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['UpdateContractStatus'])
            ->setParamId($actionParamsIds['UpdateContractStatus']['PROCESS_STATUS_ID'])
            ->setParamValue($processStatusesIds[static::RENT_PS_AUDITOR_APPROVEMENT])
            ->save();
        //endregion

        //region финдиректор - "не согласовано"
        $triggerId = TaskStatusTrigger::createEmpty()
            ->setProcessId($processId)
            ->setResponsibleRoleId($finDirectorRoleId)
            ->setProcessStatusId($processStatusesIds[static::RENT_PS_FINANCIER_APPROVEMENT])
            ->setNewUfStatusId($linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_NOT_APPROVED])
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['UpdateContractStatus'])
            ->setParamId($actionParamsIds['UpdateContractStatus']['PROCESS_STATUS_ID'])
            ->setParamValue($processStatusesIds[ProcessStatus::STATUS_CODE_NOT_APPROVED])
            ->save();
        //endregion

        //region директор - "согласовано"
        $triggerId = TaskStatusTrigger::createEmpty()
            ->setProcessId($processId)
            ->setResponsibleRoleId($directorRoleId)
            ->setProcessStatusId($processStatusesIds[static::RENT_PS_HEAD_APPROVEMENT])
            ->setOldUfStatusId($linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_ACCEPTED])
            ->setNewUfStatusId($linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_APPROVED])
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['UpdateContractStatus'])
            ->setParamId($actionParamsIds['UpdateContractStatus']['PROCESS_STATUS_ID'])
            ->setParamValue($processStatusesIds[ProcessStatus::STATUS_CODE_APPROVED])
            ->save();
        //endregion

        //region директор - "не согласовано"
        $triggerId = TaskStatusTrigger::createEmpty()
            ->setProcessId($processId)
            ->setResponsibleRoleId($directorRoleId)
            ->setProcessStatusId($processStatusesIds[static::RENT_PS_HEAD_APPROVEMENT])
            ->setOldUfStatusId($linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_ACCEPTED])
            ->setNewUfStatusId($linearTStatusIds[Process::PARTICIPANT_TSTATUS_CODE_NOT_APPROVED])
            ->save();

        TriggerEffect::createEmpty($triggerTypeTask)
            ->setTriggerId($triggerId)
            ->setActionId($actionsIds['UpdateContractStatus'])
            ->setParamId($actionParamsIds['UpdateContractStatus']['PROCESS_STATUS_ID'])
            ->setParamValue($processStatusesIds[ProcessStatus::STATUS_CODE_NOT_APPROVED])
            ->save();
        //endregion
    }

    protected static function getTransitionMapKey($ttypeId, $srcStatusId, $destStatusId, $roleCode)
    {
        return (int)$ttypeId . '_' . (int)$srcStatusId . '_' . (int)$destStatusId . '_' . $roleCode;
    }
}