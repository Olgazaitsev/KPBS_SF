<?php
global $USER;
$cid = $USER->GetID();
$admin = $USER->IsAdmin();

if($cid != $arResult['ENTITY_DATA']['ASSIGNED_BY_ID'] && !$admin) {
    unset($arResult['TABS'][8]);
}
