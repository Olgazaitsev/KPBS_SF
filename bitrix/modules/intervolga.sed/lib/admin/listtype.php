<?php namespace Intervolga\Sed\Admin;

use Bitrix\Main\Localization\Loc;
use Intervolga\Sed\Entities\TaskTypeElement as TType;

Loc::loadMessages(__FILE__);

class ListType extends SettingsList
{
    /** @var TType[] data */
    protected $data;


    protected function getAddBtnLabel()
    {
        return Loc::getMessage('CTS.ADMIN_TTYPE_LIST.ADD_BTN_LABEL');
    }

    protected function initFields()
    {
        parent::initFields();

        $this->fields['VALUE'] = array(
            'LABEL' => Loc::getMessage('CTS.ADMIN_TTYPE_LIST.NAME_FIELD_LABEL'),
            'TYPE' => 'INPUT',
            'USED_BY_FILTER' => 'Y'
        );

        $this->fields['XML_ID'] = array(
            'LABEL' => Loc::getMessage('CTS.ADMIN_TTYPE_LIST.CODE_FIELD_LABEL'),
            'TYPE' => 'INPUT',
            'USED_BY_FILTER' => 'Y'
        );

        $this->fields['SORT'] = array(
            'LABEL' => Loc::getMessage('CTS.ADMIN_TTYPE_LIST.SORT_FIELD_LABEL'),
            'TYPE' => 'INPUT',
            'USED_BY_FILTER' => 'Y'
        );
    }

    protected function getData()
    {
        $this->data = TType::getListByFilter($this->filter, null, array($this->sort['BY'] => $this->sort['ORDER']));
    }

    protected function initTableData()
    {
        $result = array();

        if(!empty($this->data)) {
            foreach ($this->data as $taskType) {
                $result[] = array(
                    'ID' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => $taskType->getId()
                    ),
                    'VALUE' => array(
                        'TYPE' => 'URL',
                        'URL' => $this->params['DETAIL_PAGE_URL'] . '?' . $this->params['DETAIL_PAGE_PARAM'] . '=' . $taskType->getId(),
                        'VALUE' => $taskType->getName()
                    ),
                    'XML_ID' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => $taskType->getCode()
                    ),
                    'SORT' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => $taskType->getSort()
                    ),
                );
            }
        }

        return $result;
    }
}