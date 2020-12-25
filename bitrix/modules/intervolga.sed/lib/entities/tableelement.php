<?php namespace Intervolga\Sed\Entities;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class TableElement
{
    protected $fields = array();
    protected $staledFields = array();

    protected function __construct($fields)
    {
        $this->fields = $fields;
    }

    public function deleteSelf()
    {
        static::delete($this->getId());
    }

    public function getRawData()
    {
        return $this->fields;
    }

    public function registerWithTagCache($tableName)
    {
        global $CACHE_MANAGER;
        $CACHE_MANAGER->RegisterTag("table_name_" . $tableName);
    }

    /**
     * @param $fieldName
     * @param string $date в формате d.m.Y H:i:s
     * @return static
     */
    protected function setFieldDateTimeValue($fieldName, $date)
    {
        return $this->setFieldValue($fieldName, \Bitrix\Main\Type\DateTime::createFromUserTime($date));

    }

    /**
     * @param $fieldName
     * @return mixed
     */
    protected function getFieldDateTimeValue($fieldName, $isTimestamp = false)
    {
        /** @var \Bitrix\Main\Type\DateTime $dateTime */
        $dateTime = $this->getFieldValue($fieldName);
        if ($dateTime instanceof \Bitrix\Main\Type\DateTime) {
            return $isTimestamp ? $dateTime->getTimestamp() : $dateTime->toString();
        } else {
            return $dateTime;
        }
    }

    /**
     * @param string $fieldName
     * @param array $file массив от $_FILES
     * @return $this
     */
    protected function setFileFieldValue($fieldName, $file)
    {
        $file = array_merge(array('MODULE_ID' => 'intervolga.alfaportal'), $file);
        if (!isset($file['del'])) {
            $file['del'] = 'N';
        }
        $oldFile = $this->getFieldValue($fieldName);
        if (intval($oldFile) > 0) {
            $file['old_file'] = $oldFile;
        }
        $fileId = \CFile::SaveFile($file, 'club');

        return $this->setFieldValue($fieldName, $fileId);
    }

    /**
     * @param $fieldName
     * @param $value
     * @return $this
     */
    protected function setFieldValue($fieldName, $value)
    {
        $this->fields[$fieldName] = $value;
        $this->staledFields[$fieldName] = true;

        return $this;
    }

    /**
     * @param $fieldName
     * @return mixed
     */
    public function getFieldValue($fieldName)
    {
        return $this->fields[$fieldName];
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function save()
    {
        $entity = static::getEntity();
        /** @var DataManager $dc */
        $dc = $entity->getDataClass();
        $arFieldsValue = array();
        foreach ($this->staledFields as $fieldName => $dummyValue) {
            $arFieldsValue[$fieldName] = $this->getFieldValue($fieldName);
        }

        if ($this->getId() > 0) {
            $result = $dc::update($this->getId(), $arFieldsValue);
        } else {
            foreach ($arFieldsValue as $key => $item) {
                if (empty($item)) {
                    unset($arFieldsValue[$key]);
                }
            }
            $result = $dc::add($arFieldsValue);
        }
        if ($result->isSuccess()) {
            static::clearCache($entity->getDBTableName());
            $this->staledFields = array();
            return $result->getId();
        } else {
            throw new \Exception(implode("\n", $result->getErrorMessages()));
        }
    }

    /**
     * @return static
     * @throws \Exception
     * @throws \Bitrix\Main\ObjectNotFoundException
     */
    public function saveAndRetrieve()
    {
        $id = $this->save();
        if ($id) {
            return static::getById($id);
        } else {
            return null;
        }
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getId()
    {
        return $this->getFieldValue($this->getPrimaryField());
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getPrimaryField()
    {
        return static::getEntity()->getPrimary();
    }

    /**
     * @param string $fieldName
     * @param array $value
     * @return $this
     */
    public function setSerializedArrayFieldValue($fieldName, $value)
    {
        if (!is_array($value)) {
            $value = array();
        }

        return $this->setFieldValue($fieldName, serialize($value));
    }

    /**
     * @param string $fieldName
     * @return array
     */
    protected function getSerializedArrayFieldValue($fieldName)
    {
        $value = @unserialize($this->getFieldValue($fieldName));
        return (is_array($value)) ? $value : array();
    }


    /**
     * @param $tableName
     */
    protected static function clearCache($tableName)
    {
        global $CACHE_MANAGER;
        $CACHE_MANAGER->ClearByTag('table_name_' . $tableName);
    }

    /**
     * @throws \Exception
     */
    protected static function getEntity()
    {
        throw new \Exception();
    }

    public static function getAllFieldsName()
    {
        $ans = array();
        $entity = static::getEntity();
        $fields = $entity->getFields();
        foreach ($fields as $key => $field) {
            $ans[] = $key;
        }

        return $ans;
    }

    /**
     * Добавление нового элемента
     * @return static
     */
    public static function createEmpty()
    {
        return new static(array());
    }

    /**
     * @param $id
     * @return static
     * @throws \Bitrix\Main\ObjectNotFoundException
     * @throws \Exception
     */
    public static function getById($id)
    {
        $entity = static::getEntity();
        self::registerWithTagCache($entity->getDBTableName());
        $dc = $entity->getDataClass();
        $result = $dc::getByPrimary($id, ['select' => ['*', 'UF_*']]);
        $arFields = $result->fetch();
        if ($arFields) {
            return new static($arFields);
        } else {
            throw new \Bitrix\Main\ObjectNotFoundException();
        }
    }

    /**
     * @param $arFilter
     * @param array $arOrder
     * @param array $arSelect
     * @param int $limit
     * @param int $offset
     * @return static[]
     * @throws \Exception
     */
    public static function getListByFilter($arFilter, $arOrder = array(), $arSelect = array(), $limit = 0, $offset = 0)
    {
        $entity = static::getEntity();
        $query = new \Bitrix\Main\Entity\Query($entity);

        self::registerWithTagCache($entity->getDBTableName());

        $ans = array();
        $arSelect = array_merge(array('*'), $arSelect);
        $query->setSelect($arSelect);
        if (in_array('RAND', $arOrder)) {
            $query->registerRuntimeField("RAND", new \Bitrix\Main\Entity\ExpressionField("RAND", "RAND()"));
        }
        $query->setFilter($arFilter)
            ->setOrder($arOrder);

        if ($limit > 0) {
            $query->setLimit($limit);
        }

        if ($offset > 0) {
            $query->setOffset($offset);
        }

        $db = $query->exec();

        while ($arFields = $db->fetch()) {
            $ans[] = new static($arFields);
        }

        return $ans;
    }

    /**
     * @param $arSelect
     * @param $arFilter
     * @param $arGroup
     * @param array $arOrder
     * @param int $limit
     * @param int $offset
     * @param array $runtime
     * @return static[]
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Exception
     */
    public static function getListByGetList($arSelect, $arFilter, $arGroup = array(), $arOrder = array(), $limit = 0, $offset = 0, $runtime = array())
    {
        $entity = static::getEntity();
        $data_manager = $entity->getDataClass();
        /** @var DataManager $instance */
        $instance = new $data_manager();
        self::registerWithTagCache($entity->getDBTableName());

        $ans = array();

        $db = $instance->getList(array(
            'select' => $arSelect,
            'filter' => $arFilter,
            'group' => $arGroup,
            'order' => $arOrder,
            'limit' => $limit ? $limit : null,
            'offset' => $offset ? $offset : null,
            'runtime' => $runtime
        ));

        while ($arFields = $db->fetch()) {
            $ans[] = new static($arFields);
        }

        return $ans;
    }

    /**
     * @param $ids
     * @param array $arOrder
     * @param array $arSelect
     * @param int $limit
     * @return static[]
     */
    public static function getListByIds($ids, $arOrder = array(), $arSelect = array(), $limit = 0)
    {
        return static::getListByFilter(array('@' . static::getPrimaryField() => $ids), $arOrder, $arSelect, $limit);
    }

    /**
     * @param $arFilter
     * @param $arRuntimeFields
     * @param array $arGroup
     * @param array $arOrder
     * @param int $limit
     * @return array
     * @throws \Exception
     */
    public static function getListByFilterWithRuntimeField($arFilter, $arRuntimeFields, $arGroup = array(), $arOrder = array(), $limit = 0)
    {
        $entity = static::getEntity();
        $query = new \Bitrix\Main\Entity\Query($entity);

        self::registerWithTagCache($entity->getDBTableName());

        $ans = array();
        $arSelect = array();
        $query->setFilter($arFilter)
            ->setGroup($arGroup)
            ->setOrder($arOrder);

        if ($limit > 0) {
            $query->setLimit($limit);
        }

        foreach ($arRuntimeFields as $name => $field) {
            $query->registerRuntimeField($name, array("expression" => array($field['RUNTIME'] . "(%s)", $field['FIELD'])));
            $arSelect[] = $name;
        }

        $arSelect = array_merge($arSelect, $arGroup);

        $db = $query
            ->setSelect($arSelect)
            ->exec();

        while ($arFields = $db->fetch()) {
            $ans[] = $arFields;
        }

        return $ans;
    }


    /**
     * @param $arFilter
     * @param array $arOrder
     * @param array $arSelect
     * @return static
     * @throws \Bitrix\Main\ObjectNotFoundException
     * @throws \Exception
     */
    public static function getOneByFilter($arFilter, $arOrder = array(), $arSelect = array())
    {
        $entity = static::getEntity();
        $query = new \Bitrix\Main\Entity\Query($entity);

        self::registerWithTagCache($entity->getDBTableName());

        $ans = null;
        $arSelect = array_merge(array('*'), $arSelect);
        $db = $query->setSelect($arSelect)
            ->setFilter($arFilter)
            ->setOrder($arOrder)
            ->exec();

        if ($arFields = $db->fetch()) {
            $ans = new static($arFields);
        } else {
            throw new \Bitrix\Main\ObjectNotFoundException();
        }

        return $ans;
    }

    /**
     * @param $id
     * @return bool
     * @throws \Exception
     * @throws \Exception
     */
    public static function delete($id)
    {
        $entity = static::getEntity();
        $dc = $entity->getDataClass();
        $result = $dc::delete($id);
        if ($result->isSuccess()) {
            static::clearCache($entity->getDBTableName());
            return true;
        } else {
            throw new \Exception(implode("\n", $result->getErrorMessages()));
        }
    }

    public static function deleteAll()
    {
        $entity = static::getEntity();
        $entity->getConnection()->truncateTable($entity->getDBTableName());
    }

    /**
     * @param static[] $instances
     * @return array
     */
    public static function getIdsByEntityList($instances)
    {
        $ids = array();
        if (!empty($instances)) {
            foreach ($instances as $instance) {
                $ids[] = $instance->getId();
            }
        }
        return $ids;
    }

    /**
     * @param array $filter
     * @return int
     */
    public static function getCountByFilter($filter)
    {
        $result = static::getListByFilterWithRuntimeField(
            $filter,
            array(
                'CNT' => array(
                    'RUNTIME' => 'COUNT',
                    'FIELD' => 'ID'
                )
            )
        );

        return empty($result[0]) ? 0 : $result[0]['CNT'];
    }

    public static function getListAll($arOrder = array(), $arSelect = array())
    {
        return static::getListByFilter(array(), $arOrder, $arSelect);
    }

    /**
     * @param $filter
     * @return bool
     */
    public static function removeByFilter($filter)
    {
        if (empty($filter) || !is_array($filter)) {
            return false;
        }

        $instances = static::getListByFilter($filter);
        foreach ($instances as $instance) {
            $instance->deleteSelf();
        }

        return true;
    }

    /**
     * @param static[] $entities
     * @return array
     */
    public static function makePrimaryAsArrayKeys($entities)
    {
        $result = array();

        foreach ($entities as $entity) {
            $result[$entity->getId()] = $entity;
        }

        return $result;
    }
}