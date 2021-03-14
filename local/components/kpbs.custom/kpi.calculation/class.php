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
        $rsUser = \CUser::GetList(($by="ID"), ($order="desc"), array("SELECT"=>array("ID", "NAME", "LAST_NAME")));
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