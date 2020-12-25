<?php namespace Intervolga\Sed\Entities;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

abstract class UfElement extends AbstractUfEntity
{
    /** @var string $multiple */
    protected $multiple;
    /** @var string $mandatory */
    protected $mandatory;
    /** @var string $showFilter */
    protected $showFilter;
    /** @var string $showInList */
    protected $showInList;
    /** @var string $editInList */
    protected $editInList;
    /** @var string $isSearchable */
    protected $isSearchable;
    /** @var string $userTypeId */
    protected $userTypeId;
    /** @var string $userTypeId */
    protected $entityId;
    /** @var string $userTypeId */
    protected $fieldName;
    /** @var  array $settings */
    protected $settings;
    /** @var string $fieldLabel */
    protected $fieldLabel;


    protected function __construct($fields, $setEntityFields = false)
    {
        parent::__construct($fields);

        $this->setShowFilter($fields['SHOW_FILTER'])
            ->setMultiple($fields['MULTIPLE'])
            ->setMandatory($fields['MANDATORY'])
            ->setShowInList($fields['SHOW_IN_LIST'])
            ->setEditInList($fields['EDIT_IN_LIST'])
            ->setIsSearchable($fields['IS_SEARCHABLE'])
            ->setSettings($fields['SETTINGS'])
            ->setFieldLabel($fields['EDIT_FORM_LABEL']);

        if($setEntityFields) {
            $this->setFieldName($fields['FIELD_NAME'])
                ->setEntityId($fields['ENTITY_ID'])
                ->setUserTypeId($fields['USER_TYPE_ID']);
        }
    }

    /**
     * @param array $arSettings
     * @return static
     */
    abstract public function setSettings($arSettings);

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @param $value
     * @return $this
     */
    protected function setFieldLabel($value)
    {
        $value = (string)$value;
        if($value) {
            $this->fieldLabel = $value;
        }

        return $this;
    }

    /**
     * @param string $value
     * @param string $defaultValue
     * @return $this
     */
    public function setShowFilter($value, $defaultValue = 'I')
    {
        $value = (string)$value;
        if(strlen($value) > 0 && mb_strpos("NIES", $value) !== false) {
            $this->showFilter = $value;
        }
        else {
            $this->showFilter = (string)$defaultValue;
        }

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    protected function setFieldName($value)
    {
        $value = (string)$value;
        if($value) {
            $this->fieldName = $value;
        }

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    protected function setEntityId($value)
    {
        $value = (string)$value;
        if($value) {
            $this->entityId = $value;
        }

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     * @throws \Bitrix\Main\ArgumentException
     */
    protected function setUserTypeId($value)
    {
        global $USER_FIELD_MANAGER;
        $value = (string)$value;
        if($USER_FIELD_MANAGER->GetUserType($value)) {
            $this->userTypeId = $value;
        }
        else {
            throw new \Bitrix\Main\ArgumentException(Loc::getMessage('C.UFELEMENT.NOT_SUPPORTED_USER_TYPE_ID_VALUE'));
        }

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setMultiple($value)
    {
        $this->multiple = $this->checkBxBoolean($value);
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setMandatory($value)
    {
        $this->mandatory = $this->checkBxBoolean($value);
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setShowInList($value)
    {
        $this->showInList = $this->checkBxBoolean($value);
        return $this;
    }

    /**
     * @param $value
     * @param string $defaultValue
     * @return $this
     */
    public function setEditInList($value, $defaultValue = 'Y')
    {
        if($value) {
            $this->editInList = $this->checkBxBoolean($value);
        }
        else {
            $this->editInList = $this->checkBxBoolean($defaultValue);
        }
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setIsSearchable($value)
    {
        $this->isSearchable = $this->checkBxBoolean($value);
        return $this;
    }

    /**
     * 1. FIELD_NAME, ENTITY_ID, USER_TYPE_ID - непустые поля
     * 2. USER_TYPE_ID должно быть одним из допустимых значений (enumeration, string, ...)
     * 3. FIELD_NAME - уникальное значение в рамках одного ENTITY_ID
     * 4. несуществующее ENTITY_ID с любым FIELD_NAME - нет ошибки
     *
     * @return $this
     * @throws \Bitrix\Main\SystemException
     */
    public function save()
    {
        $action = $this->id ? 'update' : 'add';

        if($action == 'update') {
            $oUserTypeEntity = new \CUserTypeEntity();
            $isSuccess = $oUserTypeEntity->Update($this->getId(), static::getMap());

            if(!$isSuccess) {
                static::throwCrudException(Loc::getMessage('C.UFELEMENT.CRUD_ACTION_UPDATE'), true);
            }

        } else {
            $oUserTypeEntity = new \CUserTypeEntity();
            $idUserTypeProp = $oUserTypeEntity->Add(static::getMap(true));

            if(!$idUserTypeProp) {
                static::throwCrudException(Loc::getMessage('C.UFELEMENT.CRUD_ACTION_CREATE'), true);
            } else {
                $this->setId($idUserTypeProp);
            }
        }

        return $this;
    }

    protected function checkRequiredParams()
    {
        if(empty($this->userTypeId)) {
            throw new \Bitrix\Main\SystemException(Loc::getMessage('C.UFELEMENT.EMPTY_USER_FIELD_ERROR'));
        }
        if(empty($this->fieldName)) {
            throw new \Bitrix\Main\SystemException(Loc::getMessage('C.UFELEMENT.EMPTY_FIELD_NAME_ERROR'));
        }
        if(empty($this->entityId)) {
            throw new \Bitrix\Main\SystemException(Loc::getMessage('C.UFELEMENT.EMPTY_ENTITY_ID_ERROR'));
        }
    }

    /**
     * @param bool $getFullMap
     * @return array
     */
    protected function getMap($getFullMap = false)
    {
        $map =  array(
            'XML_ID' => $this->xmlId,
            'SORT' => $this->sort,
            'MANDATORY' => $this->mandatory,
            'SHOW_FILTER' => $this->showFilter,
            'SHOW_IN_LIST' => $this->showInList,
            'EDIT_IN_LIST' => $this->editInList,
            'IS_SEARCHABLE' => $this->isSearchable,
            'SETTINGS' => $this->settings,
        );

        if(!empty($this->fieldLabel)) {
            $map['EDIT_FORM_LABEL'] = array ('ru' => $this->fieldLabel);
            $map['LIST_COLUMN_LABEL'] = array('ru' => $this->fieldLabel);
            $map['LIST_FILTER_LABEL'] = array('ru' => $this->fieldLabel);
            $map['EDIT_FORM_LABEL'] = array ('en' => $this->fieldLabel);
            $map['LIST_COLUMN_LABEL'] = array('en' => $this->fieldLabel);
            $map['LIST_FILTER_LABEL'] = array('en' => $this->fieldLabel);
        }

        if($getFullMap) {
            $map['ENTITY_ID'] = $this->entityId;
            $map['FIELD_NAME'] = $this->fieldName;
            $map['USER_TYPE_ID'] = $this->userTypeId;
            $map['MULTIPLE'] = $this->multiple;
        }

        return $map;
    }


    /**
     * @param int $id
     * @return bool
     * @throws \Bitrix\Main\ArgumentNullException
     */
    protected static function deleteById($id)
    {
        $id = (int)$id;
        if($id < 1) {
            throw new \Bitrix\Main\ArgumentNullException('id');
        }

        $userTypeObj = new \CUserTypeEntity();
        $res = $userTypeObj->Delete($id);

        if(!$res) {
            static::throwCrudException(Loc::getMessage('C.UFELEMENT.CRUD_ACTION_DELETE'), true);
        }

        return true;
    }

    /**
     * @param array $arFilter
     * @param array $arOrder
     * @return \CDBResult
     */
    protected static function dbQuery($arFilter, $arOrder = array("SORT" => "ASC"))
    {
        $userTypeObj = new \CUserTypeEntity();
        $rsData = $userTypeObj->GetList($arOrder, $arFilter);
        return $rsData;
    }
}

/*
 * Array
(
    [0] => Array
        (
            [ID] => 200
            [ENTITY_ID] => TASKS_TASK
            [FIELD_NAME] => UF_TASK_TTYPE
            [USER_TYPE_ID] => enumeration
            [XML_ID] =>
            [SORT] => 100
            [MULTIPLE] => N
            [MANDATORY] => N
            [SHOW_FILTER] => I
            [SHOW_IN_LIST] => Y
            [EDIT_IN_LIST] => Y
            [IS_SEARCHABLE] => N
            [SETTINGS] => Array
                (
                    [DISPLAY] => CHECKBOX
                    [LIST_HEIGHT] => 5
                    [CAPTION_NO_VALUE] => Обычная задача
                )
        )
)
*/