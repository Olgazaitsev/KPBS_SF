<?php namespace Intervolga\Sed\Admin;

use Bitrix\Main\Localization\Loc;
use Intervolga\Sed\Entities\TaskTypeElement as TType;

Loc::loadMessages(__FILE__);

class DetailType extends SettingsDetail
{
    protected function getListButtonLabel()
    {
        return Loc::getMessage('CTS.ADMIN_TTYPE_DETAIL.LIST_BTN');
    }

    protected function getPageHeader()
    {
        return Loc::getMessage('CTS.ADMIN_TTYPE_DETAIL.PAGE_HEADER');
    }

    protected function getDetailEntityPageUrl()
    {
        return $this->params['DETAIL_PAGE_URL'] . '?TYPE=' . $this->data->getId();
    }

    protected function getFieldsDisplayData()
    {
        $result = $this->fields;

        if($this->data instanceof TType && $this->data->getId()) {

            $idField = array(
                'ID' => array(
                    'LABEL' => 'ID',
                    'VALUE' => $this->data->getId()
                )
            );

            $result['VALUE']['VALUE'] = $this->data->getName();
            $result['XML_ID']['VALUE'] = $this->data->getCode();
            $result['SORT']['VALUE'] = $this->data->getSort();

            $result = $idField + $result;
        }

        return $result;
    }

    protected function initFields()
    {
        $this->fields = array(
            'VALUE' => array(
                'LABEL' => Loc::getMessage('CTS.ADMIN_TTYPE_DETAIL.FIELDS.VALUE'),
                'EDITABLE' => 'Y',
                'REQUIRED' => 'Y',
                'TYPE' => 'INPUT'
            ),
            'XML_ID' => array(
                'LABEL' => Loc::getMessage('CTS.ADMIN_TTYPE_DETAIL.FIELDS.XML_ID'),
                'EDITABLE' => 'Y',
                'REQUIRED' => 'Y',
                'TYPE' => 'INPUT'
            ),
            'SORT' => array(
                'LABEL' => Loc::getMessage('CTS.ADMIN_TTYPE_DETAIL.FIELDS.SORT'),
                'EDITABLE' => 'Y',
                'TYPE' => 'INPUT'
            )
        );
    }

    protected function getData()
    {
        $taskTypeId = $this->request->getQuery('TYPE');
        if($taskTypeId) {
            try {
                $this->data = TType::getById($taskTypeId);
            }
            catch (\Bitrix\Main\ObjectNotFoundException $e) {
                $this->errors[] = Loc::getMessage('CTS.ADMIN_TTYPE_DETAIL.TTYPE_NOT_FOUND');
            }
        }
    }

    protected function saveOrUpdate()
    {
        if(!($this->data instanceof TType)) {
            $this->data = TType::createEmpty();
        }

        $taskTypeBeforeSave = clone $this->data;

        try {
            $this->data->setName($this->request->getPost('VALUE'))
                ->setCode($this->request->getPost('XML_ID'))
                ->setSort($this->request->getPost('SORT'))
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
            $this->notifications[] = Loc::getMessage('CTS.ADMIN_TTYPE_DETAIL.SAVE_ERROR', array('#ERROR_DESC#' => $e->getMessage()));
        }
    }
}