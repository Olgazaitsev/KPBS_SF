<?php namespace Intervolga\Sed\Admin;

abstract class AbstractSettings
{
    /** @var array $result */
    protected $result;
    /** @var array $params */
    protected $params;
    /** @var \Bitrix\Main\HttpRequest */
    protected $request;
    /** @var array $errors */
    protected $errors;


    abstract protected function prepareParams();


    public function __construct($params = array())
    {
        $this->params = is_array($params) ? $params : array();
        $this->request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        $this->errors = array();
        $this->result = array();
    }

    public function getResult()
    {
        return $this->result;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getParam($key)
    {
        return (strlen($key)) ? $this->params[$key] : null;
    }
}