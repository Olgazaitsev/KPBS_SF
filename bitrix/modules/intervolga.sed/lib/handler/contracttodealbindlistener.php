<?php
namespace intervolga\sed\handler;


use Bitrix\Main\Event;
use Intervolga\Sed\Entities\ProcessStatus;
use intervolga\sed\service\ContractToDealBinder;


/**
 * Класс обработчика событий, при которых нужно совершать манипуляции с прикрепленной к согласованию сделкой.
 *
 * Class ContractFileToDealBindListener
 *
 * @package intervolga\sed\handler
 */
class ContractToDealBindListener {
    /**
     * Обработчик события \Intervolga\Sed\Tables\Contract::OnAfterAdd,
     * отправляет комментарий в прикрепленную к согласованию сделке о его создании.
     *
     * @param Event $event - объект события.
     *
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @throws \Bitrix\Main\ObjectNotFoundException
     */
    public static function onAfterContractAdd(Event $event) {
        $contractId = $event->getParameter('id');
        $contractFileToDealBinderInstance = new ContractToDealBinder($contractId);
        $contractFileToDealBinderInstance->addCommentToDeal();
    }

    /**
     * Обработчик события \Intervolga\Sed\Tables\Contract::OnUpdate,
     * прикрепляет к сделке файл договора и создает об этом комментарий.
     *
     * @param Event $event - объект события.
     */
    public static function onAfterContractUpdate(Event $event) {
        $contractId = $event->getParameter('id')['ID'];
        $contractFieldList = $event->getParameter('fields');
        $newProcessStatusId = $contractFieldList['PROCESS_STATUS_ID'];
        if (empty($newProcessStatusId)) {
            return;
        }

        try {
            $newProcessStatus = ProcessStatus::getById($newProcessStatusId);
            if ($newProcessStatus->getCode() !== ProcessStatus::STATUS_CODE_APPROVED) {
                return;
            }

            $contractFileToDealBinderInstance = new ContractToDealBinder($contractId);
            $contractFileToDealBinderInstance->attachContractFileToDeal();
        } catch (\Exception $exception) {
            // В данном случае исключение не выбрасывается дальше, а сохраняется в $APPLICATION,
            // так как событие вызывется при событии изменении задачи битрикса, исключения который не выбрасываются,
            // а только логируются. По этой причине дальше после обновления задачи идет проверка на наличие исключений
            // и выбрасывание его вновь.
            global $APPLICATION;
            $APPLICATION->ThrowException($exception->getMessage());
        }
    }
}