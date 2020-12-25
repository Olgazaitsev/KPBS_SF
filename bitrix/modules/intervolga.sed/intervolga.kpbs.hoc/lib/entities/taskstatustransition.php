<?php namespace Intervolga\Sed\Entities;

use Intervolga\Sed\Tables\TaskStatusTransitionTable;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class TaskStatusTransition extends TableElement
{
    const ORIGINATOR_CODE = 'ORIG';
    const RESPONSIBLE_CODE = 'RESP';


    /**
     * @return \Bitrix\Main\Entity\Base
     */
    protected static function getEntity()
    {
        return TaskStatusTransitionTable::getEntity();
    }


    /**
     * @param $value
     * @return $this
     */
    public function setTaskType($value)
    {
        return $this->setFieldValue('TASK_TYPE', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setSourceStatus($value)
    {
        return $this->setFieldValue('SOURCE_STATUS', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setDestStatus($value)
    {
        return $this->setFieldValue('DEST_STATUS', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setUserRole($value)
    {
        return $this->setFieldValue('USER_ROLE', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setCommentIsNeeded($value)
    {
        return $this->setFieldValue('NEED_COMMENT', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setButtonLabel($value)
    {
        return $this->setFieldValue('BTN_LABEL', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setButtonSort($value)
    {
        return $this->setFieldValue('BTN_SORT', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setButtonColor($value)
    {
        return $this->setFieldValue('BTN_COLOR', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setButtonTextColor($value)
    {
        return $this->setFieldValue('BTN_TEXT_COLOR', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setButtonHoverMode($value)
    {
        return $this->setFieldValue('BTN_HOVER_MODE', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setTransitionAllowed($value)
    {
        return $this->setFieldValue('TRANSITION_ALLOWED', $value);
    }

    public function getTaskType()
    {
        return $this->getFieldValue('TASK_TYPE');
    }

    public function getSourceStatus()
    {
        return $this->getFieldValue('SOURCE_STATUS');
    }

    public function getDestStatus()
    {
        return $this->getFieldValue('DEST_STATUS');
    }

    public function getUserRole()
    {
        return $this->getFieldValue('USER_ROLE');
    }

    public function isCommentNeeded()
    {
        return $this->getFieldValue('NEED_COMMENT');
    }

    public function getButtonLabel()
    {
        return $this->getFieldValue('BTN_LABEL');
    }

    public function getButtonSort()
    {
        return $this->getFieldValue('BTN_SORT');
    }

    public function getButtonColor()
    {
        return $this->getFieldValue('BTN_COLOR');
    }

    public function getButtonTextColor()
    {
        return $this->getFieldValue('BTN_TEXT_COLOR');
    }

    public function getButtonHoverMode()
    {
        return $this->getFieldValue('BTN_HOVER_MODE');
    }

    public function isTransitionAllowed()
    {
        return $this->getFieldValue('TRANSITION_ALLOWED');
    }

    public static function createTransition($taskTypeId, $sourceStatusId, $destStatusId, $userRole, $needComment = false, $transitionAllowed = false, $buttonLabel = '')
    {
        $instance = static::createEmpty()
            ->setTaskType($taskTypeId)
            ->setSourceStatus($sourceStatusId)
            ->setDestStatus($destStatusId)
            ->setUserRole($userRole)
            ->setCommentIsNeeded($needComment)
            ->setTransitionAllowed($transitionAllowed)
            ->setButtonLabel($buttonLabel);

        $instance->save();

        return $instance;
    }

    public static function addStatusTransitions(\Intervolga\Sed\Entities\TaskStatusElement $sourceStatus, $statusField = null)
    {
        if(!($statusField instanceof \Intervolga\Sed\Entities\TaskStatusField)) {
            $statusField = \Intervolga\Sed\Entities\TaskStatusField::getById($sourceStatus->getUserFieldId(), null, false);
        }

        $taskTypeId = $statusField->getTaskTypeIdFromFieldName();
        $statuses = \Intervolga\Sed\Entities\TaskStatusElement::getListAll($taskTypeId);

        if(is_array($statuses) && count($statuses)) {
            foreach ($statuses as $destStatus) {
                if($sourceStatus->getId() != $destStatus->getId()) {
                    try {
                        static::createTransition($taskTypeId, $sourceStatus->getId(), $destStatus->getId(), static::ORIGINATOR_CODE);
                    }
                    catch(\Bitrix\Main\DB\SqlQueryException $e) {}
                    try {
                        static::createTransition($taskTypeId, $destStatus->getId(), $sourceStatus->getId(), static::ORIGINATOR_CODE);
                    }
                    catch(\Bitrix\Main\DB\SqlQueryException $e) {}
                    try {
                        static::createTransition($taskTypeId, $sourceStatus->getId(), $destStatus->getId(), static::RESPONSIBLE_CODE);
                    }
                    catch(\Bitrix\Main\DB\SqlQueryException $e) {}
                    try {
                        static::createTransition($taskTypeId, $destStatus->getId(), $sourceStatus->getId(), static::RESPONSIBLE_CODE);
                    }
                    catch(\Bitrix\Main\DB\SqlQueryException $e) {}
                }
            }
        }
    }

    /**
     * @param $statusId
     * @return bool
     */
    public static function removeByStatusId($statusId)
    {
        if(empty($statusId)) {
            return false;
        }

        return static::removeByFilter(array(
            'LOGIC' => 'OR',
            array('SOURCE_STATUS' => $statusId),
            array('DEST_STATUS' => $statusId),
        ));
    }
}