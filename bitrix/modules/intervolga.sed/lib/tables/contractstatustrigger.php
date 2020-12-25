<?php namespace Intervolga\Sed\Tables;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class ContractStatusTriggerTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     * @return string
     */
    public static function getTableName()
    {
        return 'intervolga_sed_contract_status_trigger';
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
                $trigger = \Intervolga\Sed\Entities\ContractStatusTrigger::getById($triggerId);
                $contracts = \Intervolga\Sed\Entities\Contract::getListByFilter(array('PROCESS_ID' => $trigger->getProcessId()));

                if(!empty($contracts)) {
                    throw new \Bitrix\Main\InvalidOperationException(Loc::getMessage('SED.TABLES.ON_BEFORE_CONTRACT_TRIGGER_DELETE.USED_BY_CONTRACT'));
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
            new Entity\IntegerField('OLD_PROCESS_STATUS_ID'),
            new Entity\IntegerField('NEW_PROCESS_STATUS_ID'),
            new \Bitrix\Main\Entity\ReferenceField(
                'PROCESS',
                'Intervolga\Sed\Tables\ProcessTable',
                array('=this.PROCESS_ID' => 'ref.ID'),
                array('join_type' => 'LEFT')
            ),
        );
    }
}