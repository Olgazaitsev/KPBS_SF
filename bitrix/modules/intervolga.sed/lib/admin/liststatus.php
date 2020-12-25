<?php namespace Intervolga\Sed\Admin;

use Bitrix\Main\Localization\Loc;
use Intervolga\Sed\Entities\ProcessStatus;

Loc::loadMessages(__FILE__);

class ListStatus extends SettingsList
{
    /** @var ProcessStatus[] data */
    protected $data;


    protected function prepareParams()
    {
        parent::prepareParams();

        $this->extraFormInputs = array(
            array(
                'NAME' => 'PROCESS',
                'VALUE' => $this->request->getQuery('PROCESS')
            )
        );

        $this->params['DETAIL_PAGE_URL'] .= '?PROCESS=' . $this->request->getQuery('PROCESS');
    }

    protected function getAddBtnLabel()
    {
        return Loc::getMessage('SED.ADMIN_STATUS_LIST.ADD_BTN_LABEL');
    }

    protected function initFields()
    {
        parent::initFields();

        $this->fields['NAME'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_STATUS_LIST.NAME_FIELD_LABEL'),
            'TYPE' => 'INPUT',
            'USED_BY_FILTER' => 'Y'
        );

        $this->fields['CODE'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_STATUS_LIST.CODE_FIELD_LABEL'),
            'TYPE' => 'INPUT',
            'USED_BY_FILTER' => 'Y'
        );
    }

    protected function getData()
    {
        if($this->request->getQuery('PROCESS')) {
            $this->data = ProcessStatus::getListByFilter(
                array_merge($this->filter, array('PROCESS_ID' => $this->request->getQuery('PROCESS'))),
                array($this->sort['BY'] => $this->sort['ORDER'])
            );
        }
        else {
            $this->errors[] = Loc::getMessage('SED.ADMIN_STATUS_LIST.NO_PROCESS_ID');
        }
    }

    protected function initTableData()
    {
        $result = array();

        if(!empty($this->data)) {
            foreach ($this->data as $status) {
                $result[] = array(
                    'ID' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => $status->getId()
                    ),
                    'NAME' => array(
                        'TYPE' => 'URL',
                        'URL' => $this->params['DETAIL_PAGE_URL'] . '&' . $this->params['DETAIL_PAGE_PARAM'] . '=' . $status->getId(),
                        'VALUE' => $status->getName()
                    ),
                    'CODE' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => $status->getCode()
                    )
                );
            }
        }

        return $result;
    }
}