<?php namespace Intervolga\Sed\Entities;

use Intervolga\Sed\Tables\ParticipantTable;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Participant extends TableElement
{
    /**
     * @return \Bitrix\Main\Entity\Base
     */
    protected static function getEntity()
    {
        return ParticipantTable::getEntity();
    }


    /**
     * @param $value
     * @return $this
     */
    public function setContractId($value)
    {
        return $this->setFieldValue('CONTRACT_ID', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setRoleId($value)
    {
        return $this->setFieldValue('ROLE_ID', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setUserId($value)
    {
        return $this->setFieldValue('USER_ID', $value);
    }


    public function getContractId()
    {
        return $this->getFieldValue('CONTRACT_ID');
    }

    public function getRoleId()
    {
        return $this->getFieldValue('ROLE_ID');
    }

    public function getUserId()
    {
        return $this->getFieldValue('USER_ID');
    }

    /*
     * -----------------------------------------------
     * ---- Поля связанных таблиц (без alias'ов) -----
     * -----------------------------------------------
     */
    public function getReferenceRoleId()
    {
        return $this->getFieldValue('INTERVOLGA_SED_TABLES_PARTICIPANT_ROLE_ID');
    }

    public function getReferenceRoleName()
    {
        return $this->getFieldValue('INTERVOLGA_SED_TABLES_PARTICIPANT_ROLE_NAME');
    }

    public function getReferenceRoleIsInitiator()
    {
        return $this->getFieldValue('INTERVOLGA_SED_TABLES_PARTICIPANT_ROLE_IS_INITIATOR');
    }

    public function getReferenceTaskId()
    {
        return $this->getFieldValue('INTERVOLGA_SED_TABLES_PARTICIPANT_CONTRACT_TASK_TASK_ID');
    }

    public function getReferenceTaskRespRoleId()
    {
        return $this->getFieldValue('INTERVOLGA_SED_TABLES_PARTICIPANT_CONTRACT_TASK_RESP_ROLE_ID');
    }

    /**
     * Возвращает ID участников конкретного согласования.
     * @param $contractId
     * @param null $roleIdList
     * @return array
     */
    public static function getUserIdList($contractId, $roleIdList = null)
    {
        $userIdList = array();
        $filter = array(
            'CONTRACT_ID' => $contractId,
            '!ROLE_ID' => false,
            '!USER_ID' => false
        );

        if($roleIdList !== null){
            $filter['ROLE_ID'] = $roleIdList;
        }

        $participantList = Participant::getListByFilter($filter);
        foreach ($participantList as $participant) {
            $userIdList[$participant->getRoleId()] = $participant->getUserId();
        }

        return $userIdList;
    }

    public static function getAuditorsIdList($contractId, $excludeUserId = 0)
    {
        $contractUsersIds = static::getUserIdList($contractId);

        $auditorsIds = array();
        foreach($contractUsersIds as $uid){
            if($uid != $excludeUserId){
                $auditorsIds[] = $uid;
            }
        }

        return $auditorsIds;
    }
}