<?php namespace Intervolga\Sed\Tables;

use Bitrix\Main\Entity;

class TaskStatusTransitionTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     * @return string
     */
    public static function getTableName()
    {
        return 'intervolga_sed_task_status_transition';
    }

    /**
     * Returns entity map definition.
     * @return array
     */
    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array('primary' => true, 'autocomplete' => true)),
            new Entity\IntegerField('TASK_TYPE', array('required' => true)),
            new Entity\IntegerField('SOURCE_STATUS', array('required' => true)),
            new Entity\IntegerField('DEST_STATUS', array('required' => true)),
            new Entity\StringField('USER_ROLE', array('size' => 255)),
            new Entity\IntegerField('TRANSITION_ALLOWED'),
            new Entity\IntegerField('NEED_COMMENT'),
            new Entity\StringField('BTN_LABEL', array('size' => 255)),
            new Entity\StringField('BTN_COLOR', array('size' => 6)),
            new Entity\StringField('BTN_TEXT_COLOR', array('size' => 6)),
            new Entity\IntegerField('BTN_HOVER_MODE'),
            new Entity\IntegerField('BTN_SORT'),
        );
    }
}