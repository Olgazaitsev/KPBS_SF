<?php namespace Intervolga\Sed\Admin;

use Bitrix\Main\Localization\Loc;
use Intervolga\Sed\Entities\TaskStatusElement as TStatus;
use Intervolga\Sed\Entities\TaskTypeElement as TType;

Loc::loadMessages(__FILE__);

class C_DetailStatus extends SettingsDetail
{
    /** @var TType $taskType */
    protected $taskType;


    protected function prepareParams()
    {
        parent::prepareParams();
        $this->initTaskType();
    }

    protected function getListButtonLabel()
    {
        return Loc::getMessage('CTS.ADMIN_TSTATUS_DETAIL.LIST_BTN');
    }

    protected function getPageHeader()
    {
        return Loc::getMessage('CTS.ADMIN_TSTATUS_DETAIL.PAGE_HEADER');
    }

    protected function getDetailEntityPageUrl()
    {
        return $this->params['DETAIL_PAGE_URL'] . '?TYPE=' . $this->taskType->getId() . '&STATUS=' . $this->data->getId();
    }

    protected function initTaskType()
    {
        $taskTypeId = $this->request->getQuery('TYPE');
        if($taskTypeId) {
            try {
                $this->taskType = TType::getById($taskTypeId);
                $this->params['LIST_PAGE_URL'] .= '?TYPE=' . $taskTypeId;
            }
            catch (\Bitrix\Main\ObjectNotFoundException $e) {
                $this->errors[] = Loc::getMessage('CTS.ADMIN_TSTATUS_DETAIL.TTYPE_NOT_FOUND');
            }
        }
        else {
            $this->errors[] = Loc::getMessage('CTS.ADMIN_TSTATUS_DETAIL.EMPTY_TTYPE_PARAM');
        }
    }

    protected function initFields()
    {
        $this->fields = array(
            'VALUE' => array(
                'LABEL' => Loc::getMessage('CTS.ADMIN_TSTATUS_DETAIL.FIELDS.VALUE'),
                'EDITABLE' => 'Y',
                'REQUIRED' => 'Y',
                'TYPE' => 'INPUT'
            ),
            'CODE' => array(
                'LABEL' => Loc::getMessage('CTS.ADMIN_TSTATUS_DETAIL.FIELDS.CODE'),
                'EDITABLE' => 'Y',
                'REQUIRED' => 'Y',
                'TYPE' => 'INPUT'
            ),
            'NATIVE_STATUS' => array(
                'LABEL' => Loc::getMessage('CTS.ADMIN_TSTATUS_DETAIL.FIELDS.NATIVE_STATUS'),
                'EDITABLE' => 'Y',
                'REQUIRED' => 'Y',
                'TYPE' => 'SELECT',
                'OPTIONS' => array()
            ),
            'SORT' => array(
                'LABEL' => Loc::getMessage('CTS.ADMIN_TSTATUS_DETAIL.FIELDS.SORT'),
                'EDITABLE' => 'Y',
                'TYPE' => 'INPUT'
            )
        );

        $nativeStatusNames = TStatus::getNativeTaskStatusNames();
        foreach($nativeStatusNames as $nativeStatusId => $nativeStatusName) {
            $this->fields['NATIVE_STATUS']['OPTIONS'][] = array(
                'NAME' => $nativeStatusName,
                'VALUE' => $nativeStatusId
            );
        }
    }

    protected function getFieldsDisplayData()
    {
        $result = $this->fields;

        if($this->data instanceof TStatus && $this->data->getId()) {

            $idField = array(
                'ID' => array(
                    'LABEL' => 'ID',
                    'VALUE' => $this->data->getId()
                )
            );

            $result['VALUE']['VALUE'] = $this->data->getName();
            $result['CODE']['VALUE'] = $this->data->getCode();
            $result['NATIVE_STATUS']['VALUE'] = $this->data->getNativeTaskStatus();
            $result['SORT']['VALUE'] = $this->data->getSort();

            $result = $idField + $result;
        }

        return $result;
    }

    protected function getData()
    {
        if($this->taskType instanceof TType) {
            $taskStatusId = $this->request->getQuery('STATUS');
            if($taskStatusId) {
                try {
                    $this->data = TStatus::getById($taskStatusId, $this->taskType);
                }
                catch (\Bitrix\Main\ObjectNotFoundException $e) {
                    $this->errors[] = Loc::getMessage('CTS.ADMIN_TSTATUS_DETAIL.TSTATUS_NOT_FOUND');
                }
            }
        }
    }

    protected function saveOrUpdate()
    {
        if(!($this->data instanceof TStatus)) {
            $this->data = TStatus::createEmpty($this->taskType);
        }

        $taskTypeBeforeSave = clone $this->data;

        try {
            $this->data->setName($this->request->getPost('VALUE'))
                ->setCode($this->request->getPost('CODE'))
                ->setNativeTaskStatus($this->request->getPost('NATIVE_STATUS'))
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
            $this->errors[] = Loc::getMessage('CTS.ADMIN_TSTATUS_DETAIL.SAVE_ERROR', array('#ERROR_DESC#' => $e->getMessage()));
        }
    }

    protected function getListButtonsInfo()
    {
        return array_merge(parent::getListButtonsInfo(), array(
            array(
                'LABEL' => Loc::getMessage('CTS.ADMIN_TSTATUS_DETAIL.PARENT_LIST_BTN'),
                'URL' => $this->params['PARENT_LIST_PAGE_URL']
            )
        ));
    }
}