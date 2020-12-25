<?php namespace Intervolga\Sed\Admin;

use Bitrix\Main\Localization\Loc;
use Intervolga\Sed\Entities\Process;

Loc::loadMessages(__FILE__);

class ListProcess extends SettingsList
{
    /** @var Process[] data */
    protected $data;


    protected function getAddBtnLabel()
    {
        return Loc::getMessage('SED.ADMIN_PROCESS_LIST.ADD_BTN_LABEL');
    }

    protected function initFields()
    {
        parent::initFields();

        $this->fields['NAME'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_PROCESS_LIST.NAME_FIELD_LABEL'),
            'TYPE' => 'INPUT',
            'USED_BY_FILTER' => 'Y'
        );
    }

    protected function getData()
    {
        $this->data = Process::getListByFilter($this->filter, array($this->sort['BY'] => $this->sort['ORDER']));
    }

    protected function initTableData()
    {
        $result = array();

        if(!empty($this->data)) {
            foreach ($this->data as $process) {
                $result[] = array(
                    'ID' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => $process->getId()
                    ),
                    'NAME' => array(
                        'TYPE' => 'URL',
                        'URL' => $this->params['DETAIL_PAGE_URL'] . '?' . $this->params['DETAIL_PAGE_PARAM'] . '=' . $process->getId(),
                        'VALUE' => $process->getName()
                    )
                );
            }
        }

        return $result;
    }
}