<?php namespace Intervolga\Sed\Tables;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class ProcessTaskGroupTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     * @return string
     */
    public static function getTableName()
    {
        return 'intervolga_sed_process_task_group';
    }

    /**
     * @param Entity\Event $event
     * @throws \Bitrix\Main\InvalidOperationException
     */
    public static function OnBeforeDelete(\Bitrix\Main\Entity\Event $event)
    {
        $parameters = $event->getParameters();
        $groupId = $parameters['primary']['ID'];

        if($groupId) {
            try {
                $processGroup = \Intervolga\Sed\Entities\ProcessTaskGroup::getOneByFilter(
                    array('ID' => $groupId, '!PROCESS.ID' => false),
                    array(),
                    array('PROCESS.ID')
                );

                if(
                    \Intervolga\Sed\Entities\ProcessTaskGroup::isUsedInTaskGroupStatusTriggers($groupId) ||
                    \Intervolga\Sed\Entities\ProcessTaskGroup::isUsedInTriggerEffects($groupId)
                ) {
                    throw new \Bitrix\Main\InvalidOperationException(Loc::getMessage('SED.TABLES.ON_BEFORE_PROCESS_TASK_GROUP_DELETE.USED_IN_TRIGGERS'));
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
            new Entity\StringField('NAME', array('size' => 255, 'required' => true)),
            new \Bitrix\Main\Entity\ReferenceField(
                'PROCESS',
                'Intervolga\Sed\Tables\ProcessTable',
                array('=this.PROCESS_ID' => 'ref.ID'),
                array('join_type' => 'LEFT')
            )
        );
    }
}