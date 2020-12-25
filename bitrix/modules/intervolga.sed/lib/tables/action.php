<?php namespace Intervolga\Sed\Tables;

use Bitrix\Main\Entity;

class ActionTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     * @return string
     */
    public static function getTableName()
    {
        return 'intervolga_sed_action';
    }

    /**
     * Returns entity map definition.
     * @return array
     */
    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array('primary' => true, 'autocomplete' => true)),
            new Entity\StringField('NAME', array('size' => 255)),
            new Entity\StringField('CODE', array('size' => 255, 'required' => true)),

        );
    }
}