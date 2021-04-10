<?php
defined('B_PROLOG_INCLUDED') || die;

class KpbsKpicalculationComponent extends CBitrixComponent
{
    public function __construct(CBitrixComponent $component = null)
    {
        //global $USER;
        parent::__construct($component);
    }

    public function executeComponent()
    {

        $userslist = explode(',', COption::GetOptionString('kpbs.custom', 'users_list'));

        \Bitrix\Main\Diag\Debug::writeToFile($userslist, "ulist", "__miros.log");

        $filter = Array
        (
            "ACTIVE" => "Y",
            //"ID" => $userslist
            //"GROUPS_ID"           => Array(1)
        );


        $rsUser = \CUser::GetList(($by="ID"), ($order="desc"), $filter);
        // заносим прочие показатели
        $users = array();

        while ($arResUser = $rsUser->Fetch()) {
            if(in_array($arResUser['ID'], $userslist)) {
                array_push($users, $arResUser);
            }
        }

        $this->arResult = array(
           "USERS" => $users
        );

        $this->includeComponentTemplate();
    }
}