<?php namespace Intervolga\Sed\Tables;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class TaskGroupStatusTriggerTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     * @return string
     */
    public static function getTableName()
    {
        return 'intervolga_sed_task_group_status_trigger';
    }

    /**
     * @param Entity\Event $event
     * @throws \Bitrix\Main\InvalidOperationException
     */
    public static function OnBeforeDelete(\Bitrix\Main\Entity\Event $event)
    {
        $parameters = $event->getParameters();
        $triggerId = $parameters['primary']['ID'];

        if($triggerId) {
            try {
                $trigger = \Intervolga\Sed\Entities\TaskGroupStatusTrigger::getById($triggerId);
                $contracts = \Intervolga\Sed\Entities\Contract::getListByFilter(array('PROCESS_ID' => $trigger->getProcessId()));

                if(!empty($contracts)) {
                    throw new \Bitrix\Main\InvalidOperationException(Loc::getMessage('SED.TABLES.ON_BEFORE_GROUP_TRIGGER_DELETE.USED_BY_CONTRACT'));
                }
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
            new Entity\IntegerField('GROUP_ID', array('required' => true)),
            new Entity\IntegerField('PROCESS_STATUS_ID', array('required' => true)),
            new Entity\IntegerField('ORIGINATOR_ROLE_ID'),
            new Entity\IntegerField('ALL_IN_STATUS'), // статус, в котором должны находиться все задачи
            new Entity\StringField('ALL_OUT_OF_STATUSES', array('size' => 255)), // все задачи находятся в статусах, отличных от данных
            new Entity\IntegerField('ANYONE_IN_STATUS'), // статус, в котором должна быть хотя бы одна задача
            new Entity\StringField('ANYONE_OUT_OF_STATUSES', array('size' => 255)), // хотя бы одна задача должна находиться в статусе, отличном от данных
        );
    }
}