<?php namespace Intervolga\Sed\Tables;

use Bitrix\Main\Entity;
use Intervolga\Sed\Entities\TaskTypeElement;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class ProcessTaskTypeTable extends Entity\DataManager
{
    protected static $elementHasJustBeenSuccessfullyRemoved = array();


    /**
     * Returns DB table name for entity.
     * @return string
     */
    public static function getTableName()
    {
        return 'intervolga_sed_process_task_type';
    }

    /**
     * Если вернет "true", значит все проверки для удаления ТЗ уже произведены
     * @param int $ttypeId
     * @return bool
     */
    public static function hasElementJustBeenSuccessfullyRemoved($ttypeId)
    {
        return (static::$elementHasJustBeenSuccessfullyRemoved[$ttypeId] === true);
    }

    /**
     * @param Entity\Event $event
     * @throws \Bitrix\Main\InvalidOperationException
     */
    public static function OnBeforeDelete(\Bitrix\Main\Entity\Event $event)
    {
        $parameters = $event->getParameters();
        $processTaskTypeId = $parameters['primary']['ID'];

        if($processTaskTypeId) {
            try {
                $processTaskType = \Intervolga\Sed\Entities\ProcessTaskType::getOneByFilter(
                    array('ID' => $processTaskTypeId, '!PROCESS.ID' => false),
                    array(),
                    array('PROCESS.ID')
                );

                $taskType = TaskTypeElement::getById($processTaskType->getTaskTypeId());

                // запрещаем удалять свзяь с типом задачи "Для инициатора"
                if($taskType->getXmlId() == \Intervolga\Sed\Entities\Process::INITIATOR_TTYPE_CODE) {
                    throw new \Bitrix\Main\InvalidOperationException(Loc::getMessage('SED.TABLES.ON_BEFORE_PROCESS_TTYPE_DELETE.INITIATOR_TTYPE'));
                }

                // запрещаем удаление, если ТЗ используется в обработчиках
                if(\Intervolga\Sed\Entities\ProcessTaskType::isUsedInTriggerEffects($processTaskType->getTaskTypeId())) {
                    throw new \Bitrix\Main\InvalidOperationException(Loc::getMessage('SED.TABLES.ON_BEFORE_PROCESS_TTYPE_DELETE.USED_IN_TRIGGERS'));
                }

                // проверяем на наличие незавершенных задач
                \Intervolga\Sed\Tools\Utils::checkIncompleteTasks($processTaskType->getTaskTypeId());

                // ошибок нет - устанавливаем флаг об успешном прохождении проверок
                static::$elementHasJustBeenSuccessfullyRemoved[$processTaskType->getTaskTypeId()] = true;
            }
            catch (\Bitrix\Main\ObjectNotFoundException $e) {}
        }
    }

    /**
     * Returns entity map definition.
     * @return array
     */
    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array('primary' => true, 'autocomplete' => true)),
            new Entity\IntegerField('PROCESS_ID', array('required' => true)),
            new Entity\IntegerField('TASK_TYPE_ID', array('required' => true)),
            new \Bitrix\Main\Entity\ReferenceField(
                'PROCESS',
                'Intervolga\Sed\Tables\ProcessTable',
                array('=this.PROCESS_ID' => 'ref.ID'),
                array('join_type' => 'LEFT')
            )
        );
    }
}