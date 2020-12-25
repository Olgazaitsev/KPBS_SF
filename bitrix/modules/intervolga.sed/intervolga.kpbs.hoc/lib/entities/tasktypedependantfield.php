<?php namespace Intervolga\Sed\Entities;

use Intervolga\Sed\Entities\TaskTypeElement;
use Intervolga\Sed\Entities\TaskTypeField;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

abstract class TaskTypeDependantField extends EnumerableUfElement
{
    public function getTaskTypeIdFromFieldName()
    {
        $taskTypeId = mb_substr($this->getFieldName(), mb_strlen(static::getFieldNamePrefix()));
        if($taskTypeId !== false) {
            return (int)$taskTypeId;
        }
        return null;
    }


    public static function getTaskTypeIdFromFieldNameStatic($fieldName)
    {
        $taskTypeId = mb_substr($fieldName, mb_strlen(static::getFieldNamePrefix()));
        if($taskTypeId !== false) {
            return (int)$taskTypeId;
        }
        return null;
    }

    protected static function getFieldNamePrefix()
    {
        throw new \Bitrix\Main\NotImplementedException();
    }

    public static function createEmpty($entityFilter = null)
    {
        return parent::createEmpty()
            ->setEntityId(TaskTypeField::TASK_ENTITY_ID)
            ->setFieldName(static::getFieldNameByEntityFilter($entityFilter));
    }

    /**
     * @param int|string|TaskTypeElement $entityFilter
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ArgumentNullException
     */
    public static function getFieldNameByEntityFilter($entityFilter)
    {
        if(empty($entityFilter)) {
            throw new \Bitrix\Main\ArgumentNullException('entityFilter');
        }

        // $param - объект, xml_id или id одного из типов задач
        if($entityFilter instanceof TaskTypeElement) {
            if($entityFilter->getId() > 1) {
                return static::getFieldNamePrefix() . $entityFilter->getId();
            }
            else {
                throw new \Bitrix\Main\ArgumentNullException('entityFilter->getId()');
            }
        }
        else {
            try {
                $id = TaskTypeElement::getByXmlId($entityFilter)->getId();
                return static::getFieldNamePrefix() . $id;
            }
            catch (\Bitrix\Main\ObjectNotFoundException $e) {
                try {
                    $id = TaskTypeElement::getById($entityFilter)->getId();
                    return static::getFieldNamePrefix() . $id;
                }
                catch (\Bitrix\Main\SystemException $e) {
                    throw new \Bitrix\Main\ArgumentException('There is no UF for such \'entityFilter\' or \'entityFilter\' parameter is wrong');
                }
            }
        }
    }

    /**
     * @param mixed $entityFilter
     * @return static
     */
    public static function getOneByEntityFilter($entityFilter = null)
    {
        return static::getOneByFilter(array(), $entityFilter);
    }

    /**
     * @param null $entityFilter
     * @return array
     */
    protected static function makeArrayFromEntityFilter($entityFilter = null)
    {
        $filter = array(
            'ENTITY_ID' => TaskTypeField::TASK_ENTITY_ID,
            'FIELD_NAME' => static::getFieldNameByEntityFilter($entityFilter),
        );
        $parentFilter = parent::makeArrayFromEntityFilter($entityFilter);

        if(is_array($parentFilter)) {
            $filter = array_merge($parentFilter, $filter);
        }

        return $filter;
    }

    /**
     * API Bitrix'а не предусматривает передачи в $arFilter параметров в виде массива.
     * Если такие параметры были переданы, то фильтрацию по ним производим после запроса к БД ('параметр' => array() пропускаем).
     *
     * @param array $arFilter
     * @param null $entityFilter
     * @param array $arOrder
     * @param bool $useEntityFilter
     * @return static[]
     */
    public static function getListByFilter($arFilter, $entityFilter = null, $arOrder = array("SORT" => "ASC"), $useEntityFilter = true)
    {
        $taskTypes = TaskTypeElement::getListAll();

        if(empty($taskTypes)) {
            return array();
        }

        $fieldsToFilterAfter = array();
        $allowedFieldNames = array();
        foreach ($taskTypes as $taskType) {
            $allowedFieldNames[static::getFieldNamePrefix() . $taskType->getId()] = true;
        }

        if(is_string($arFilter['FIELD_NAME']) && mb_strlen($arFilter['FIELD_NAME'])) {
            if(($allowedFieldNames[$arFilter['FIELD_NAME']]) !== true) {
                return array();
            }
        }

        foreach ($arFilter as $fieldName => $arFieldValue) {
            if(is_array($arFieldValue) && count($arFieldValue)) {
                $fieldsToFilterAfter[$fieldName] = $arFieldValue;
            }
        }

        $instances = array();
        $rsData = static::dbQuery(array_merge($arFilter, array(
            'ENTITY_ID' => TaskTypeField::TASK_ENTITY_ID,
            'USER_TYPE_ID' => static::USER_TYPE_ID
        )), $arOrder);

        while($arUserField = $rsData->Fetch()) {

            if(($allowedFieldNames[$arUserField['FIELD_NAME']]) !== true) {
                continue;
            }

            $outerOfFilter = false;
            foreach ($fieldsToFilterAfter as $fieldName => $arFieldValue) {
                if(isset($arUserField[$fieldName]) && !in_array($arUserField[$fieldName], $arFieldValue)) {
                    $outerOfFilter = true;
                    break;
                }
            }

            if($outerOfFilter === true) {
                continue;
            }

            $instance = new static($arUserField, true);
            $instances[] = $instance;
        }

        return $instances;
    }

    /**
     * @return array
     */
    public static function getAllFieldsId()
    {
        $ids = array();
        $fields = static::getListAll();

        if(!empty($fields)) {
            foreach ($fields as $field) {
                $ids[] = $field->getId();
            }
        }

        return $ids;
    }
}