<?php namespace Intervolga\Sed\Admin;

use Bitrix\Main\Localization\Loc;
use Intervolga\Sed\Entities\ProcessStatus;
use Intervolga\Sed\Entities\Process;

Loc::loadMessages(__FILE__);

class DetailStatus extends SettingsDetail
{
    /** @var Process $process */
    protected $process;
    protected $savedDataId;


    protected function getListButtonLabel()
    {
        return Loc::getMessage('SED.ADMIN_STATUS_DETAIL.LIST_BTN');
    }

    protected function getPageHeader()
    {
        return Loc::getMessage('SED.ADMIN_STATUS_DETAIL.PAGE_HEADER');
    }

    protected function prepareParams()
    {
        parent::prepareParams();
        $this->initProcess();
    }

    protected function getDetailEntityPageUrl()
    {
        return $this->params['DETAIL_PAGE_URL'] . '?PROCESS=' . $this->process->getId() . '&STATUS=' . $this->savedDataId;
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
                $this->errors[] = Loc::getMessage('SED.ADMIN_STATUS_DETAIL.PROCESS_NOT_FOUND');
            }
        }
        else {
            $this->errors[] = Loc::getMessage('SED.ADMIN_STATUS_DETAIL.EMPTY_PROCESS_PARAM');
        }
    }

    protected function initFields()
    {
        $this->fields = array(
            'IS_REQUIRED' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_STATUS_DETAIL.FIELDS.IS_REQUIRED'),
                'VALUE' => Loc::getMessage('SED.ADMIN_STATUS_DETAIL.NO')
            ),
            'NAME' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_STATUS_DETAIL.FIELDS.NAME'),
                'EDITABLE' => 'Y',
                'REQUIRED' => 'Y',
                'TYPE' => 'INPUT'
            ),
            'CODE' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_STATUS_DETAIL.FIELDS.CODE'),
                'EDITABLE' => 'Y',
                'REQUIRED' => 'Y',
                'TYPE' => 'INPUT'
            ),
        );
    }

    protected function getFieldsDisplayData()
    {
        $result = $this->fields;

        if($this->data instanceof ProcessStatus && $this->data->getId()) {

            $idField = array(
                'ID' => array(
                    'LABEL' => 'ID',
                    'VALUE' => $this->data->getId()
                )
            );

            $result['NAME']['VALUE'] = $this->data->getName();
            $result['CODE']['VALUE'] = $this->data->getCode();

            if(ProcessStatus::isDefault($this->data->getCode())) {
                $result['IS_REQUIRED']['VALUE'] = Loc::getMessage('SED.ADMIN_STATUS_DETAIL.YES');
                $result['CODE']['EDITABLE'] = 'N';
                $result['CODE']['REQUIRED'] = 'N';
                $result['CODE']['TYPE'] = 'TEXT';
            }

            $result = $idField + $result;
        }

        return $result;
    }

    protected function getData()
    {
        if($this->process instanceof Process) {
            $statusId = $this->request->getQuery('STATUS');
            if($statusId) {
                try {
                    $this->data = ProcessStatus::getOneByFilter(array(
                        'ID' => $statusId,
                        'PROCESS_ID' =>  $this->process->getId()
                    ));
                }
                catch (\Bitrix\Main\ObjectNotFoundException $e) {
                    $this->errors[] = Loc::getMessage('SED.ADMIN_STATUS_DETAIL.STATUS_NOT_FOUND');
                }
            }
        }
    }

    protected function saveOrUpdate()
    {
        if (!($this->data instanceof ProcessStatus)) {
            $this->data = ProcessStatus::createEmpty();
        }

        $statusBeforeSave = clone $this->data;

        try {
            $this->data->setName($this->request->getPost('NAME'));
            $this->data->setProcessId($this->process->getId());

            if($this->request->getPost('CODE')) {
                $this->data->setCode($this->request->getPost('CODE'));
            }

            $this->savedDataId = $this->data->save();


            if (!($statusBeforeSave->getId())) {
                $this->elementCreated = true;
            } else {
                $this->elementUpdated = true;
            }
        } catch (\Exception $e) {
            $this->data = $statusBeforeSave;
            $this->errors[] = Loc::getMessage('SED.ADMIN_STATUS_DETAIL.SAVE_ERROR', array('#ERROR_DESC#' => $e->getMessage()));
        }
    }

    protected function checkRequiredFields()
    {
        parent::checkRequiredFields();

        $postList = $this->request->getPostList();
        if(isset($postList['CODE']) && !strlen($postList['CODE'])) {
            $this->notifications[] = \Bitrix\Main\Localization\Loc::getMessage('SED.ADMIN_STATUS_DETAIL.FIELD_IS_REQUIRED', array('#FIELD#' => $this->fields['CODE']['LABEL']));
        }
    }
}