<?php namespace Intervolga\Sed\Tables;

use Bitrix\Main\DB\Exception;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class ProcessStatusTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     * @return string
     */
    public static function getTableName()
    {
        return 'intervolga_sed_process_status';
    }

    /**
     * @param Entity\Event $event
     * @throws \Bitrix\Main\InvalidOperationException
     */
    public static function OnBeforeUpdate(\Bitrix\Main\Entity\Event $event)
    {
        $processStatusId = $event->getParameter('id');
        $fields = $event->getParameter('fields');

        if($processStatusId) {
            try {
                $oldProcessStatus = \Intervolga\Sed\Entities\ProcessStatus::getById($processStatusId);
                // запрещаем изменять символьный код стандартных статусов
                if(\Intervolga\Sed\Entities\ProcessStatus::isDefault($oldProcessStatus->getCode()) && ($oldProcessStatus->getCode() != $fields['CODE'])) {
                    throw new \Bitrix\Main\InvalidOperationException(Loc::getMessage('SED.TABLES.ON_BEFORE_PROCESS_STATUS_UPDATE.DEFAULT_STATUS'));
                }
            }
            catch (\Bitrix\Main\ObjectNotFoundException $e) {}
        }
    }

    /**
     * @param Entity\Event $event
     * @throws \Bitrix\Main\InvalidOperationException
     */
    public static function OnBeforeDelete(\Bitrix\Main\Entity\Event $event)
    {
        $parameters = $event->getParameters();
        $processStatusId = $parameters['primary']['ID'];

        if($processStatusId) {
            try {
                $status = \Intervolga\Sed\Entities\ProcessStatus::getOneByFilter(
                    array('ID' => $processStatusId, '!PROCESS.ID' => false),
                    array(),
                    array('PROCESS.ID')
                );

                if(\Intervolga\Sed\Entities\ProcessStatus::isDefault($status->getCode())) {
                    throw new \Bitrix\Main\InvalidOperationException(Loc::getMessage('SED.TABLES.ON_BEFORE_PROCESS_STATUS_DELETE.DEFAULT_STATUS'));
                }
                elseif(
                    \Intervolga\Sed\Entities\ProcessStatus::isUsedInContractStatusTriggers($processStatusId) ||
                    \Intervolga\Sed\Entities\ProcessStatus::isUSedInTaskStatusTriggers($processStatusId) ||
                    \Intervolga\Sed\Entities\ProcessStatus::isUsedInTaskGroupStatusTriggers($processStatusId) ||
                    \Intervolga\Sed\Entities\ProcessStatus::isUSedInTriggerEffects($processStatusId)
                ) {
                    throw new \Bitrix\Main\InvalidOperationException(Loc::getMessage('SED.TABLES.ON_BEFORE_PROCESS_STATUS_DELETE.USED_IN_TRIGGERS'));
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
            new Entity\StringField('NAME', array('size' => 255)),
            new Entity\IntegerField('PROCESS_ID', array('required' => true)),
            new Entity\StringField('CODE', array('size' => 255, 'required' => true)),
            new \Bitrix\Main\Entity\ReferenceField(
                'PROCESS',
                'Intervolga\Sed\Tables\ProcessTable',
                array('=this.PROCESS_ID' => 'ref.ID'),
                array('join_type' => 'LEFT')
            ),
        );
    }
}