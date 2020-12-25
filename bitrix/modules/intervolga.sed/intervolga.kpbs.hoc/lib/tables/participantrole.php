<?php namespace Intervolga\Sed\Tables;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class ParticipantRoleTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     * @return string
     */
    public static function getTableName()
    {
        return 'intervolga_sed_participant_role';
    }

    /**
     * @param Entity\Event $event
     * @throws \Bitrix\Main\InvalidOperationException
     */
    public static function OnBeforeDelete(\Bitrix\Main\Entity\Event $event)
    {
        $parameters = $event->getParameters();
        $roleId = $parameters['primary']['ID'];

        if($roleId) {
            try {
                $role = \Intervolga\Sed\Entities\ParticipantRole::getOneByFilter(
                    array('ID' => $roleId, '!PROCESS.ID' => false),
                    array(),
                    array('PROCESS.ID')
                );

                if($role->isInitiator()) {
                    throw new \Bitrix\Main\InvalidOperationException(Loc::getMessage('SED.TABLES.ON_BEFORE_ROLE_DELETE.INITIATOR'));
                }
                elseif(
                    \Intervolga\Sed\Entities\ParticipantRole::isUsedInTaskStatusTriggers($roleId) ||
                    \Intervolga\Sed\Entities\ParticipantRole::isUsedInTaskGroupStatusTriggers($roleId) ||
                    \Intervolga\Sed\Entities\ParticipantRole::isUsedInTriggerEffects($roleId)
                ) {
                    throw new \Bitrix\Main\InvalidOperationException(Loc::getMessage('SED.TABLES.ON_BEFORE_ROLE_DELETE.TASK_STATUS_TRIGGER'));
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
            new Entity\StringField('NAME', array('size' => 255, 'required' => true)),
            new Entity\IntegerField('PROCESS_ID', array('required' => true)),
            new Entity\IntegerField('DEFAULT_USER_ID'),
            new Entity\IntegerField('IS_INITIATOR'),
            new \Bitrix\Main\Entity\ReferenceField(
                'PROCESS',
                'Intervolga\Sed\Tables\ProcessTable',
                array('=this.PROCESS_ID' => 'ref.ID'),
                array('join_type' => 'LEFT')
            ),
            new \Bitrix\Main\Entity\ReferenceField(
                'PARICIPANT',
                'Intervolga\Sed\Tables\ParticipantTable',
                array('=this.ID' => 'ref.ROLE_ID'),
                array('join_type' => 'LEFT')
            ),
            new \Bitrix\Main\Entity\ReferenceField(
                'TASK',
                'Intervolga\Sed\Tables\ContractTaskTable',
                array('=this.ID' => 'ref.RESP_ROLE_ID'),
                array('join_type' => 'LEFT')
            ),
        );
    }
}