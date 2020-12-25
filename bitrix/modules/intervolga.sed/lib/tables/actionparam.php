<?php namespace Intervolga\Sed\Tables;

use Bitrix\Main\Entity;

class ActionParamTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     * @return string
     */
    public static function getTableName()
    {
        return 'intervolga_sed_action_param';
    }

    /**
     * Returns entity map definition.
     * @return array
     */
    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array('primary' => true, 'autocomplete' => true)),
            new Entity\IntegerField('ACTION_ID', array('required' => true)),
            new Entity\StringField('CODE', array('size' => 255, 'required' => true)),
            new Entity\StringField('NAME', array('size' => 255)),
            new Entity\StringField('TYPE', array('size' => 255, 'required' => true)),
            new Entity\IntegerField('IS_MULTIPLE'),
            new Entity\IntegerField('IS_REQUIRED'),
            new \Bitrix\Main\Entity\ReferenceField(
                'ACTION',
                'Intervolga\Sed\Tables\ActionTable',
                array('=this.ACTION_ID' => 'ref.ID'),
                array('join_type' => 'LEFT')
            ),
        );
    }
}