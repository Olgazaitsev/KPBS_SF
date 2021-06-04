<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class kpbs_custom extends \CModule
{
    /**
     * @return string
     */
    public static function getModuleId()
    {
        return basename(dirname(__DIR__));
    }

    public function __construct()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__) . "/version.php");
        $this->MODULE_ID = self::getModuleId();
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = Loc::getMessage("ITRACK_CUSTOM_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("ITRACK_CUSTOM_MODULE_DESC");

        $this->PARTNER_NAME = Loc::getMessage("ITRACK_CUSTOM_PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage("ITRACK_CUSTOM_PARTNER_URI");
    }

    public function installEvents()
    {

    }

    public function uninstallEvents()
    {

    }

    function InstallDB()
    {
        global $APPLICATION, $DB;
        $bInstall = true;



        return $bInstall;
    }

    function UnInstallDB()
    {
        global $APPLICATION, $DB;
        $bInstall = true;



        return $bInstall;
    }

    public function doInstall()
    {
        try {
            $this->InstallDB();
            $this->installEvents();
            Main\ModuleManager::registerModule($this->MODULE_ID);
        } catch (\Exception $e) {
            global $APPLICATION;
            $APPLICATION->ThrowException($e->getMessage());

            return false;
        }

        return true;
    }

    public function doUninstall()
    {
        try {
            $this->uninstallEvents();
            Main\ModuleManager::unRegisterModule($this->MODULE_ID);
            $this->UnInstallDB();
        } catch (\Exception $e) {
            global $APPLICATION;
            $APPLICATION->ThrowException($e->getMessage());

            return false;
        }

        return true;
    }
}
