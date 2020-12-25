<?php namespace Intervolga\Sed\Admin;

use Bitrix\Main\Localization\Loc;
use Intervolga\Sed\Entities\ParticipantRole;

Loc::loadMessages(__FILE__);

class ListRole extends SettingsList
{
    /** @var ParticipantRole[] data */
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
        return Loc::getMessage('SED.ADMIN_ROLE_LIST.ADD_BTN_LABEL');
    }

    protected function initFields()
    {
        parent::initFields();

        $this->fields['NAME'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_ROLE_LIST.NAME_FIELD_LABEL'),
            'TYPE' => 'INPUT',
            'USED_BY_FILTER' => 'Y'
        );

        $this->fields['DEFAULT_USER_ID'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_ROLE_LIST.DEFAULT_USER_FIELD_LABEL'),
            'TYPE' => 'INPUT',
            'USED_BY_FILTER' => 'Y'
        );

        $this->fields['IS_INITIATOR'] = array(
            'LABEL' => Loc::getMessage('SED.ADMIN_ROLE_LIST.IS_INITIATOR_FIELD_LABEL'),
            'TYPE' => 'INPUT',
            'USED_BY_FILTER' => 'N'
        );
    }

    protected function getData()
    {
        if($this->request->getQuery('PROCESS')) {
            $this->data = ParticipantRole::getListByFilter(
                array_merge($this->filter, array('PROCESS_ID' => $this->request->getQuery('PROCESS'))),
                array($this->sort['BY'] => $this->sort['ORDER'])
            );
        }
        else {
            $this->errors[] = Loc::getMessage('SED.ADMIN_ROLE_LIST.NO_PROCESS_ID');
        }
    }

    protected function initTableData()
    {
        $result = array();

        if(!empty($this->data)) {
            foreach ($this->data as $role) {
                $result[] = array(
                    'ID' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => $role->getId()
                    ),
                    'NAME' => array(
                        'TYPE' => 'URL',
                        'URL' => $this->params['DETAIL_PAGE_URL'] . '&' . $this->params['DETAIL_PAGE_PARAM'] . '=' . $role->getId(),
                        'VALUE' => $role->getName()
                    ),
                    'DEFAULT_USER_ID' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => $role->getDefaultUserId()
                    ),
                    'IS_INITIATOR' => array(
                        'TYPE' => 'TEXT',
                        'VALUE' => ($role->isInitiator()) ? Loc::getMessage('SED.ADMIN_ROLE_LIST.YES') : Loc::getMessage('SED.ADMIN_ROLE_LIST.NO')
                    ),
                );
            }
        }

        return $result;
    }
}