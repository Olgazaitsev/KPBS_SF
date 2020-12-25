<?php
namespace intervolga\sed\handler;


use Bitrix\Main\Loader;
use Bitrix\Main\ObjectNotFoundException;
use Bitrix\Tasks\Item\Task;
use Bitrix\Tasks\Util\User;
use Intervolga\Sed\Entities\Action;
use Intervolga\Sed\Entities\Contract;
use Intervolga\Sed\Entities\ContractTask;
use Intervolga\Sed\Entities\Participant;


/**
 * Класс обработчика делегирования задачи.
 *
 * Class TaskDelegateListener
 *
 * @package intervolga\sed\handler
 */
class TaskDelegateListener {
    /**
     * Метод обработчика события tasks:OnBeforeTaskUpdate, меняет пользователя,
     * закрепленного за ролью в согласовании, на нового ответственного по задаче.
     *
     * @param $ID - id задачи.
     * @param $arFields - массив изменненных полей задачи.
     * @param $arTaskCopy - массив полей задачи до изменений.
     *
     * @throws ObjectNotFoundException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    public static function assignNewUserToRoleOnTaskDelegate($ID, &$arFields, &$arTaskCopy) {
        static $bSkipDelegation = false;
        if ($bSkipDelegation || Action::getExecutingAction() === 'UpdateTask') {
            return;
        }
        $bSkipDelegation = true;

        if (!Loader::includeModule('tasks')) {
            $bSkipDelegation = false;
            return;
        }

        try {
            $contractTask = ContractTask::getOneByFilter(array(
                    'TASK_ID' => $ID
            ));
        } catch (ObjectNotFoundException $e) {
            $bSkipDelegation = false;
            return;
        }

        $contract = Contract::getById($contractTask->getContractId());
        $isResponsibleIdChanged =
                array_key_exists('RESPONSIBLE_ID', $arFields)
                && $arFields['RESPONSIBLE_ID'] != $arTaskCopy['RESPONSIBLE_ID'];
        if (!$isResponsibleIdChanged) {
            $bSkipDelegation = false;
            return;
        }

        if (array_key_exists('STATUS', $arFields) && $arFields['STATUS'] == \CTasks::STATE_PENDING) {
            // The pending state is setting automatically by bitrix, so cancel it, due to restriction to change
            // contract task status manually(without changing SED's custom status)
            unset($arFields['STATUS']);
        }

        $newResponsibleParticipationCount = Participant::getCountByFilter(array(
                'CONTRACT_ID' => $contract->getId(),
                'USER_ID' => $arFields['RESPONSIBLE_ID']
        ));

        $participant = Participant::getOneByFilter(array(
                'CONTRACT_ID' => $contract->getId(),
                'ROLE_ID' => $contractTask->getResponsibleRoleId()
        ));
        $participant->setUserId($arFields['RESPONSIBLE_ID'])->save();

        $taskIds = ContractTask::getTaskIdsByFilter(array(
                'CONTRACT_ID' => $contract->getId(),
                'RESP_ROLE_ID' => $contractTask->getResponsibleRoleId()
        ));
        if (($key = array_search($ID, $taskIds)) !== false) {
            unset($taskIds[$key]);
        }
        foreach ($taskIds as $taskId) {
            $task = new Task($taskId);
            $task['RESPONSIBLE_ID'] = $arFields['RESPONSIBLE_ID'];

            /*
             * Из-за специфической логики в \Bitrix\Tasks\Item\Task, попытка выполнить $task->save()
             * без параметра SE_MEMBER приводит к выбросу исключения.
             *
             * TODO: исправить этот хотфикс. Сейчас в метод обновления, не смотря на передачу ответственного, отлетает пустая коллекция членов.
             */
            $task['SE_MEMBER'] = $arFields['RESPONSIBLE_ID'];
            $task->save();
        }

        $prevResponsibleParticipationCount = Participant::getCountByFilter(array(
                'CONTRACT_ID' => $contract->getId(),
                'USER_ID' => $arTaskCopy['RESPONSIBLE_ID']
        ));
        if ($prevResponsibleParticipationCount && $newResponsibleParticipationCount) {
            $bSkipDelegation = false;
            return;
        }

        $taskIds = ContractTask::getTaskIdsByFilter(array(
                'CONTRACT_ID' => $contract->getId()
        ));
        foreach ($taskIds as $taskId) {
            $taskItem = new \CTaskItem($taskId, User::getAdminId());
            $taskData = $taskItem->getData();
            if (!$prevResponsibleParticipationCount) {
                if (($key = array_search($arTaskCopy['RESPONSIBLE_ID'], $taskData['AUDITORS'])) !== false) {
                    unset($taskData['AUDITORS'][$key]);
                }
            }
            if (!$newResponsibleParticipationCount) {
                $taskData['AUDITORS'][] = $arFields['RESPONSIBLE_ID'];
            }
            $taskItem->update(array(
                    'AUDITORS' => $taskData['AUDITORS']
            ));
        }

        $bSkipDelegation = false;
    }
}
