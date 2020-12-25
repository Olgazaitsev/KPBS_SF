<?php namespace Intervolga\Sed\Tools;

class Html
{
    const TABLE_ITEM_TYPE_ROLE_TASK_STATUS = 'role-task-status';
    const TABLE_ITEM_TYPE_PROCESS_STATUS = 'process-status';


    public static function getPageTitleButton($text, $url = null, $type = 'accept', $actionClass = null)
    {
        $href = empty($url) ? '' : 'href="' . $url . '"';
        $typeClass = empty($type) ? '' : 'webform-small-button-' . $type;
        $content = '
        <div class="task-list-toolbar task-list-toolbar-float">
            <div class="task-list-toolbar-actions">
                <a class="webform-small-button ' . $typeClass . ' task-list-toolbar-create ' . (string)$actionClass . '" ' . $href . '>
                    <span class="webform-small-button-left"></span>
                    <span class="webform-small-button-text">' . $text .'</span>
                    <span class="webform-small-button-right"></span>
                </a>
            </div>
        </div>';
        return $content;
    }

    public static function getErrorHtml($error)
    {
        return (empty($error)) ? '' : '<div class="msg msg-error">' . $error . '</div>';
    }

    public static function getWarningHtml($message)
    {
        return (empty($message)) ? '' : '<div class="msg msg-warning">' . $message . '</div>';
    }

    public static function getContractListTableItem($text, $type, $option = 0)
    {
        return '<span class="change-parent-node" data-type="' . $type . '" data-option="' . $option . '">' . $text . '</span>';
    }

    public static function getUserRolePopup($userId, $name = 'DEFAULT_SELECTOR', $onChange = null)
    {
        ob_start();
        global $APPLICATION;
        $APPLICATION->IncludeComponent(
            "bitrix:intranet.user.selector.new",
            "",
            array(
                "MULTIPLE" => "N",
                "NAME" => $name,
                "POPUP" => "N",
                "ON_CHANGE" => $onChange,
                "SITE_ID" => SITE_ID,
                'SHOW_EXTRANET_USERS' => 'FROM_MY_GROUPS',
                'DISPLAY_TAB_GROUP' => 'Y',
                'SHOW_LOGIN' => 'Y',
                'VALUE' => $userId
            ),
            null,
            array("HIDE_ICONS" => "Y")
        );
        return ob_get_clean();
    }
}