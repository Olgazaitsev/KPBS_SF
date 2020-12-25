<?php namespace Intervolga\Sed\Entities;

use Intervolga\Sed\Tables\TaskGroupStatusTriggerTable;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class TaskGroupStatusTrigger extends AbstractTrigger
{
    /**
     * @return \Bitrix\Main\Entity\Base
     */
    protected static function getEntity()
    {
        return TaskGroupStatusTriggerTable::getEntity();
    }

    public static function getType()
    {
        return 'TASK_GROUP';
    }


    /**
     * @param $value
     * @return $this
     */
    public function setGroupId($value)
    {
        return $this->setFieldValue('GROUP_ID', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setProcessStatusId($value)
    {
        return $this->setFieldValue('PROCESS_STATUS_ID', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setOriginatorRoleId($value)
    {
        return $this->setFieldValue('ORIGINATOR_ROLE_ID', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setAllInStatus($value)
    {
        return $this->setFieldValue('ALL_IN_STATUS', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setAnyOneInStatus($value)
    {
        return $this->setFieldValue('ANYONE_IN_STATUS', $value);
    }

    /**
     * @param array $value
     * @return $this
     */
    public function setAllOutOfStatuses($value)
    {
        return $this->setSerializedArrayFieldValue('ALL_OUT_OF_STATUSES', $value);
    }

    /**
     * @param array $value
     * @return $this
     */
    public function setAnyOneOutOfStatuses($value)
    {
        return $this->setSerializedArrayFieldValue('ANYONE_OUT_OF_STATUSES', $value);
    }



    public function getGroupId()
    {
        return $this->getFieldValue('GROUP_ID');
    }

    public function getProcessStatusId()
    {
        return $this->getFieldValue('PROCESS_STATUS_ID');
    }

    public function getOriginatorRoleId()
    {
        return $this->getFieldValue('ORIGINATOR_ROLE_ID');
    }

    public function getAllInStatus()
    {
        return $this->getFieldValue('ALL_IN_STATUS');
    }

    public function getAnyOneInStatus()
    {
        return $this->getFieldValue('ANYONE_IN_STATUS');
    }

    /**
     * @return array
     */
    public function getAllOutOfStatuses()
    {
        return $this->getSerializedArrayFieldValue('ALL_OUT_OF_STATUSES');
    }

    /**
     * @return array
     */
    public function getAnyOneOutOfStatuses()
    {
        return $this->getSerializedArrayFieldValue('ANYONE_OUT_OF_STATUSES');
    }

    /**
     * @param array $taskStatusesIds
     * @return bool
     */
    public function checkAllInStatus($taskStatusesIds)
    {
        if (!$this->getAllInStatus()) {
            return true;
        }

        if (empty($taskStatusesIds)) {
            return false;
        }

        foreach($taskStatusesIds as $id) {
            if ($id != $this->getAllInStatus()) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param array $taskStatusesIds
     * @return bool
     */
    public function checkAnyOneInStatus($taskStatusesIds)
    {
        if (!$this->getAnyOneInStatus()) {
            return true;
        }

        if (empty($taskStatusesIds)) {
            return false;
        }

        foreach($taskStatusesIds as $id) {
            if ($id == $this->getAnyOneInStatus()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $taskStatusesIds
     * @return bool
     */
    public function checkAllOutOfStatuses($taskStatusesIds)
    {
        $statusesIds = $this->getAllOutOfStatuses();
        if (empty($statusesIds)) {
            return true;
        }

        if (empty($taskStatusesIds)) {
            return false;
        }

        $overallStatuses = array_intersect($taskStatusesIds, $statusesIds);

        return (empty($overallStatuses));
    }

    /**
     * @param array $taskStatusesIds
     * @return bool
     */
    public function checkAnyOutOfStatuses($taskStatusesIds)
    {
        $statusesIds = $this->getAnyOneOutOfStatuses();
        if (empty($statusesIds)) {
            return true;
        }

        if (empty($taskStatusesIds)) {
            return false;
        }

        foreach ($taskStatusesIds as $id) {
            if (!in_array($id, $statusesIds)) {
                return true;
            }
        }
        
        return false;
    }


    /**
     * @param $statusId
     * @return bool
     */
    public static function isStatusUsed($statusId)
    {
        if (!$statusId) {
            return false;
        }

        $triggers = static::getListAll();
        foreach ($triggers as $trigger) {
            if (
                ($trigger->getAllInStatus() == $statusId) ||
                ($trigger->getAnyOneInStatus() == $statusId) ||
                (in_array($statusId, $trigger->getAllOutOfStatuses())) ||
                (in_array($statusId, $trigger->getAnyOneOutOfStatuses()))
            ) {
                return true;
            }
        }

        return false;
    }
}