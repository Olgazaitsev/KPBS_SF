<?php namespace Intervolga\Sed\Admin;

use Bitrix\Main\Localization\Loc;
use Intervolga\Sed\Entities\Process;

Loc::loadMessages(__FILE__);

class DetailProcess extends SettingsDetail
{
    protected $savedDataId;


    protected function getListButtonLabel()
    {
        return Loc::getMessage('SED.ADMIN_PROCESS_DETAIL.LIST_BTN');
    }

    protected function getPageHeader()
    {
        return Loc::getMessage('SED.ADMIN_PROCESS_DETAIL.PAGE_HEADER');
    }

    protected function getDetailEntityPageUrl()
    {
        return $this->params['DETAIL_PAGE_URL'] . '?PROCESS=' . $this->savedDataId;
    }

    protected function getFieldsDisplayData()
    {
        $result = $this->fields;

        if($this->data instanceof Process && $this->data->getId()) {

            $idField = array(
                'ID' => array(
                    'LABEL' => 'ID',
                    'VALUE' => $this->data->getId()
                )
            );

            $result['NAME']['VALUE'] = $this->data->getName();

            $result = $idField + $result;
        }

        return $result;
    }

    protected function initFields()
    {
        $this->fields = array(
            'NAME' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_PROCESS_DETAIL.FIELDS.NAME'),
                'EDITABLE' => 'Y',
                'REQUIRED' => 'Y',
                'TYPE' => 'INPUT'
            )
        );
    }

    protected function getData()
    {
        $taskTypeId = $this->request->getQuery('PROCESS');
        if($taskTypeId) {
            try {
                $this->data = Process::getById($taskTypeId);
            }
            catch (\Bitrix\Main\ObjectNotFoundException $e) {
                $this->errors[] = Loc::getMessage('SED.ADMIN_PROCESS_DETAIL.PROCESS_NOT_FOUND');
            }
        }
    }

    protected function saveOrUpdate()
    {
        if(!($this->data instanceof Process)) {
            $this->data = Process::createEmpty();
        }

        $taskTypeBeforeSave = clone $this->data;

        try {
            $this->savedDataId = $this->data->setName($this->request->getPost('NAME'))
                ->save();

            if(!($taskTypeBeforeSave->getId())) {
                $this->elementCreated = true;
            }
            else {
                $this->elementUpdated = true;
            }
        }
        catch (\Exception $e) {
            $this->data = $taskTypeBeforeSave;
            $this->notifications[] = Loc::getMessage('SED.ADMIN_PROCESS_DETAIL.SAVE_ERROR', array('#ERROR_DESC#' => $e->getMessage()));
        }
    }
}