<?php namespace Intervolga\Sed\Admin;

use Bitrix\Main\Localization\Loc;
use Intervolga\Sed\Entities\ProcessTaskGroup;
use Intervolga\Sed\Entities\Process;

Loc::loadMessages(__FILE__);

class DetailTaskGroup extends SettingsDetail
{
    /** @var Process $process */
    protected $process;
    protected $savedDataId;


    protected function getListButtonLabel()
    {
        return Loc::getMessage('SED.ADMIN_TASK_GROUP_DETAIL.LIST_BTN');
    }

    protected function getPageHeader()
    {
        return Loc::getMessage('SED.ADMIN_TASK_GROUP_DETAIL.PAGE_HEADER');
    }

    protected function prepareParams()
    {
        parent::prepareParams();
        $this->initProcess();
    }

    protected function getDetailEntityPageUrl()
    {
        return $this->params['DETAIL_PAGE_URL'] . '?PROCESS=' . $this->process->getId() . '&GROUP=' . $this->savedDataId;
    }

    protected function initProcess()
    {
        $processId = $this->request->getQuery('PROCESS');
        if($processId) {
            try {
                $this->process = Process::getById($processId);
                $this->params['LIST_PAGE_URL'] .= '?PROCESS=' . $processId;
            }
            catch (\Bitrix\Main\ObjectNotFoundException $e) {
                $this->errors[] = Loc::getMessage('SED.ADMIN_TASK_GROUP_DETAIL.PROCESS_NOT_FOUND');
            }
        }
        else {
            $this->errors[] = Loc::getMessage('SED.ADMIN_TASK_GROUP_DETAIL.EMPTY_PROCESS_PARAM');
        }
    }

    protected function initFields()
    {
        $this->fields = array(
            'NAME' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_TASK_GROUP_DETAIL.FIELDS.NAME'),
                'EDITABLE' => 'Y',
                'REQUIRED' => 'Y',
                'TYPE' => 'INPUT'
            )
        );
    }

    protected function getFieldsDisplayData()
    {
        $result = $this->fields;

        if($this->data instanceof ProcessTaskGroup && $this->data->getId()) {

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

    protected function getData()
    {
        if($this->process instanceof Process) {
            $statusId = $this->request->getQuery('GROUP');
            if($statusId) {
                try {
                    $this->data = ProcessTaskGroup::getOneByFilter(array(
                        'ID' => $statusId,
                        'PROCESS_ID' =>  $this->process->getId()
                    ));
                }
                catch (\Bitrix\Main\ObjectNotFoundException $e) {
                    $this->errors[] = Loc::getMessage('SED.ADMIN_TASK_GROUP_DETAIL.TASK_GROUP_NOT_FOUND');
                }
            }
        }
    }

    protected function saveOrUpdate()
    {
        if (!($this->data instanceof ProcessTaskGroup)) {
            $this->data = ProcessTaskGroup::createEmpty();
        }

        $statusBeforeSave = clone $this->data;

        try {
            $this->data->setName($this->request->getPost('NAME'));
            $this->data->setProcessId($this->process->getId());

            $this->savedDataId = $this->data->save();

            if (!($statusBeforeSave->getId())) {
                $this->elementCreated = true;
            } else {
                $this->elementUpdated = true;
            }
        } catch (\Exception $e) {
            $this->data = $statusBeforeSave;
            $this->errors[] = Loc::getMessage('SED.ADMIN_TASK_GROUP_DETAIL.SAVE_ERROR', array('#ERROR_DESC#' => $e->getMessage()));
        }
    }
}