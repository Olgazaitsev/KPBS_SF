<?php namespace Intervolga\Sed\Admin;

use Bitrix\Main\Localization\Loc;
use Intervolga\Sed\Entities\ProcessTaskType;
use Intervolga\Sed\Entities\TaskTypeElement;

Loc::loadMessages(__FILE__);

class ListTaskType extends SettingsList
{
    /** @var TaskTypeElement[] $data */
    protected $data;
    /** @var ProcessTaskType[] $processTaskTypes */
    protected $processTaskTypes;


    protected function prepareParams()
    {
        parent::prepareParams();

        $this->extraFormInputs = array(
            array(
                'NAME' => 'PROCESS',
                'VALUE' => $this->request->getQuery('PROCESS')
            )
        );

//        $this->params['DETAIL_PAGE_URL'] .= '?PROCESS=' . $this->request->getQuery('PROCESS');
    }

    protected function getAddBtnLabel()
    {
        return Loc::getMessage('SED.ADMIN_TTYPE_LIST.ADD_BTN_LABEL');
    }

    protected function initFields()
    {
        parent::initFields();

        $this->fields['VALUE'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_TTYPE_LIST.NAME_FIELD_LABEL'),
            'TYPE' => 'INPUT',
            'USED_BY_FILTER' => 'Y'
        );

        $this->fields['XML_ID'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_TTYPE_LIST.CODE_FIELD_LABEL'),
            'TYPE' => 'INPUT',
            'USED_BY_FILTER' => 'Y'
        );

        $this->fields['SORT'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_TTYPE_LIST.SORT_FIELD_LABEL'),
            'TYPE' => 'INPUT',
            'USED_BY_FILTER' => 'Y'
        );
    }

    protected function getData()
    {
        if($this->request->getQuery('PROCESS')) {

            $this->processTaskTypes = ProcessTaskType::getListByFilter(array('PROCESS_ID' => $this->request->getQuery('PROCESS')));
            $ttypeIds = ProcessTaskType::getTTypeIdsByEntityList($this->processTaskTypes);

            if(!empty($ttypeIds)) {
                $this->data = TaskTypeElement::getListByFilter(array_merge($this->filter, array('ID' => $ttypeIds)), null, array($this->sort['BY'] => $this->sort['ORDER']));
            }
        }
        else {
            $this->errors[] = Loc::getMessage('SED.ADMIN_TTYPE_LIST.NO_PROCESS_ID');
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

    protected function fillResult()
    {
        parent::fillResult();

        $this->result['TABLE']['ADD_ITEM_BTN']['URL'] = $this->params['CREATE_PROCESS_TTYPE_PAGE_URL'] . '?PROCESS=' . $this->request->getQuery('PROCESS');
    }
}