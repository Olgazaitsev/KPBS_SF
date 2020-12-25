<?php namespace Intervolga\Sed\Admin;

use Bitrix\Main\Localization\Loc;
use Intervolga\Sed\Entities\TaskStatusElement as TStatus;

Loc::loadMessages(__FILE__);

class C_ListStatus extends SettingsList
{
    /** @var TStatus[] data */
    protected $data;


    public function getTypeId()
    {
        return (int)$this->request->getQuery('TYPE');
    }

    protected function prepareParams()
    {
        parent::prepareParams();

        $this->extraFormInputs = array(
            array(
                'NAME' => 'TYPE',
                'VALUE' => $this->request->getQuery('TYPE')
            )
        );

        $this->params['DETAIL_PAGE_URL'] .= '?TYPE=' . $this->request->getQuery('TYPE');
    }

    protected function getAddBtnLabel()
    {
        return Loc::getMessage('CTS.ADMIN_TSTATUS_LIST.ADD_BTN_LABEL');
    }

    protected function initFields()
    {
        parent::initFields();

        $this->fields['VALUE'] = array(
            'LABEL' => Loc::getMessage('CTS.ADMIN_TSTATUS_LIST.NAME_FIELD_LABEL'),
            'TYPE' => 'INPUT',
            'USED_BY_FILTER' => 'Y'
        );

        $this->fields['CODE'] = array(
            'LABEL' => Loc::getMessage('CTS.ADMIN_TSTATUS_LIST.CODE_FIELD_LABEL'),
            'TYPE' => 'INPUT',
            'USED_BY_FILTER' => 'N'
        );

        $this->fields['REAL_STATUS'] = array(
            'LABEL' => Loc::getMessage('CTS.ADMIN_TSTATUS_LIST.REAL_STATUS_FIELD_LABEL'),
            'TYPE' => 'INPUT',
            'USED_BY_FILTER' => 'N'
        );

        $this->fields['SORT'] = array(
            'LABEL' => Loc::getMessage('CTS.ADMIN_TSTATUS_LIST.SORT_FIELD_LABEL'),
            'TYPE' => 'INPUT',
            'USED_BY_FILTER' => 'Y'
        );
    }

    protected function getData()
    {
        try {
            $this->data = TStatus::getListByFilter($this->filter, $this->request->getQuery('TYPE'), array($this->sort['BY'] => $this->sort['ORDER']));
        }
        catch (\Exception $e) {
            $this->errors[] = Loc::getMessage('CTS.ADMIN_TSTATUS_LIST.ERR_ID_NOT_FOUND');
        }
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
                        'URL' => $this->params['DETAIL_PAGE_URL'] . '&' . $this->params['DETAIL_PAGE_PARAM'] . '=' . $taskType->getId(),
                        'VALUE' => $taskType->getName()
                    ),
                    'CODE' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => $taskType->getCode()
                    ),
                    'REAL_STATUS' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => TStatus::getNativeStatusNameById($taskType->getNativeTaskStatus())
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