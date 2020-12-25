<?php namespace Intervolga\Sed\Tables;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class ContractTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     * @return string
     */
    public static function getTableName()
    {
        return 'intervolga_sed_contract';
    }

    public static function getUfId()
    {
        return 'SED_CONTRACT';
    }

    public static function onBeforeUpdate(\Bitrix\Main\Entity\Event $event)
    {
        $result = new \Bitrix\Main\Entity\EventResult();
        $data = $event->getParameter("fields");

        $data['DATE_UPDATE'] = new \Bitrix\Main\Type\DateTime();
        $result->modifyFields($data);

        try {
            $arContractId = $event->getParameter("id");
            \Intervolga\Sed\Tools\Handler::onContractUpdate($arContractId['ID'], $data);
        }
        catch (\Exception $e) {}

        return $result;
    }

    public static function OnBeforeDelete(\Bitrix\Main\Entity\Event $event)
    {
        $parameters = $event->getParameters();
        $contractId = $parameters['id']['ID'];

        if($contractId) {
            try {
                $contract = \Intervolga\Sed\Entities\Contract::getOneByFilter(
                    array('ID' => $contractId),
                    array(),
                    array('PROCESS_STATUS.CODE')
                );
                if($contract->cantBeDeleted()) {
                    throw new \Bitrix\Main\InvalidOperationException(Loc::getMessage('SED.TABLES.ON_BEFORE_CONTRACT_DELETE.WRONG_PROCESS_STATUS'));
                }
            }
            catch (\Bitrix\Main\ObjectNotFoundException $e) {}
        }
    }

    public static function OnAfterDelete(\Bitrix\Main\Entity\Event $event)
    {
        $parameters = $event->getParameters();
        $contractId = $parameters['id']['ID'];

        if($contractId) {
            \Intervolga\Sed\Entities\Participant::removeByFilter(array('CONTRACT_ID' => $contractId));
            \Intervolga\Sed\Entities\ContractTask::removeByFilter(array('CONTRACT_ID' => $contractId));
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
            new Entity\StringField('NAME', array('size' => 255)),
            new Entity\IntegerField('PROCESS_STATUS_ID'),
            new Entity\IntegerField('PROCESS_ID', array('required' => true)),
            new Entity\IntegerField('FILE_ID'),
            new \Bitrix\Main\Entity\DatetimeField('DATE_CREATE', array(
                'default_value' => function () {
                    return new \Bitrix\Main\Type\DateTime();
                }
            )),
            new \Bitrix\Main\Entity\DatetimeField('DATE_UPDATE', array(
                'default_value' => function () {
                    return new \Bitrix\Main\Type\DateTime();
                }
            )),
            new Entity\IntegerField('DAYS_TO_HARMONIZE', array('required' => true)),
            new \Bitrix\Main\Entity\ReferenceField(
                'PROCESS_STATUS',
                'Intervolga\Sed\Tables\ProcessStatusTable',
                array('=this.PROCESS_STATUS_ID' => 'ref.ID'),
                array('join_type' => 'LEFT')
            ),
            new \Bitrix\Main\Entity\ReferenceField(
                'PROCESS',
                'Intervolga\Sed\Tables\ProcessTable',
                array('=this.PROCESS_ID' => 'ref.ID'),
                array('join_type' => 'LEFT')
            ),

            new \Bitrix\Main\Entity\ReferenceField(
                'TASK',
                'Intervolga\Sed\Tables\ContractTaskTable',
                array('=this.ID' => 'ref.CONTRACT_ID'),
                array('join_type' => 'RIGHT')
            ),
            new \Bitrix\Main\Entity\ReferenceField(
                'PARTICIPANT',
                'Intervolga\Sed\Tables\ParticipantTable',
                array('=this.ID' => 'ref.CONTRACT_ID'),
                array('join_type' => 'RIGHT')
            ),
            new \Bitrix\Main\Entity\ReferenceField(
                'PARTICIPANT_ROLE',
                'Intervolga\Sed\Tables\ParticipantRoleTable',
                array('=this.PROCESS_ID' => 'ref.PROCESS_ID'),
                array('join_type' => 'LEFT')
            ),
        );
    }
}