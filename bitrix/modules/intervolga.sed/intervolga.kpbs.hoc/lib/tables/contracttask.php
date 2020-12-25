<?php namespace Intervolga\Sed\Tables;

use Bitrix\Main\Entity;

class ContractTaskTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     * @return string
     */
    public static function getTableName()
    {
        return 'intervolga_sed_contract_task';
    }

    /**
     * Returns entity map definition.
     * @return array
     */
    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array('primary' => true, 'autocomplete' => true)),
            new Entity\IntegerField('TASK_ID', array('required' => true)),
            new Entity\IntegerField('TASK_TYPE_ID', array('required' => true)),
            new Entity\IntegerField('CONTRACT_ID', array('required' => true)),
            new Entity\IntegerField('GROUP_ID'),
            new Entity\IntegerField('IS_MASTER'),
            new Entity\IntegerField('CREATOR_ROLE_ID', array('required' => true)),
            new Entity\IntegerField('RESP_ROLE_ID', array('required' => true)),
            new Entity\IntegerField('GROUP_INSTANCE_ID'),
            new \Bitrix\Main\Entity\ReferenceField(
                'CONTRACT',
                'Intervolga\Sed\Tables\ContractTable',
                array('=this.CONTRACT_ID' => 'ref.ID'),
                array('join_type' => 'LEFT')
            ),
        );
    }
}