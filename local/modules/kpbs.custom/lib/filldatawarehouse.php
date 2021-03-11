<?php


namespace kpbs\custom;


class filldatawarehouse
{
    public static function executefilling()
    {
        \Bitrix\Main\Diag\Debug::writeToFile(date("Y.m.d G:i:s") ."agent", "agent", "__miros.log");

        return 'kpbs\custom\filldatawarehouse::executefilling();';
    }
}