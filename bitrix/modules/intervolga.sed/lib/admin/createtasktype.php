<?php namespace Intervolga\Sed\Admin;

use Bitrix\Main\Localization\Loc;
use Intervolga\Sed\Entities\Process;

Loc::loadMessages(__FILE__);

class CreateTaskType extends SettingsDetail
{
    /** @var Process $process */
    protected $process;


    protected function getListButtonLabel()
    {
        return Loc::getMessage('SED.ADMIN_TTYPE_CREATE.LIST_BTN');
    }

    protected function getPageHeader()
    {
        return Loc::getMessage('SED.ADMIN_TTYPE_CREATE.PAGE_HEADER');
    }

    protected function prepareParams()
    {
        parent::prepareParams();
        $this->initProcess();
    }

    protected function initProcess()
    {
        $processId = $this->request->getQuery('PROCESS');
        if($processId) {
            try {
                $this->process = Process::getById($processId);
                $this->params['LIST_PAGE_URL'] = $this->params['LIST_PAGE_URL'] .= '?PROCESS=' . $this->process->getId();
            }
            catch (\Bitrix\Main\ObjectNotFoundException $e) {
                $this->errors[] = Loc::getMessage('SED.ADMIN_TTYPE_CREATE.PROCESS_NOT_FOUND');
            }
        }
        else {
            $this->errors[] = Loc::getMessage('SED.ADMIN_TTYPE_CREATE.EMPTY_PROCESS_PARAM');
        }
    }

    protected function getDetailEntityPageUrl()
    {
        return $this->params['DETAIL_PAGE_URL'] . '?TYPE=' . $this->data->getId();
    }

    protected function getFieldsDisplayData()
    {
        return $this->fields;
    }

    protected function initFields()
    {
        $this->fields = array(
            'VALUE' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_TTYPE_CREATE.FIELDS.VALUE'),
                'EDITABLE' => 'Y',
                'REQUIRED' => 'Y',
                'TYPE' => 'INPUT'
            ),
            'XML_ID' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_TTYPE_CREATE.FIELDS.XML_ID'),
                'EDITABLE' => 'Y',
                'REQUIRED' => 'Y',
                'TYPE' => 'INPUT'
            ),
            'SORT' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_TTYPE_CREATE.FIELDS.SORT'),
                'EDITABLE' => 'Y',
                'TYPE' => 'INPUT'
            )
        );
    }

    protected function getData()
    {
        return null;
    }

    protected function saveOrUpdate()
    {
        try {
            $this->data = Process::createNonInitiatorTType(
                $this->process->getId(),
                $this->request->getPost('VALUE'),
                $this->request->getPost('XML_ID'),
                $this->request->getPost('SORT')
            );
            $this->elementCreated = true;
        }
        catch (\Exception $e) {
            $this->notifications[] = Loc::getMessage('SED.ADMIN_TTYPE_CREATE.SAVE_ERROR', array('#ERROR_DESC#' => $e->getMessage()));
        }
    }
}