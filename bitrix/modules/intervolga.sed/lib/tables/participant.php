<?php namespace Intervolga\Sed\Tables;

use Bitrix\Main\Entity;

class ParticipantTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     * @return string
     */
    public static function getTableName()
    {
        return 'intervolga_sed_participant';
    }

    /**
     * Returns entity map definition.
     * @return array
     */
    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array('primary' => true, 'autocomplete' => true)),
            new Entity\IntegerField('CONTRACT_ID', array('required' => true)),
            new Entity\IntegerField('ROLE_ID', array('required' => true)),
            new Entity\IntegerField('USER_ID', array('required' => true)),
            new \Bitrix\Main\Entity\ReferenceField(
                'CONTRACT',
                'Intervolga\Sed\Tables\ContractTable',
                array('=this.CONTRACT_ID' => 'ref.ID'),
                array('join_type' => 'LEFT')
            ),
            new \Bitrix\Main\Entity\ReferenceField(
                'ROLE',
                'Intervolga\Sed\Tables\ParticipantRoleTable',
                array('=this.ROLE_ID' => 'ref.ID'),
                array('join_type' => 'LEFT')
            ),
        );
    }
}