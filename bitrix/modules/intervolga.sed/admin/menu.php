<?php
$menu = array();
$moduleId = basename(dirname(dirname(__FILE__)));

global $USER;
if ($USER->isAdmin() && \Bitrix\Main\Loader::includeModule($moduleId)) {

    $processes = \Intervolga\Sed\Entities\Process::getListAll();
    $roles = \Intervolga\Sed\Entities\ParticipantRole::getListAll();
    $statuses = \Intervolga\Sed\Entities\ProcessStatus::getListAll();
    $taskGroups = \Intervolga\Sed\Entities\ProcessTaskGroup::getListAll();
    
    $processTaskTypes = \Intervolga\Sed\Entities\ProcessTaskType::getListAll();
    $usedTTypesMap = array(); // [ttype_id => TaskTypeElement(ttype_id), ttype_id2 => TaskTypeElement(ttype_id2), ...]
    $usedTStatusesMap = array(); // [ttype_id => [tstatus_id => TaskStatusElement(tstatus_id), tstatus_id2 => TaskStatusElement(tstatus_id2), ...], ttype_id2 => ...]

    if(count($processTaskTypes)) {

        // ID типов задач, используемых при согласовании договоров
        $usedTTypeIds = array();
        foreach ($processTaskTypes as $processTType) {
            $usedTTypeIds[] = $processTType->getTaskTypeId();
        }

        $usedTTypes = \Intervolga\Sed\Entities\TaskTypeElement::getListByFilter(array('ID' => $usedTTypeIds));

        if(count($usedTTypes)) {

            $usedTTypesMap = \Intervolga\Sed\Entities\TaskTypeElement::makeIdsAsArrayKeys($usedTTypes);
            $usedTStatusFieldsNames = array();
            foreach ($usedTTypes as $ttype) {
                $usedTStatusFieldsNames[] = \Intervolga\Sed\Entities\TaskStatusField::getFieldNamePrefix() . $ttype->getId();
            }

            $usedTStatusFields = \Intervolga\Sed\Entities\TaskStatusField::getListByFilter(array('FIELD_NAME' => $usedTStatusFieldsNames));

            if(count($usedTStatusFields)) {
                $usedTStatusFields = \Intervolga\Sed\Entities\TaskStatusField::makeIdsAsArrayKeys($usedTStatusFields);
                $usedTStatuses = \Intervolga\Sed\Entities\TaskStatusElement::getListByFilter(array('USER_FIELD_ID' => array_keys($usedTStatusFields)), null, array(), false);

                foreach ($usedTStatuses as $TStatus) {
                    $TStatusFieldId = $TStatus->getUserFieldId();
                    $TTypeId = $usedTStatusFields[$TStatusFieldId]->getTaskTypeIdFromFieldName();
                    $usedTStatusesMap[$TTypeId][$TStatus->getId()] = $TStatus;
                }
            }
        }
    }

    $processRolesMap = array();
    $processStatusesMap = array();
    $processTaskGroupsMap = array();
    $processTaskTypesMap = array();

    foreach ($roles as $role) {
        $processRolesMap[$role->getProcessId()][] = $role;
    }

    foreach ($statuses as $status) {
        $processStatusesMap[$status->getProcessId()][] = $status;
    }

    foreach ($taskGroups as $group) {
        $processTaskGroupsMap[$group->getProcessId()][] = $group;
    }

    foreach ($processTaskTypes as $processTaskType) {
        $processTaskTypesMap[$processTaskType->getProcessId()][] = $processTaskType;
    }

    $menu = array(
        'parent_menu' => 'global_menu_services',
        'section' => $moduleId,
        'items_id' => $moduleId . '_process_items',
        'sort' => 25,
        'text' => GetMessage('INTERVOLGA_SED.MENU_NEGOTIATIONS'),
        'title' => GetMessage('INTERVOLGA_SED.MENU_NEGOTIATIONS'),
        'url' => '#',
        'icon' => 'adm-submenu-item-link-icon sale_menu_icon_buyers_affiliate'
    );

    $menu['items']['sed'] = array(
        'parent_menu' => 'global_menu_services',
        'section' => $moduleId,
        'items_id' => $moduleId . '_process_items',
        'sort' => 20,
        'text' => GetMessage('INTERVOLGA_SED.MENU_SETUP_NEGOTIATIONS'),
        'title' => GetMessage('INTERVOLGA_SED.MENU_SETUP_NEGOTIATIONS'),
        'url' => $moduleId . '_process_list.php',
        'list' => array()
    );

//    $menu['items']['cts'] = array(
//        'parent_menu' => 'global_menu_services',
//        'section' => $moduleId,
//        'items_id' => $moduleId . '_task_type_items',
//        'sort' => 20,
//        'text' => GetMessage('INTERVOLGA_SED.MENU_USER_STATUSES'),
//        'title' => GetMessage('INTERVOLGA_SED.MENU_USER_STATUSES'),
//        'url' => $moduleId . '_type_list.php'
//    );

    if(!empty($processes)) {

        $menu['items']['sed']['items'] = array();
        foreach ($processes as $process) {

            $processMenuItem = array(
                'parent_menu' => $moduleId,
                'section' => $moduleId . '_process' . $process->getId(),
                'items_id' => $moduleId . '_process_items' . $process->getId(),
                'text' => $process->getName(),
                'title' => $process->getName(),
                'url' => $moduleId . '_process_detail.php?PROCESS=' . $process->getId()
            );

            // раздел "Роли участников"
            $roleMenu = array(
                'parent_menu' => $moduleId . '_process' . $process->getId(),
                'section' => $moduleId . '_process' . $process->getId() . '_roles',
                'items_id' => $moduleId . '_process' . $process->getId() . '_roles_items',
                'text' => GetMessage('INTERVOLGA_SED.MENU_PARTICIPANTS_ROLES'),
                'title' => GetMessage('INTERVOLGA_SED.MENU_PARTICIPANTS_ROLES'),
                'url' => $moduleId . '_role_list.php?PROCESS=' . $process->getId(),
                'items' => array()
            );

            if(!empty($processRolesMap[$process->getId()])) {
                foreach ($processRolesMap[$process->getId()] as $role) {
                    /** @var \Intervolga\Sed\Entities\ParticipantRole $role */
                    $roleMenu['items'][] = array(
                        'text' => $role->getName(),
                        'title' => $role->getName(),
                        'url' => $moduleId . '_role_detail.php?PROCESS=' . $process->getId() . '&ROLE=' . $role->getId(),
                    );
                }
            }

            // раздел "Статусы маршрута"
            $statusMenu = array(
                'parent_menu' => $moduleId . '_process' . $process->getId(),
                'section' => $moduleId . '_process' . $process->getId() . '_statuses',
                'items_id' => $moduleId . '_process' . $process->getId() . '_statuses_items',
                'text' => GetMessage('INTERVOLGA_SED.MENU_ROUTE_STATUSES'),
                'title' => GetMessage('INTERVOLGA_SED.MENU_ROUTE_STATUSES'),
                'url' => $moduleId . '_status_list.php?PROCESS=' . $process->getId(),
                'items' => array()
            );

            if(!empty($processStatusesMap[$process->getId()])) {
                foreach ($processStatusesMap[$process->getId()] as $status) {
                    /** @var \Intervolga\Sed\Entities\ProcessStatus $status */
                    $statusMenu['items'][] = array(
                        'text' => $status->getName(),
                        'title' => $status->getName(),
                        'url' => $moduleId . '_status_detail.php?PROCESS=' . $process->getId() . '&STATUS=' . $status->getId(),
                    );
                }
            }

            // раздел "Используемые типы задач"
            $usedTTypesContainerMenu = array(
                'parent_menu' => $moduleId . '_process' . $process->getId(),
                'section' => $moduleId . '_process' . $process->getId() . '_used_ttypes_container',
                'items_id' => $moduleId . '_process' . $process->getId() . '_used_ttypes_container_items',
                'text' => GetMessage('INTERVOLGA_SED.MENU_USED_TASK_TYPES'),
                'title' => GetMessage('INTERVOLGA_SED.MENU_USED_TASK_TYPES'),
                //                'url' => $moduleId . '_ttype_list.php?PROCESS=' . $process->getId(),
            );

            $usedTTypesMenu = array(
                'parent_menu' => $moduleId . '_process' . $process->getId() . '_used_ttypes_container',
                'section' => $moduleId . '_process' . $process->getId() . '_used_ttypes',
                'items_id' => $moduleId . '_process' . $process->getId() . '_used_ttypes_items',
                'text' => GetMessage('INTERVOLGA_SED.MENU_TASK_TYPES'),
                'title' => GetMessage('INTERVOLGA_SED.MENU_TASK_TYPES'),
                'url' => $moduleId . '_ttype_list.php?PROCESS=' . $process->getId()
            );

            $transitionsMenu = array(
                'text' => GetMessage('INTERVOLGA_SED.MENU_TASK_STATUSES_TRANS'),
                'title' => GetMessage('INTERVOLGA_SED.MENU_TASK_STATUSES_TRANS'),
                'url' => $moduleId . '_transition_list.php?PROCESS=' . $process->getId()
            );

            if(!empty($processTaskTypesMap)) {
                foreach ($processTaskTypesMap[$process->getId()] as $processTaskType) {
                    /** @var \Intervolga\Sed\Entities\ProcessTaskType $processTaskType */
                    if($usedTTypesMap[$processTaskType->getTaskTypeId()] instanceof \Intervolga\Sed\Entities\TaskTypeElement) {
                        $taskType = $usedTTypesMap[$processTaskType->getTaskTypeId()];

                        $usedTTypeMenuItem = array(
                            'parent_menu' => $moduleId . '_process' . $process->getId() . '_used_ttypes',
                            'section' => $moduleId . '_process' . $process->getId() . '_used_ttypes' . $taskType->getId(),
                            'items_id' => $moduleId . '_process' . $process->getId() . '_task_status_items' . $taskType->getId(),
                            'text' => $taskType->getName(),
                            'title' => $taskType->getName(),
                            'url' => $moduleId . '_c_status_list.php?TYPE=' . $taskType->getId(),
                        );

                        if(!empty($usedTStatusesMap[$taskType->getId()])) {
                            foreach ($usedTStatusesMap[$taskType->getId()] as $taskStatus) {
                                /** @var \Intervolga\Sed\Entities\TaskStatusElement $taskStatus */
                                $usedTTypeMenuItem['items'][] = array(
                                    'parent_menu' => $moduleId . '_process' . $process->getId() . '_used_ttypes' . $taskType->getId(),
                                    'text' => $taskStatus->getName(),
                                    'title' => $taskStatus->getName(),
                                    'url' => $moduleId . '_c_status_detail.php?TYPE=' . $taskType->getId() . '&STATUS=' . $taskStatus->getId(),
                                );
                            }
                        }

                        $usedTTypesMenu['items'][] = $usedTTypeMenuItem;
                        unset($taskType);
                    }
                }
            }

            $usedTTypesContainerMenu['items'] = array($usedTTypesMenu, $transitionsMenu);


            // раздел "Группы задач"
            $taskGroupMenu = array(
                'parent_menu' => $moduleId . '_process' . $process->getId(),
                'section' => $moduleId . '_process' . $process->getId() . '_task_groups',
                'items_id' => $moduleId . '_process' . $process->getId() . '_task_groups_items',
                'text' => GetMessage('INTERVOLGA_SED.MENU_TASK_GROUPS'),
                'title' => GetMessage('INTERVOLGA_SED.MENU_TASK_GROUPS'),
                'url' => $moduleId . '_task_group_list.php?PROCESS=' . $process->getId(),
                'items' => array()
            );

            if(!empty($processTaskGroups[$process->getId()])) {
                foreach ($processTaskGroupsMap[$process->getId()] as $group) {
                    /** @var \Intervolga\Sed\Entities\ProcessTaskGroup $group */
                    $taskGroupMenu['items'][] = array(
                        'text' => $group->getName(),
                        'title' => $group->getName(),
                        'url' => $moduleId . '_task_group_detail.php?PROCESS=' . $process->getId() . '&GROUP=' . $group->getId(),
                    );
                }
            }

            // раздел "Обработчики"
            $triggerMenu = array(
                'parent_menu' => $moduleId . '_process' . $process->getId(),
                'section' => $moduleId . '_process' . $process->getId() . '_triggers',
                'items_id' => $moduleId . '_process' . $process->getId() . '_triggers_items',
                'text' => GetMessage('INTERVOLGA_SED.MENU_HANDLERS'),
                'title' => GetMessage('INTERVOLGA_SED.MENU_HANDLERS'),
                'items' => array()
            );

            $taskTriggerMenu = array(
                'parent_menu' => $moduleId . '_process' . $process->getId() . '_triggers',
                'section' => $moduleId . '_process' . $process->getId() . '_task_triggers',
                'items_id' => $moduleId . '_process' . $process->getId() . '_task_triggers_items',
                'text' => GetMessage('INTERVOLGA_SED.MENU_STATUS_CHANGE'),
                'title' => GetMessage('INTERVOLGA_SED.MENU_STATUS_CHANGE'),
                'url' => $moduleId . '_task_trigger_list.php?PROCESS=' . $process->getId(),
                'items' => array()
            );

            $taskGroupTriggerMenu = array(
                'parent_menu' => $moduleId . '_process' . $process->getId() . '_triggers',
                'section' => $moduleId . '_process' . $process->getId() . '_task_group_triggers',
                'items_id' => $moduleId . '_process' . $process->getId() . '_task_group_triggers_items',
                'text' => GetMessage('INTERVOLGA_SED.MENU_GROUP_STATUS_CHANGE'),
                'title' => GetMessage('INTERVOLGA_SED.MENU_GROUP_STATUS_CHANGE'),
                'url' => $moduleId . '_task_group_trigger_list.php?PROCESS=' . $process->getId(),
                'items' => array()
            );

            $triggerMenu['items'] = array($taskTriggerMenu, $taskGroupTriggerMenu);

            $userFields = array(
                'parent_menu' => $moduleId . '_process' . $process->getId(),
                'section' => $moduleId . '_process' . $process->getId() . '_task_groups',
                'items_id' => $moduleId . '_process' . $process->getId() . '_task_groups_items',
                'text' => GetMessage('INTERVOLGA_SED.MENU_CONTRACT_USER_FIELDS'),
                'title' => GetMessage('INTERVOLGA_SED.MENU_CONTRACT_USER_FIELDS'),
                'url' => $moduleId . '_contract_user_fields.php?PROCESS=' . $process->getId(),
                'items' => array()
            );

            $processMenuItem['items'] = array(
                $roleMenu,
                $statusMenu,
                $usedTTypesContainerMenu,
                $taskGroupMenu,
                $triggerMenu,
                $userFields
            );
            $menu['items']['sed']['items'][] = $processMenuItem;
        }
    }

//    $taskTypes = \Intervolga\Sed\Entities\TaskTypeElement::getListAll();
//
//    if(!empty($taskTypes)) {
//        $menu['items']['cts']['items'] = array();
//        foreach($taskTypes as $taskType) {
//
//            $ttypeMenuItem = array(
//                'parent_menu' => $moduleId,
//                'section' => $moduleId . '_task_type' . $taskType->getId(),
//                'items_id' => $moduleId . '_task_status_items' . $taskType->getId(),
//                'text' => $taskType->getName(),
//                'title' => $taskType->getName(),
//                'url' => $moduleId . '_c_status_list.php?TYPE=' . $taskType->getId(),
//            );
//
//            $taskStatuses = \Intervolga\Sed\Entities\TaskStatusElement::getListAll($taskType);
//            if(!empty($taskStatuses)) {
//                $ttypeMenuItem['items'] = array();
//                foreach($taskStatuses as $taskStatus) {
//                    $ttypeMenuItem['items'][] = array(
//                        'text' => $taskStatus->getName(),
//                        'title' => $taskStatus->getName(),
//                        'url' => $moduleId . '_c_status_detail.php?TYPE=' . $taskType->getId() . '&STATUS=' . $taskStatus->getId(),
//                    );
//                }
//            }
//
//            $menu['items']['cts']['items'][] = $ttypeMenuItem;
//        }
//    }
}

return $menu;