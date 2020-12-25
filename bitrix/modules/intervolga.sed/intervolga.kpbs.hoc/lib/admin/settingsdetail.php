<?php namespace Intervolga\Sed\Admin;

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

abstract class SettingsDetail extends AbstractSettings
{
//    const SAVE_ACTION_CODE = 'save';

    /** @var \Intervolga\Sed\Entities\TableElement $data */
    protected $data;
    /** @var array $errors */
    protected $notifications;
    /** @var bool $elementCreated */
    protected $elementCreated;
    /** @var bool $elementCreated */
    protected $elementUpdated;
    /** @var array $fields */
    protected $fields;


    /**
     * @return string
     */
    abstract protected function getListButtonLabel();

    /**
     * @return string
     */
    abstract protected function getPageHeader();

    /**
     * @return array
     */
    abstract protected function getFieldsDisplayData();

    abstract protected function getDetailEntityPageUrl();

    abstract protected function saveOrUpdate();

    abstract protected function initFields();

    abstract protected function getData();


    public function __construct($params = array())
    {
        parent::__construct($params);

        $this->prepareParams();
        $this->initFields();
        $this->getData();
        $this->checkRequest();
        $this->fillResult();
    }

    protected function prepareParams()
    {
        $this->notifications = array();

        if(!strlen($this->params['DETAIL_PAGE_URL'])) {
            $this->params['DETAIL_PAGE_URL'] = $this->request->getRequestedPage();
        }
    }

    protected function checkRequiredFields()
    {
        foreach ($this->fields as $fieldCode => $field) {
            if(($field['REQUIRED'] == 'Y') && !strlen($this->request->getPost($fieldCode))) {
                $this->notifications[] = Loc::getMessage('SED.ADMIN_DETAIL.FIELD_IS_REQUIRED', array('#FIELD#' => $field['LABEL']));
            }
        }
    }

    protected function checkRequest()
    {
//        if($this->request->isPost() && check_bitrix_sessid() && $this->request->getPost(static::SAVE_ACTION_CODE)) {
        if($this->request->isPost() && check_bitrix_sessid()) {

            $this->checkRequiredFields();

            if(!count($this->errors) && !count($this->notifications)) {
                $this->saveOrUpdate();
            }
        }
    }

    protected function getListButtonsInfo()
    {
        return array(
            array(
                'LABEL' => $this->getListButtonLabel(),
                'URL' => $this->params['LIST_PAGE_URL']
            )
        );
    }

    protected function fillResult()
    {
        $this->result['LIST_BUTTONS'] = $this->getListButtonsInfo();

        $this->result['MESSAGES'] = array();
        if($this->elementCreated) {
            $this->result['MESSAGES'] = array(
                'TYPE' => 'SUCCESS',
                'TITLE' => Loc::getMessage('SED.ADMIN_DETAIL.MESSAGES.CREATE.TITLE'),
                'BODY' => Loc::getMessage('SED.ADMIN_DETAIL.MESSAGES.CREATE.BODY', array('#URL#' => $this->getDetailEntityPageUrl()))
            );
        }
        elseif(count($this->errors)) {
            $this->result['MESSAGES'] = array(
                'TITLE' => Loc::getMessage('SED.ADMIN_DETAIL.MESSAGES.ERRORS.TITLE'),
                'BODY' => implode('<br>', $this->errors)
            );
        }
        else {
            $this->result['TITLE'] = $this->getPageHeader();

            $this->result['SAVE_BTN'] = array(
//                'NAME' => static::SAVE_ACTION_CODE,
                'LABEL' => Loc::getMessage('SED.ADMIN_DETAIL.SAVE_BTN_LABEL')
            );

            $this->result['CANCEL_BTN']  = array(
                'URL' => $this->params['LIST_PAGE_URL'],
                'LABEL' => Loc::getMessage('SED.ADMIN_DETAIL.CANCEL_BTN_LABEL')
            );

            if(count($this->notifications)) {
                $this->result['MESSAGES'] = array(
                    'TITLE' => Loc::getMessage('SED.ADMIN_DETAIL.MESSAGES.ERRORS.TITLE'),
                    'BODY' => implode('<br>', $this->notifications)
                );
            }
            elseif($this->elementUpdated) {
                $this->result['MESSAGES'] = array(
                    'TYPE' => 'SUCCESS',
                    'TITLE' => Loc::getMessage('SED.ADMIN_DETAIL.MESSAGES.UPDATE.TITLE'),
                );
            }

            $this->result['FIELDS'] = $this->getFieldsDisplayData();
        }
    }
}