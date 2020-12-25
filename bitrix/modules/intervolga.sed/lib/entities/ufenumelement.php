<?php namespace Intervolga\Sed\Entities;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

abstract class UfEnumElement extends AbstractUfEntity
{
    /** @var string $value */
    protected $value = '';
    /** @var int $userFieldId */
    protected $userFieldId;

    protected function __construct($fields, $setEntityFields = false)
    {
        parent::__construct($fields);

        $this->setValue($fields['VALUE']);

        if ($setEntityFields) {
            $this->setUserFieldId($fields['USER_FIELD_ID']);
        }
    }

    /**
     * @return string
     */
    protected function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return $this
     */
    protected function setValue($value)
    {
        $value = (string)$value;
        if ($value) {
            $this->value = $value;
        }
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    protected function setUserFieldId($value)
    {
        $value = (int)$value;
        if ($value > 1) {
            $this->userFieldId = $value;
        }
        return $this;
    }

    public function getUserFieldId()
    {
        return $this->userFieldId;
    }

    /**
     * @return $this
     * @throws \Bitrix\Main\SystemException
     */
    public function save()
    {
        // не позволяем обновлять или создавать элементы без явного указания параметров 'VALUE', 'XML_ID', 'USER_FIELD_ID'
        if ((int)$this->userFieldId < 1) {
            throw new \Bitrix\Main\SystemException(Loc::getMessage('C.UFENUMELEMENT.EMPTY_USER_FIELD_ID_ERROR'));
        }
        if (mb_strlen($this->value) < 1) {
            throw new \Bitrix\Main\SystemException(Loc::getMessage('C.UFENUMELEMENT.EMPTY_VALUE_ERROR'));
        }
        if (mb_strlen($this->xmlId) < 1) {
            throw new \Bitrix\Main\SystemException(Loc::getMessage('C.UFENUMELEMENT.EMPTY_XML_ID_ERROR'));
        }

//        $action = $this->id ? "update" : "add";

//        if($action == "update") {
        if ($this->id) {
            $objEnum = new \CUserFieldEnum();
            $res = $objEnum->SetEnumValues($this->userFieldId, array(
                $this->id => array(
                    "XML_ID" => $this->xmlId,
                    "VALUE" => $this->value,
                    "SORT" => $this->sort
                )
            ));

            if (!$res) {
                static::throwCrudException(Loc::getMessage('C.UFENUMELEMENT.CRUD_ACTION_UPDATE'), true);
            }
        } else {
            try {
                // Проверка, существует ли поле
                $el = static::getOneByFilter(
                    array(
                        'XML_ID' => $this->getXmlId(),
                        'USER_FIELD_ID' => $this->getUserFieldId()
                    ),
                    null,
                    array(),
                    false
                );
                $this->setId($el->getId());
            } catch (\Bitrix\Main\ObjectNotFoundException $e) {
                $objEnum = new \CUserFieldEnum();
                $res = $objEnum->SetEnumValues($this->userFieldId, array(
                    "n0" => array(
                        "XML_ID" => $this->xmlId,
                        "VALUE" => $this->value,
                        "SORT" => $this->sort,
                    ),
                ));

                if (!$res) {
                    static::throwCrudException(Loc::getMessage('C.UFENUMELEMENT.CRUD_ACTION_CREATE'), true);
                } else {
                    // Метод битрикса не возвращает id созданного элемента, поэтому делаем дополнительный запрос
                    $el = static::getOneByFilter(
                        array(
                            'XML_ID' => $this->getXmlId(),
                            'USER_FIELD_ID' => $this->getUserFieldId()
                        ),
                        null,
                        array(),
                        false
                    );
                    $this->setId($el->getId());
                }
            }
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function delete()
    {
        if ($this->id > 0) {
            return static::deleteById($this->id, $this->userFieldId);
        }

        return false;
    }


    protected static function deleteById($id, $userFieldId = null)
    {
        $id = (int)$id;
        $userFieldId = (int)$userFieldId;
        if ($id < 1) {
            throw new \Bitrix\Main\ArgumentNullException(Loc::getMessage('C.UFENUMELEMENT.EMPTY_ID_ERROR'));
        }
        if ($userFieldId < 1) {
            throw new \Bitrix\Main\ArgumentNullException(Loc::getMessage('C.UFENUMELEMENT.EMPTY_USER_FIELD_ID_ERROR'));
        }

        $objEnum = new \CUserFieldEnum();
        $res = $objEnum->SetEnumValues($userFieldId, array(
            $id => array(
                'DEL' => 'Y',
            )
        ));

        if (!$res) {
            static::throwCrudException(Loc::getMessage('C.UFENUMELEMENT.CRUD_ACTION_DELETE'), true);
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
        $objEnum = new \CUserFieldEnum();
        $dbRes = $objEnum->GetList($arOrder, $arFilter);
        return $dbRes;
    }
}