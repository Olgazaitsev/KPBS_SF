<?php namespace Intervolga\Sed\Entities;

use Bitrix\Main\UI\Uploader\Status;
use Intervolga\Sed\Entities\TaskStatusField;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class TaskStatusElement extends TaskTypeDependantElement
{
    const NATIVE_TASK_STATUS_SEPARATOR = '--';
    const PARSED_XML_ID__STATUS_INDEX = 1;
    const PARSED_XML_ID__SEPARATOR_INDEX = 2;
    const PARSED_XML_ID__CODE_INDEX = 3;

    protected $nativeTaskStatus = 0;
    protected $code = '';


    /**
     * @return int
     */
    public function getNativeTaskStatus()
    {
        return $this->nativeTaskStatus;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getValue();
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setNativeTaskStatus($value)
    {
        $value = (int)$value;
        if($value > 0) {
            $this->nativeTaskStatus = $value;
            if($this->getCode()) {
                parent::setXmlId($this->getNativeTaskStatus() . self::NATIVE_TASK_STATUS_SEPARATOR . $this->getCode());
            }
        }
        
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setCode($value)
    {
        $value = (string)$value;
        if($value) {
            $this->code = $value;
            if($this->getNativeTaskStatus()) {
                parent::setXmlId($this->getNativeTaskStatus() . self::NATIVE_TASK_STATUS_SEPARATOR . $this->getCode());
            }
        }

        return $this;
    }

    public function setName($value)
    {
        return $this->setValue($value);
    }

    /**
     * @param string $value
     * @return $this
     */
    protected function setXmlId($value)
    {
        parent::setXmlId($value);

        if($this->getXmlId()) {
            $matches = $this->parseXmlId();
            $this->nativeTaskStatus = ($matches[static::PARSED_XML_ID__STATUS_INDEX]) ? $matches[static::PARSED_XML_ID__STATUS_INDEX] : null;
            $this->code = ($matches[static::PARSED_XML_ID__CODE_INDEX]) ? $matches[static::PARSED_XML_ID__CODE_INDEX] : '';
        }
        
        return $this;
    }

    /**
     * @return array
     */
    protected function parseXmlId()
    {
        $matches = array();
        preg_match(static::getNativeTaskStatusRegexp(), $this->getXmlId(), $matches);
        return $matches;
    }

    public function save()
    {
        if($this->getNativeTaskStatus() < 1) {
            throw new \Bitrix\Main\SystemException(Loc::getMessage('C.TASKSTATUSELEMENT.EMPTY_NATIVETASKSTATUS_ERROR'));
        }
        if(!$this->getCode()) {
            throw new \Bitrix\Main\SystemException(Loc::getMessage('C.TASKSTATUSELEMENT.EMPTY_CODE_ERROR'));
        }

        $hasId = (bool)$this->getId();

        if($hasId) {
            $OnBeforeTaskStatusUpdateEvent = new \Bitrix\Main\Event(
                'intervolga.sed',
                'OnBeforeTaskStatusUpdate',
                array(
                    'status' => clone $this
                )
            );
            $OnBeforeTaskStatusUpdateEvent->send();
        }
        else {
            $OnBeforeTaskStatusAddEvent = new \Bitrix\Main\Event(
                'intervolga.sed',
                'OnBeforeTaskStatusAdd',
                array(
                    'status' => clone $this
                )
            );
            $OnBeforeTaskStatusAddEvent->send();
        }
        
        parent::save();

        if(!$hasId) {
            $OnAfterTaskStatusAddEvent = new \Bitrix\Main\Event(
                'intervolga.sed',
                'OnAfterTaskStatusAdd',
                array(
                    'status' => clone $this
                )
            );
            $OnAfterTaskStatusAddEvent->send();
        }

        return $this;
    }


    protected static function deleteById($statusId, $userFieldId = null)
    {
        $OnBeforeTaskStatusRemoveEvent = new \Bitrix\Main\Event(
            'intervolga.sed',
            'OnBeforeTaskStatusRemove',
            array(
                'statusId' => $statusId,
                'userFieldId' => $userFieldId,
            )
        );
        $OnBeforeTaskStatusRemoveEvent->send();
        $results = $OnBeforeTaskStatusRemoveEvent->getResults();

        if(is_array($results) && count($results)) {
            foreach ($results as $result) {
                if($result->getType() == \Bitrix\Main\EventResult::ERROR) {
                    return false;
                }
            }
        }
        
        $res = parent::deleteById($statusId, $userFieldId);

        if($res === true) {
            $OnAfterTaskStatusRemoveEvent = new \Bitrix\Main\Event(
                'intervolga.sed',
                'OnAfterTaskStatusRemove',
                array(
                    'statusId' => $statusId
                )
            );
            $OnAfterTaskStatusRemoveEvent->send();
        }

        return $res;
    }

    protected static function getNativeTaskStatusRegexp()
    {
        return '/^([1-9]{1,2})(' . static::NATIVE_TASK_STATUS_SEPARATOR . ')(.*)$/';
    }

    /**
     * @param $entityFilter
     * @return int|null
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function getFieldId($entityFilter = null)
    {
        $res = null;
        try {
            $res = TaskStatusField::getOneByEntityFilter($entityFilter)->getId();
        }
        catch (\Bitrix\Main\ObjectNotFoundException $e) {
            if(static::$isUfCreationAllowed) {
                // создаём свойство для хранения статусов задач, если такого свойства нет
                $instance = TaskStatusField::createEmpty($entityFilter)->save();
                $res = $instance->getId();
            }
            else {
                throw new \Bitrix\Main\ArgumentException('There is no UF for this task status');
            }
        }

        return $res;
    }

    public static function getNativeStatusNameById($id)
    {
        $names = static::getNativeTaskStatusNames();

        return (!$names[$id]) ? '' : $names[$id];
    }

    public static function getNativeTaskStatusNames()
    {
        return array(
            \CTasks::STATE_NEW => Loc::getMessage('C.TASKSTATUSELEMENT.ST_NEW'),
            \CTasks::STATE_PENDING => Loc::getMessage('C.TASKSTATUSELEMENT.ST_PENDING'),
            \CTasks::STATE_IN_PROGRESS => Loc::getMessage('C.TASKSTATUSELEMENT.ST_IN_PROGRESS'),
            \CTasks::STATE_SUPPOSEDLY_COMPLETED => Loc::getMessage('C.TASKSTATUSELEMENT.ST_SUPPOSEDLY_COMPLETED'),
            \CTasks::STATE_COMPLETED => Loc::getMessage('C.TASKSTATUSELEMENT.ST_COMPLETED'),
            \CTasks::STATE_DEFERRED => Loc::getMessage('C.TASKSTATUSELEMENT.ST_DEFERRED'),
            \CTasks::STATE_DECLINED => Loc::getMessage('C.TASKSTATUSELEMENT.ST_DECLINED'),
        );
    }

    /**
     * @param $code
     * @param null $entityFilter
     * @param array $arOrder
     * @param bool $useEntityFilter
     * @return TaskStatusElement
     * @throws \Bitrix\Main\ObjectNotFoundException
     */
    public static function getByCode($code, $entityFilter = null, $arOrder = array("SORT" => "ASC"), $useEntityFilter = true)
    {
        $statuses = static::getListAll($entityFilter, $arOrder, $useEntityFilter);

        if(count($statuses)) {
            foreach ($statuses as $status) {
                if($status->getCode() == $code) {
                    return $status;
                }
            }
        }

        throw new \Bitrix\Main\ObjectNotFoundException();
    }
}