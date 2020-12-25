<?php namespace Intervolga\Sed\Entities;

use Intervolga\Sed\Interfaces\AbstractUfEntityInterface;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

abstract class AbstractUfEntity implements AbstractUfEntityInterface
{
    /** @var int */
    protected $id = 0;
    /** @var string */
    protected $xmlId = '';
    /** @var int $sort */
    protected $sort;


    /**
     * AbstractUfEntity constructor.
     * @param array $fields
     * @param bool $setEntityFields
     */
    protected function __construct($fields, $setEntityFields = false)
    {
        $this->setId($fields['ID'])
            ->setXmlId($fields['XML_ID'])
            ->setSort($fields['SORT'], 100);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getXmlId()
    {
        return $this->xmlId;
    }

    /**
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param $value
     * @return static
     */
    protected function setId($value)
    {
        $value = (int)$value;
        if($value > 1) {
            $this->id = $value;
        }
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    protected function setXmlId($value)
    {
        $value = (string)$value;
        if($value) {
            $this->xmlId = $value;
        }
        return $this;
    }

    /**
     * @param int $value
     * @param int|null $defaultValue
     * @return static
     */
    public function setSort($value, $defaultValue = null)
    {
        $value = (int)$value;
        $defaultValue = (int)$defaultValue;
        if($value > 1) {
            $this->sort = $value;
        }
        elseif($defaultValue > 1) {
            $this->sort = $defaultValue;
        }
        return $this;
    }

    /**
     * @param $value
     * @return string
     */
    protected function checkBxBoolean($value)
    {
        if((string)$value == 'Y') {
            return 'Y';
        }
        else {
            return 'N';
        }
    }

    /**
     * @return bool
     */
    public function delete()
    {
        if ($this->id > 0) {
            return static::deleteById($this->id);
        }

        return false;
    }


    /**
     * @return static
     */
    public static function createEmpty()
    {
        return new static(array());
    }

    /**
     * @param $id
     * @throws \Bitrix\Main\NotImplementedException
     */
    protected static function deleteById($id)
    {
        throw new \Bitrix\Main\NotImplementedException();
    }

//    /**
//     * @param string $xmlId
//     * @return bool
//     */
//    protected static function deleteByXmlId($xmlId)
//    {
//        $instance = self::getByXmlId($xmlId);
//        $success = $instance->delete();
//        return $success;
//    }

    /**
     * @param int $id
     * @param mixed $entityFilter
     * @param bool $useEntityFilter
     * @return static
     * @throws \Bitrix\Main\ArgumentNullException
     */
    public static function getById($id, $entityFilter = null, $useEntityFilter = true)
    {
        $id = (int)$id;
        if ($id > 0) {
            return static::getOneByFilter(array("ID" => $id), $entityFilter, array(), $useEntityFilter);
        } else {
            throw new \Bitrix\Main\ArgumentNullException('id');
        }
    }

    /**
     * @param $xmlId
     * @param mixed $entityFilter
     * @param bool $useEntityFilter
     * @return static
     * @throws \Bitrix\Main\ArgumentNullException
     */
    public static function getByXmlId($xmlId, $entityFilter = null, $useEntityFilter = true)
    {
        $xmlId = (string)$xmlId;
        if (mb_strlen($xmlId) > 0) {
            return static::getOneByFilter(array("XML_ID" => $xmlId), $entityFilter, array(), $useEntityFilter);
        } else {
            throw new \Bitrix\Main\ArgumentNullException('xmlid');
        }
    }

    /**
     * @param mixed $entityFilter
     * @param array $arOrder
     * @return static[]
     */
    public static function getListAll($entityFilter = null, $arOrder = array("SORT" => "ASC"), $useEntityFilter = true)
    {
        return static::getListByFilter(array(), $entityFilter, $arOrder, $useEntityFilter);
    }

    /**
     * @param array $arFilter
     * @param null $entityFilter
     * @param array $arOrder
     * @return static[]
     */
    public static function getListByFilter($arFilter, $entityFilter = null, $arOrder = array("SORT" => "ASC"), $useEntityFilter = true)
    {
        $instances = array();
        if($useEntityFilter) {
            $arFilter = array_merge($arFilter, static::makeArrayFromEntityFilter($entityFilter));
        }

        $rsData = static::dbQuery($arFilter, $arOrder);
        while($arUserField = $rsData->Fetch()) {
            $instance = new static($arUserField, true);
            $instances[] = $instance;
        }

        return $instances;
    }

    /**
     * @param $arFilter
     * @param null $entityFilter
     * @param array $arOrder
     * @return static
     * @throws \Bitrix\Main\ObjectNotFoundException
     */
    public static function getOneByFilter($arFilter, $entityFilter = null, $arOrder = array("SORT" => "ASC"), $useEntityFilter = true)
    {
        if($useEntityFilter) {
            $arFilter = array_merge($arFilter, static::makeArrayFromEntityFilter($entityFilter));
        }
        $rsData = static::dbQuery($arFilter, $arOrder);
        if($arUserField = $rsData->Fetch()) {
            $instance = new static($arUserField, true);
            return $instance;
        }
        else {
            throw new \Bitrix\Main\ObjectNotFoundException();
        }
    }

    /**
     * @param string $action
     * @param bool $checkBitrixException
     * @throws \Bitrix\Main\SystemException
     */
    protected static function throwCrudException($action, $checkBitrixException = false)
    {
        $errorMsg = Loc::getMessage('C.ABSTRACTUFENTITY.CRUD_EXCEPTION', array('#ACTION_NAME#' => $action));

        if($checkBitrixException) {
            global $APPLICATION;
            $ex = $APPLICATION->GetException();

            if($ex instanceof \CApplicationException) {
                $errorMsg = $ex->GetString();
            }
        }

        throw new \Bitrix\Main\SystemException($errorMsg);
    }

    /**
     * @param static[] $instances
     * @return array|static[]
     */
    public static function makeIdsAsArrayKeys($instances)
    {
        if(empty($instances)) {
            return $instances;
        }

        $result = array();
        foreach ($instances as $instance) {
            $result[$instance->getId()] = $instance;
        }

        return $result;
    }

    /**
     * @param array $arFilter
     * @param array $arOrder
     * @throws \Bitrix\Main\NotImplementedException
     */
    protected static function dbQuery($arFilter, $arOrder = array("SORT" => "ASC"))
    {
        throw new \Bitrix\Main\NotImplementedException();
    }

    /**
     * @param null $entityFilter
     * @throws \Bitrix\Main\NotImplementedException
     */
    protected static function makeArrayFromEntityFilter($entityFilter = null)
    {
        throw new \Bitrix\Main\NotImplementedException();
    }
}