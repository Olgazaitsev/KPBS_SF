<?php namespace Intervolga\Sed\Tables;

use Bitrix\Main\Entity;

class TriggerEffectTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     * @return string
     */
    public static function getTableName()
    {
        return 'intervolga_sed_trigger_effect';
    }

    /**
     * Returns entity map definition.
     * @return array
     */
    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array('primary' => true, 'autocomplete' => true)),
            new Entity\IntegerField('TRIGGER_ID', array('required' => true)),
            new Entity\StringField('TRIGGER_TYPE', array('size' => 255, 'required' => true)),
            new Entity\IntegerField('ACTION_ID', array('required' => true)),
            new Entity\IntegerField('PARAM_ID', array('required' => true)),
            new Entity\StringField('PARAM_VALUE', array('size' => 255)),
            new \Bitrix\Main\Entity\ReferenceField(
                'CONTRACT_TRIGGER',
                'Intervolga\Sed\Tables\ContractStatusTriggerTable',
                array('=this.TRIGGER_ID' => 'ref.ID'),
                array('join_type' => 'LEFT')
            ),
            new \Bitrix\Main\Entity\ReferenceField(
                'TASK_TRIGGER',
                'Intervolga\Sed\Tables\TaskStatusTriggerTable',
                array('=this.TRIGGER_ID' => 'ref.ID'),
                array('join_type' => 'LEFT')
            ),
            new \Bitrix\Main\Entity\ReferenceField(
                'TASK_GROUP_TRIGGER',
                'Intervolga\Sed\Tables\TaskGrouopStatusTriggerTable',
                array('=this.TRIGGER_ID' => 'ref.ID'),
                array('join_type' => 'LEFT')
            ),
            new \Bitrix\Main\Entity\ReferenceField(
                'ACTION',
                'Intervolga\Sed\Tables\ActionTable',
                array('=this.ACTION_ID' => 'ref.ID'),
                array('join_type' => 'LEFT')
            ),
            new \Bitrix\Main\Entity\ReferenceField(
                'PARAM',
                'Intervolga\Sed\Tables\ActionParamTable',
                array('=this.PARAM_ID' => 'ref.ID'),
                array('join_type' => 'LEFT')
            ),
        );
    }
}