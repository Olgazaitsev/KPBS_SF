<?php namespace Intervolga\Sed\Interfaces;

interface AbstractUfEntityInterface
{
    /**
     * @return int
     */
    function getId();

    /**
     * @return string
     */
    function getXmlId();

    /**
     * @return string
     */
    function getSort();

    /**
     * @return static
     */
    function save();

    /**
     * @return bool
     */
    function delete();


    /**
     * Создает новый пустой объект
     * Его нужно заполнить значениями и сохранить
     *
     * @return static
     */
    static function createEmpty();

    /**
     * @param int $id
     * @param mixed $entityFilter
     * @param bool $useEntityFilter
     * @return static
     */
    static function getById($id, $entityFilter = null, $useEntityFilter = true);

    /**
     * @param $xmlId
     * @param mixed $entityFilter
     * @param bool $useEntityFilter
     * @return static
     */
    static function getByXmlId($xmlId, $entityFilter = null, $useEntityFilter = true);

    /**
     * @param array $arFilter
     * @param mixed $entityFilter
     * @param array $arOrder
     * @return static[]
     */
    static function getListByFilter($arFilter, $entityFilter = null, $arOrder = array("SORT" => "ASC"), $useEntityFilter = true);

    /**
     * @param $arFilter
     * @param mixed $entityFilter
     * @param array $arOrder
     * @return static
     */
    static function getOneByFilter($arFilter, $entityFilter = null, $arOrder = array("SORT" => "ASC"), $useEntityFilter = true);

    /**
     * @param mixed $entityFilter
     * @param array $arOrder
     * @return static[]
     */
    static function getListAll($entityFilter = null, $arOrder = array("SORT" => "ASC"), $useEntityFilter = true);
}