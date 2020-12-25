<?php namespace Intervolga\Sed\Entities;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

abstract class EnumerableUfElement extends UfElement
{
    const USER_TYPE_ID = 'enumeration';
    const DEFAULT_CAPTION = 'Default caption';

    /**
     * @return mixed
     */
    public static function getDefaultCaption()
    {
        return Loc::getMessage('CONTRACTSTATUSTRIGGER_DEFAULT_CAPTION');
    }

    /**
     * @param array $arSettings
     * @return static;
     */
    public function setSettings($arSettings)
    {
        $arSettings['CAPTION_NO_VALUE'] = trim($arSettings['CAPTION_NO_VALUE']);
        $this->settings['CAPTION_NO_VALUE'] = (empty($arSettings['CAPTION_NO_VALUE'])) ? static::getDefaultCaption() : $arSettings['CAPTION_NO_VALUE'];

        $height = (int)$arSettings["LIST_HEIGHT"];
        if($height < 1) {
            $height = 1;
        }
        $this->settings['LIST_HEIGHT'] = ($height < 1) ? 1 : $height;

        $display = $arSettings["DISPLAY"];
        if($display !== "CHECKBOX" && $display !== "LIST" && $display !== 'UI') {
            $display = "CHECKBOX";
        }
        $this->settings['DISPLAY'] = $display;

        return $this;
    }

    public static function createEmpty()
    {
        return parent::createEmpty()
            ->setUserTypeId(static::USER_TYPE_ID)
            ->setSettings(array());
    }

    protected static function makeArrayFromEntityFilter($entityFilter = null)
    {
        return array('USER_TYPE_ID' => static::USER_TYPE_ID);
    }
}