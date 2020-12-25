<?php namespace Intervolga\Sed\Admin;

use Bitrix\Main\Localization\Loc;
use Intervolga\Sed\Entities\ProcessTaskGroup;

Loc::loadMessages(__FILE__);

class ListTaskGroup extends SettingsList
{
    /** @var ProcessTaskGroup[] data */
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
        return Loc::getMessage('SED.ADMIN_TASK_GROUP_LIST.ADD_BTN_LABEL');
    }

    protected function initFields()
    {
        parent::initFields();

        $this->fields['NAME'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_TASK_GROUP_LIST.NAME_FIELD_LABEL'),
            'TYPE' => 'INPUT',
            'USED_BY_FILTER' => 'Y'
        );
    }

    protected function getData()
    {
        if($this->request->getQuery('PROCESS')) {
            $this->data = ProcessTaskGroup::getListByFilter(
                array_merge($this->filter, array('PROCESS_ID' => $this->request->getQuery('PROCESS'))),
                array($this->sort['BY'] => $this->sort['ORDER'])
            );
        }
        else {
            $this->errors[] = Loc::getMessage('SED.ADMIN_TASK_GROUP_LIST.NO_PROCESS_ID');
        }
    }

    protected function initTableData()
    {
        $result = array();

        if(!empty($this->data)) {
            foreach ($this->data as $taskGroup) {
                $result[] = array(
                    'ID' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => $taskGroup->getId()
                    ),
                    'NAME' => array(
                        'TYPE' => 'URL',
                        'URL' => $this->params['DETAIL_PAGE_URL'] . '&' . $this->params['DETAIL_PAGE_PARAM'] . '=' . $taskGroup->getId(),
                        'VALUE' => $taskGroup->getName()
                    )
                );
            }
        }

        return $result;
    }
}