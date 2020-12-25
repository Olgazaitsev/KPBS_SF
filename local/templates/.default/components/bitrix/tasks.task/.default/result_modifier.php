<?php
defined('B_PROLOG_INCLUDED') || die;

/** @var CBitrixComponentTemplate $this */

use Bitrix\Main\Loader;
use Intervolga\Sed\ComponentTemplateLevel\ComponentTemplate;


if (!Loader::includeModule('intervolga.sed')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/intervolga.sed/lib/componenttemplatelevel/componenttemplate.php';
}

ComponentTemplate::extendMutator($this->getComponent());
