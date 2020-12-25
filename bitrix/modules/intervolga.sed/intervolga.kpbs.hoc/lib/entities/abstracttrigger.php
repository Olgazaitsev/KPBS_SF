<?php namespace Intervolga\Sed\Entities;

abstract class AbstractTrigger extends TableElement
{
    /**
     * @return string
     * @throws \Bitrix\Main\NotImplementedException
     */
    public static function getType()
    {
        throw new \Bitrix\Main\NotImplementedException();
    }

    /**
     * @param $value
     * @return $this
     */
    public function setProcessId($value)
    {
        return $this->setFieldValue('PROCESS_ID', $value);
    }

    public function getProcessId()
    {
        return $this->getFieldValue('PROCESS_ID');
    }
}