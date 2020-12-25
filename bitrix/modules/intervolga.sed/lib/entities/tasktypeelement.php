<?php namespace Intervolga\Sed\Entities;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class TaskTypeElement extends TaskTypeDependantElement
{
    public function setName($value)
    {
        return $this->setValue($value);
    }

    public function getName()
    {
        return $this->getValue();
    }

    public function setCode($value)
    {
        return $this->setXmlId($value);
    }

    public function getCode()
    {
        return $this->getXmlId();
    }

    public static function add($xmlId, $name)
    {
        return static::createEmpty()
            ->setXmlId($xmlId)
            ->setValue($name)
            ->save();
    }

    public static function changeName($xmlId, $newName)
    {
        return static::getByXmlId($xmlId)
            ->setValue($newName)
            ->save();
    }

    public static function changeXmlId($oldXmlId, $newXmlId)
    {
        return static::getByXmlId($oldXmlId)
            ->setXmlId($newXmlId)
            ->save();
    }

    /**
     * @param null $entityFilter
     * @return int|null
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function getFieldId($entityFilter = null)
    {
        $res = null;
        try {
            $res = TaskTypeField::getOne()->getId();
        }
        catch (\Bitrix\Main\ObjectNotFoundException $e) {
            if(static::$isUfCreationAllowed) {
                // создаём свойство для хранения типов задач, если такого свойства нет
                $instance = TaskTypeField::createEmpty()->save();
                $res = $instance->getId();
            }
            else {
                throw new \Bitrix\Main\ArgumentException('There is no UF for task types');
            }
        }

        return $res;
    }

    public function save()
    {
        if($this->getId()) {
            $OnBeforeTaskStatusUpdateEvent = new \Bitrix\Main\Event(
                'intervolga.sed',
                'OnBeforeTaskTypeUpdate',
                array(
                    'type' => clone $this
                )
            );
            $OnBeforeTaskStatusUpdateEvent->send();
        }
        else {
            $OnBeforeTaskStatusAddEvent = new \Bitrix\Main\Event(
                'intervolga.sed',
                'OnBeforeTaskTypeAdd',
                array(
                    'type' => clone $this
                )
            );
            $OnBeforeTaskStatusAddEvent->send();
        }

        return parent::save();
    }

    protected static function deleteById($taskTypeId, $userFieldId = null)
    {
        $OnBeforeTaskTypeRemoveEvent = new \Bitrix\Main\Event(
            'intervolga.sed',
            'OnBeforeTaskTypeRemove',
            array(
                'taskTypeId' => $taskTypeId,
                'userFieldId' => $userFieldId,
            )
        );
        $OnBeforeTaskTypeRemoveEvent->send();
        $results = $OnBeforeTaskTypeRemoveEvent->getResults();

        if(is_array($results) && count($results)) {
            foreach ($results as $result) {
                if($result->getType() == \Bitrix\Main\EventResult::ERROR) {
                    return false;
                }
            }
        }

        $taskStatusField = null;
        try {
            $taskStatusField = TaskStatusField::getOneByEntityFilter($taskTypeId);
        }
        catch (\Bitrix\Main\ObjectNotFoundException $e) {}

        $res = parent::deleteById($taskTypeId, $userFieldId);

        if($res === true) {
            // удаляем информацию о статусах
            if(!empty($taskStatusField)) {
                $taskStatusField->delete();
            }
        }

        return $res;
    }
}