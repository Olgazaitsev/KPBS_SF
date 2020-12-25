<?php

namespace Intervolga\Sed\Tables;

use Bitrix\Main\Entity;

class ContractUserFieldsTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     * @return string
     */
    public static function getTableName()
    {
        return 'intervolga_sed_contract_user_fields';
    }

    /**
     * Returns entity map definition.
     * @return array
     */
    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true
            )),
            new Entity\IntegerField('PROCESS_ID', array(
                'required' => true
            )),
            new Entity\StringField('FIELD_NAME', array(
                'size' => 20,
                'required' => true
            )),
            new Entity\IntegerField('SORT', array(
                'required' => true
            )),
            new Entity\BooleanField('REQUIRED', array(
                'required' => true,
                'values' => array('N', 'Y'),
                'default_value' => 'N'
            )),
            new Entity\BooleanField('SHOW', array(
                'required' => true,
                'values' => array('N', 'Y'),
                'default_value' => 'Y'
            )),
        );
    }
}