<?php namespace Intervolga\Sed\Tables;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class ProcessTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     * @return string
     */
    public static function getTableName()
    {
        return 'intervolga_sed_process';
    }

    public static function OnAfterAdd(\Bitrix\Main\Entity\Event $event)
    {
        $id = $event->getParameter("id");
        if((int)$id > 0) {
            // Создаем роль "Инициатор" для данного процесса согласования
            \Intervolga\Sed\Entities\ParticipantRole::createEmpty()
                ->setProcessId($id)
                ->setName(\Intervolga\Sed\Entities\ParticipantRole::getInitiatorRoleName())
                ->setInitiatorFlag(true)
                ->save();

            // Создаем статусы процесса согласования, которые присутствуют по умолчанию
            \Intervolga\Sed\Entities\ProcessStatus::createEmpty()
                ->setProcessId($id)
                ->setName(\Intervolga\Sed\Entities\ProcessStatus::getStatusName('STATUS_NAME_NEW'))
                ->setCode(\Intervolga\Sed\Entities\ProcessStatus::STATUS_CODE_NEW)
                ->save();

            \Intervolga\Sed\Entities\ProcessStatus::createEmpty()
                ->setProcessId($id)
                ->setName(\Intervolga\Sed\Entities\ProcessStatus::getStatusName('STATUS_NAME_PROGRESS'))
                ->setCode(\Intervolga\Sed\Entities\ProcessStatus::STATUS_CODE_PROGRESS)
                ->save();

            \Intervolga\Sed\Entities\ProcessStatus::createEmpty()
                ->setProcessId($id)
                ->setName(\Intervolga\Sed\Entities\ProcessStatus::getStatusName('STATUS_NAME_PAUSED'))
                ->setCode(\Intervolga\Sed\Entities\ProcessStatus::STATUS_CODE_PAUSED)
                ->save();

            \Intervolga\Sed\Entities\ProcessStatus::createEmpty()
                ->setProcessId($id)
                ->setName(\Intervolga\Sed\Entities\ProcessStatus::getStatusName('STATUS_NAME_APPROVED'))
                ->setCode(\Intervolga\Sed\Entities\ProcessStatus::STATUS_CODE_APPROVED)
                ->save();

            \Intervolga\Sed\Entities\ProcessStatus::createEmpty()
                ->setProcessId($id)
                ->setName(\Intervolga\Sed\Entities\ProcessStatus::getStatusName('STATUS_NAME_NOT_APPROVED'))
                ->setCode(\Intervolga\Sed\Entities\ProcessStatus::STATUS_CODE_NOT_APPROVED)
                ->save();

            // Создаем тип задач для инициатора процесса согласования
            try {
                \Intervolga\Sed\Entities\Process::createInitiatorTType($id);
            }
            catch (\Exception $e) {}
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
            $contracts = \Intervolga\Sed\Entities\Contract::getListByFilter(array('PROCESS_ID' => $processStatusId));

            if(is_array($contracts) && count($contracts)) {
                throw new \Bitrix\Main\InvalidOperationException(Loc::getMessage('SED.TABLES.ON_BEFORE_PROCESS_DELETE.CANT_DELETE'));
            }
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
        );
    }
}