<?php
use Bitrix\Main\Loader;
use Bitrix\Forum\ForumTable;

if (Loader::includeModule('forum')) {
    $dbForumTableDefaultValue = ForumTable::getList(array(
        'select' => array('ID'),
        'filter' => array('XML_ID' => 'intranet_tasks')
    ));
    $forumTableDefaultValue = $dbForumTableDefaultValue->fetch();
    if (!$forumTableDefaultValue) {
        $forumTableDefaultValue = array('ID' => 0);
    }
}

$intervolga_sed_default_option = array(
    'INTERVOLGA_SED_FORUM_ID' => $forumTableDefaultValue['ID'],
    'intervolga_sed_show_task_forums_on_detail_page' => 'Y'
);