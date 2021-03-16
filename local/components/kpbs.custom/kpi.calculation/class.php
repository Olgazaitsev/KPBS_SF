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
        $filter = Array
        (
            "ACTIVE" => "Y",
            "!ID" => 61
            //"GROUPS_ID"           => Array(1)
        );


        $rsUser = \CUser::GetList(($by="ID"), ($order="desc"), $filter);
        // заносим прочие показатели
        $users = array();

        while ($arResUser = $rsUser->Fetch()) {
            array_push($users, $arResUser);
        }

        $this->arResult = array(
           "USERS" => $users
        );

        $this->includeComponentTemplate();
    }
}