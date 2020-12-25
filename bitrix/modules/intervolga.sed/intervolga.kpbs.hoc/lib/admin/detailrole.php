<?php namespace Intervolga\Sed\Admin;

use Bitrix\Main\Localization\Loc;
use Intervolga\Sed\Entities\ParticipantRole;
use Intervolga\Sed\Entities\Process;

Loc::loadMessages(__FILE__);

class DetailRole extends SettingsDetail
{
    /** @var Process $process */
    protected $process;
    protected $savedDataId;


    protected function getListButtonLabel()
    {
        return Loc::getMessage('SED.ADMIN_ROLE_DETAIL.LIST_BTN');
    }

    protected function getPageHeader()
    {
        return Loc::getMessage('SED.ADMIN_ROLE_DETAIL.PAGE_HEADER');
    }
    
    protected function prepareParams()
    {
        parent::prepareParams();
        $this->initProcess();
    }

    protected function getDetailEntityPageUrl()
    {
        return $this->params['DETAIL_PAGE_URL'] . '?PROCESS=' . $this->process->getId() . '&ROLE=' . $this->savedDataId;
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
                $this->errors[] = Loc::getMessage('SED.ADMIN_ROLE_DETAIL.PROCESS_NOT_FOUND');
            }
        }
        else {
            $this->errors[] = Loc::getMessage('SED.ADMIN_ROLE_DETAIL.EMPTY_PROCESS_PARAM');
        }
    }

    protected function initFields()
    {
        $this->fields = array(
//            'IS_INITIATOR' => array(
//                'LABEL' => Loc::getMessage('SED.ADMIN_ROLE_DETAIL.FIELDS.IS_INITIATOR'),
//            ),
            'NAME' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_ROLE_DETAIL.FIELDS.NAME'),
                'EDITABLE' => 'Y',
                'REQUIRED' => 'Y',
                'TYPE' => 'INPUT'
            ),
            'DEFAULT_USER_ID' => array(
                'LABEL' => Loc::getMessage('SED.ADMIN_ROLE_DETAIL.FIELDS.DEFAULT_USER_ID'),
                'EDITABLE' => 'Y',
                'TYPE' => 'INPUT'
            ),
        );
    }

    protected function getFieldsDisplayData()
    {
        $result = $this->fields;

        if($this->data instanceof ParticipantRole && $this->data->getId()) {

            $idField = array(
                'ID' => array(
                    'LABEL' => 'ID',
                    'VALUE' => $this->data->getId()
                )
            );

            $result['IS_INITIATOR'] = array(
                'LABEL' => Loc::getMessage('SED.ADMIN_ROLE_DETAIL.FIELDS.IS_INITIATOR'),
                'VALUE' => ($this->data->isInitiator()) ? Loc::getMessage('SED.ADMIN_ROLE_DETAIL.YES') : Loc::getMessage('SED.ADMIN_ROLE_DETAIL.NO')
            );

            $result['NAME']['VALUE'] = $this->data->getName();
            $result['DEFAULT_USER_ID']['VALUE'] = $this->data->getDefaultUserId();

            $result = $idField + $result;
        }

        return $result;
    }

    protected function getData()
    {
        if($this->process instanceof Process) {
            $roleId = $this->request->getQuery('ROLE');
            if($roleId) {
                try {
                    $this->data = ParticipantRole::getOneByFilter(array(
                        'ID' => $roleId,
                        'PROCESS_ID' =>  $this->process->getId()
                    ));
                }
                catch (\Bitrix\Main\ObjectNotFoundException $e) {
                    $this->errors[] = Loc::getMessage('SED.ADMIN_ROLE_DETAIL.ROLE_NOT_FOUND');
                }
            }
        }
    }

    protected function saveOrUpdate()
    {
        if (!($this->data instanceof ParticipantRole)) {
            $this->data = ParticipantRole::createEmpty();
        }

        $roleBeforeSave = clone $this->data;

        try {
            $this->savedDataId = $this->data->setName($this->request->getPost('NAME'))
                ->setProcessId($this->process->getId())
                ->setDefaultUserId($this->request->getPost('DEFAULT_USER_ID'))
                ->save();

            if (!($roleBeforeSave->getId())) {
                $this->elementCreated = true;
            } else {
                $this->elementUpdated = true;
            }
        } catch (\Exception $e) {
            $this->data = $roleBeforeSave;
            $this->errors[] = Loc::getMessage('SED.ADMIN_ROLE_DETAIL.SAVE_ERROR', array('#ERROR_DESC#' => $e->getMessage()));
        }
    }
}