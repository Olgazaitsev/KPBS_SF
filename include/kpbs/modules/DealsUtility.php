<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/include/utility.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/include/xlsxwriter.class.php");

class DealsUtility
{
    public static function GetAllDealsResponsibles(){
        $connection = Bitrix\Main\Application::getConnection();
        $sql = "select distinct d.ASSIGNED_BY_ID as RESPONSIBLE_ID, u.NAME, u.LAST_NAME from b_crm_deal d join b_user u on d.ASSIGNED_BY_ID = u.ID ORDER BY u.LAST_NAME";

        $recordset = $connection->query($sql);

        while ($record = $recordset->fetch()) {
            $responsibles[] = array(
                "RESPONSIBLE_ID" => $record['RESPONSIBLE_ID'],
                "RESPONSIBLE_NAME" => $record['NAME'],
                "RESPONSIBLE_LAST_NAME" => $record['LAST_NAME']
            );
        }

        return $responsibles;

    }

    public static function GetDealsFactMarginReport($dateFrom, $dateTo, $responsiblesIds, $minKB, $maxKB){
        $kbFieldName = Utility::GetUserFieldNameByTitle("КВ");

        $sql = "
select d.ASSIGNED_BY_ID, u.LAST_NAME, u.NAME, SUM(d.OPPORTUNITY) AS MARGIN 
    from b_crm_deal d 
        join b_user u on d.ASSIGNED_BY_ID = u.ID
        left join b_uts_crm_deal ufd on d.ID = ufd.VALUE_ID 
    where 
    d.CLOSEDATE >= '".$dateFrom."' AND d.CLOSEDATE <= '".$dateTo."' and d.OPPORTUNITY is not null AND u.Id <> 1 AND d.STAGE_ID = 'WON'";

        // Условие по ответственному
        $responsiblesWhereClause = self::getDealResponsiblesWhereClause($responsiblesIds);
        if($responsiblesWhereClause != "")
            $sql = $sql . "AND u.ID IN " . $responsiblesWhereClause;

        // Условние по КВ
        if(isset($minKB) && isset($maxKB) && $maxKB > 0){
            $sql = $sql." AND ".$kbFieldName." >= ".$minKB." AND ".$kbFieldName." <= ".$maxKB;
        }

        $sql = $sql." GROUP BY d.ASSIGNED_BY_ID ORDER BY SUM(d.OPPORTUNITY) DESC;";


        $connection = Bitrix\Main\Application::getConnection();

        $recordset = $connection->query($sql);

        while ($record = $recordset->fetch()) {
            $responsibles[] = array(
                "RESPONSIBLE_ID" => $record['ASSIGNED_BY_ID'],
                "FI" => $record['LAST_NAME'].' '.substr($record['NAME'], 0, 1).'.',
                "FACT_MARGIN" => $record['MARGIN']
            );
        }

        return $responsibles;
    }

    private static function getDealResponsiblesWhereClause($responsiblesIds){
        if(!empty($responsiblesIds) && count($responsiblesIds) > 0){
            $responsiblesWhereClause = "";
            foreach ($responsiblesIds as $rId)
                $responsiblesWhereClause = $responsiblesWhereClause.$rId.",";
            $responsiblesWhereClause = "(".rtrim($responsiblesWhereClause, ',').")";
            return $responsiblesWhereClause;
        }
        return "";
    }

    private static function getDealStagesWhereClause($stagesIds){
        if(!empty($stagesIds) && count($stagesIds) > 0){
            $stagesIdsWhereClause = "";
            foreach ($stagesIds as $sId)
                $stagesIdsWhereClause = $stagesIdsWhereClause."'".$sId."',";
            $stagesIdsWhereClause = "(".rtrim($stagesIdsWhereClause, ',').")";
            return $stagesIdsWhereClause;
        }
        return "";
    }

    public static function GetDealsMarginReport($dateFrom, $dateTo, $responsiblesIds, $stagesIds, $minKB, $maxKB){
        // NAME IN ('Supply/Project impl', 'Payment responsibility', 'POT Closure'))
        $sql = "
select d.ASSIGNED_BY_ID, u.LAST_NAME, u.NAME, SUM(d.OPPORTUNITY) AS MARGIN 
    from b_crm_deal d 
        join b_user u on d.ASSIGNED_BY_ID = u.ID 
        left join b_uts_crm_deal ufd on d.ID = ufd.VALUE_ID
        join b_crm_status s on d.STAGE_ID = s.STATUS_ID        
    where 
    s.ENTITY_ID = 'DEAL_STAGE' AND   
    d.CLOSEDATE >= '".$dateFrom."' AND d.CLOSEDATE <= '".$dateTo."' and d.OPPORTUNITY is not null AND u.Id <> 1 ";

        // Условие по ответственному
        $responsiblesWhereClause = self::getDealResponsiblesWhereClause($responsiblesIds);
        if($responsiblesWhereClause != "") {
            $sql = $sql . "AND u.ID IN " . $responsiblesWhereClause;
        }

        // Условие по КВ
        if(isset($minKB) && isset($maxKB) && $maxKB > 0){
            $kbFieldName = Utility::GetUserFieldNameByTitle("КВ");
            $sql = $sql." AND ".$kbFieldName." >= ".$minKB." AND ".$kbFieldName." <= ".$maxKB;
        }

        // Условие по стадии сделки
        $stagesIdsWhereClause = self::getDealStagesWhereClause($stagesIds);
        if($stagesIdsWhereClause != "")
            $sql = $sql." AND d.STAGE_ID IN ".$stagesIdsWhereClause;
        else
            $sql = $sql." AND s.NAME IN ('Supply/Project impl', 'Payment responsibility', 'POT Closure')";
        $sql = $sql." GROUP BY d.ASSIGNED_BY_ID ORDER BY SUM(d.OPPORTUNITY) DESC;";

        $connection = Bitrix\Main\Application::getConnection();

        $recordset = $connection->query($sql);

        while ($record = $recordset->fetch()) {
            $responsibles[] = array(
                "RESPONSIBLE_ID" => $record['ASSIGNED_BY_ID'],
                "FI" => $record['LAST_NAME'].' '.substr($record['NAME'], 0, 1).'.',
                "MARGIN" => $record['MARGIN']
            );
        }

        return $responsibles;
    }

    public static function GetDealsMarginReportData($dateFrom, $dateTo, $responsiblesIds, $stagesIds, $minKB, $maxKB){
        $r1 = self::GetDealsMarginReport($dateFrom, $dateTo, $responsiblesIds, $stagesIds, $minKB, $maxKB);
        $r2 = self::GetDealsFactMarginReport($dateFrom, $dateTo, $responsiblesIds, $minKB, $maxKB);

        $result = Array();
        foreach ($r1 as $r){
            $lR = $r;
            foreach ($r2 as $fr){
                if($fr["RESPONSIBLE_ID"] == $lR["RESPONSIBLE_ID"])
                    $lR["FACT_MARGIN"] = $fr["FACT_MARGIN"];
            }

            $result[] = $lR;
        }

        foreach ($r2 as $fr) {
            $lR = $fr;
            $isSet = false;
            foreach ($r1 as $r){
                if($r["RESPONSIBLE_ID"] == $lR["RESPONSIBLE_ID"])
                    $isSet = true;
            }
            if(!$isSet)
            {
                $lR["MARGIN"] = 0;
                $result[] = $lR;
            }

        }

        return $result;
    }

    public static function GetDealStages(){
        $connection = Bitrix\Main\Application::getConnection();
        $sql = "select s.ID, s.STATUS_ID, s.NAME, s.`SYSTEM` as IsSystem from b_crm_status s where s.ENTITY_ID = 'DEAL_STAGE'  and s.STATUS_ID in (select STAGE_ID from b_crm_deal) ORDER BY s.SORT;";

        $recordset = $connection->query($sql);

        while ($record = $recordset->fetch()) {
            $dealStages[] = array(
                "ID" => $record['ID'],
                "STATUS_ID" => $record['STATUS_ID'],
                "NAME" => $record['NAME'],
                "IsSystem" => $record['IsSystem']
            );
        }

        return $dealStages;
    }


    public static function GetClosedDeals($dateFrom, $dateTo, $responsiblesIds, $minKB, $maxKB){
        $kbFieldName = Utility::GetUserFieldNameByTitle("КВ");
        $POTFieldName = Utility::GetUserFieldNameByTitle("POT #");
        $sql =
            "select 
    uf.".$POTFieldName." as POT, uf.".$kbFieldName." AS KB, d.id as ID, d.TITLE, d.CLOSEDATE, d.OPPORTUNITY, d.COMPANY_ID, c.TITLE as COMPANY, d.DATE_CREATE, u.LAST_NAME as RESPONSIBLE_LAST_NAME, u.NAME as RESPONSIBLE_NAME, u.id as RESPONSIBLE_ID, s.NAME as STAGE
    from
            b_crm_deal d join b_user u on d.ASSIGNED_BY_ID = u.ID
                join b_uts_crm_deal uf on uf.VALUE_ID = d.id
                join b_crm_company c on d.COMPANY_ID = c.ID
                join b_crm_status s on s.STATUS_ID = d.STAGE_ID 
    WHERE
    s.ENTITY_ID = 'DEAL_STAGE' AND
d.CLOSEDATE >= '".$dateFrom."' AND d.CLOSEDATE <= '".$dateTo."' and d.OPPORTUNITY is not null AND u.Id <> 1 AND d.STAGE_ID = 'WON' ";

        $responsiblesWhereClause = self::getDealResponsiblesWhereClause($responsiblesIds);
        if($responsiblesWhereClause != "") {
            $sql = $sql . "AND u.ID IN " . $responsiblesWhereClause;
        }

        // Условие по КВ
        if(isset($minKB) && isset($maxKB) && $maxKB > 0){
            $sql = $sql." AND ".$kbFieldName." >= ".$minKB." AND ".$kbFieldName." <= ".$maxKB;
        }

        $sql = $sql." ORDER BY uf.".$POTFieldName;

        $connection = Bitrix\Main\Application::getConnection();

        $recordset = $connection->query($sql);

        while ($record = $recordset->fetch()) {
            $deals[] =[
                'data'    => [ //Данные ячеек
                    "POT" => $record['POT'],
                    "RESPONSIBLE" => $record['RESPONSIBLE_LAST_NAME'].' '.substr($record['RESPONSIBLE_NAME'], 0, 1).'.',
                    "TITLE" => '<a href="'.SITE_SERVER_NAME.'/crm/deal/details/'.$record['ID'].'/" >'.$record['TITLE'].'</a>',
                    "TITLE_STR" => $record['TITLE'],
                    "CLOSEDATE" => $record['CLOSEDATE'],
                    "OPPORTUNITY" => $record['OPPORTUNITY'],
                    "KB" => $record['KB'],
                    "COMPANY" => $record['COMPANY'],
                    "DATE_CREATE" => $record['DATE_CREATE'],
                    "STAGE" => $record['STAGE']
                ],
                'actions' => []
            ];
        }

        return $deals;
    }

    public static function GetDeals($dateFrom, $dateTo, $responsiblesIds, $stagesIds, $minKB, $maxKB){
        $kbFieldName = Utility::GetUserFieldNameByTitle("КВ");
        $POTFieldName = Utility::GetUserFieldNameByTitle("POT #");
        $sql =
"select 
    uf.".$POTFieldName." as POT, uf.".$kbFieldName." AS KB, d.id as ID, d.TITLE, d.CLOSEDATE, d.OPPORTUNITY, d.COMPANY_ID, c.TITLE as COMPANY, d.DATE_CREATE, u.LAST_NAME as RESPONSIBLE_LAST_NAME, u.NAME as RESPONSIBLE_NAME, u.id as RESPONSIBLE_ID, s.NAME as STAGE
    from
            b_crm_deal d join b_user u on d.ASSIGNED_BY_ID = u.ID
                join b_uts_crm_deal uf on uf.VALUE_ID = d.id
                join b_crm_company c on d.COMPANY_ID = c.ID
                join b_crm_status s on s.STATUS_ID = d.STAGE_ID 
    WHERE
    s.ENTITY_ID = 'DEAL_STAGE' AND
d.CLOSEDATE >= '".$dateFrom."' AND d.CLOSEDATE <= '".$dateTo."' and d.OPPORTUNITY is not null AND u.Id <> 1 ";

        $responsiblesWhereClause = self::getDealResponsiblesWhereClause($responsiblesIds);
        if($responsiblesWhereClause != "") {
            $sql = $sql . "AND u.ID IN " . $responsiblesWhereClause;
        }
        $stagesIdsWhereClause = self::getDealStagesWhereClause($stagesIds);
        if($stagesIdsWhereClause != "")
            $sql = $sql." AND d.STAGE_ID IN ".$stagesIdsWhereClause;
        else
            $sql = $sql." AND s.NAME IN ('Supply/Project impl', 'Payment responsibility', 'POT Closure')";

        // Условие по КВ
        if(isset($minKB) && isset($maxKB) && $maxKB > 0){
            $sql = $sql." AND ".$kbFieldName." >= ".$minKB." AND ".$kbFieldName." <= ".$maxKB;
        }

        $sql = $sql." ORDER BY uf.".$POTFieldName;

        $connection = Bitrix\Main\Application::getConnection();

        $recordset = $connection->query($sql);

        while ($record = $recordset->fetch()) {
            $deals[] =[
                'data'    => [ //Данные ячеек
                "POT" => $record['POT'],
                "RESPONSIBLE" => $record['RESPONSIBLE_LAST_NAME'].' '.substr($record['RESPONSIBLE_NAME'], 0, 1).'.',
                "TITLE" => '<a href="'.SITE_SERVER_NAME.'/crm/deal/details/'.$record['ID'].'/" >'.$record['TITLE'].'</a>',
                "TITLE_STR" => $record['TITLE'],
                "CLOSEDATE" => $record['CLOSEDATE'],
                "OPPORTUNITY" => $record['OPPORTUNITY'],
                "KB" => $record['KB'],
                "COMPANY" => $record['COMPANY'],
                "DATE_CREATE" => $record['DATE_CREATE'],
                "STAGE" => $record['STAGE']
                ],
                'actions' => []
                ];
        }

        return $deals;
    }

    public static function exportXlsx2($deals) {

        $header = array(
            'POT №',
            'Сделка',
            'Предполагаемая дата поставки/реализации',
            'КВ',
            'Планируемая маржа',
            'Клиент',
            'Дата создания',
            'Ответственный',
            'Стадия',
        );

        echo '
		<html>
		<head>
		<title>Отчет по сделкам</title>
		<meta http-equiv="Content-Type" content="text/html; charset='.LANG_CHARSET.'">
		<style>
			td {mso-number-format:\@;}
			.number0 {mso-number-format:0;}
			.number2 {mso-number-format:Fixed;}
		</style>
		</head>
		<body>';

        echo "<table border=\"1\">";
        echo "<tr>";

        foreach($header as $h)
        {
            echo '<td>';
            echo $h;
            echo '</td>';
        }
        echo "</tr>";

        foreach ($deals as $item){
            echo "<td>".$item["POT"]."</td>".
                "<td>".$item["RESPONSIBLE"]."</td>".
                "<td>".$item["TITLE"]."</td>".
                "<td>".$item["CLOSEDATE"]."</td>".
                "<td>".$item["OPPORTUNITY"]."</td>".
                "<td>".$item["KB"]."</td>".
                "<td>".$item["COMPANY"]."</td>".
                "<td>".$item["DATE_CREATE"]."</td>".
                "<td>".$item["STAGE"]."</td>";
        }

        echo "</table>";
        echo '</body></html>';

    }

    /**
     * Формирует список товаров в заказе и выгружает данные в xlsx файл
     * Принимает номер заказа
     * @global object $APPLICATION
     * @param array $deals
     */

    public static function exportXlsx($deals) {

        global $APPLICATION;

        $book = 'list1'; //Название книги в файле

        $filename = "deals_report.xlsx"; //Имя сформированного файла



        //Шапка формируемого файла

        $header = array(
            'POT №' => 'string',
            'Сделка' => 'string',
            'Целевая дата закрытия' => 'string',
            'Планируемая маржа' => 'number',
            'КВ' => 'number',
            'Клиент' => 'string',
            'Дата создания' => 'string',
            'Ответственный' => 'string',
            'Стадия' => 'string',
        );

        //Конвертация заголовков в UTF-8 если ваш сайт Bitrix в кодировке Windows-1251
        /*
        if (SITE_CHARSET != 'UTF-8') {
            $header = array(
                ($APPLICATION->ConvertCharset('Код товара', SITE_CHARSET, 'UTF-8')) => 'string',
                ($APPLICATION->ConvertCharset('Наименование', SITE_CHARSET, 'UTF-8')) => 'string',
                ($APPLICATION->ConvertCharset('Цена', SITE_CHARSET, 'UTF-8')) => 'price',
                ($APPLICATION->ConvertCharset('Количество', SITE_CHARSET, 'UTF-8')) => 'integer',
                ($APPLICATION->ConvertCharset('Номер заказа', SITE_CHARSET, 'UTF-8')) => 'integer',
                ($APPLICATION->ConvertCharset('Дата заказа', SITE_CHARSET, 'UTF-8')) => 'string',
            );
        }
        */



        $rows = []; //Массив данных для записи в файл

        foreach ($deals as $item) {//Обход элементов корзины Bitrix D7
            $closeDate = new \Bitrix\Main\Type\DateTime($item["data"]["CLOSEDATE"]);
            $createDate = new \Bitrix\Main\Type\DateTime($item["data"]["DATE_CREATE"]);
            //Масив данных одной строки файла
            $tmp_row = [
                $item["data"]["POT"],
                $item["data"]["TITLE_STR"],
                $closeDate->format('d.m.Y'),
                str_replace('.00', '', $item["data"]["OPPORTUNITY"]),
                $item["data"]["KB"],
                $item["data"]["COMPANY"],
                $createDate->format('d.m.Y'),
                $item["data"]["RESPONSIBLE"],
                $item["data"]["STAGE"],
            ];
            //Конвертация данных в UTF-8 если ваш сайт Bitrix в кодировке Windows-1251

            if (SITE_CHARSET != 'UTF-8') {
                $tmp_row = $APPLICATION->ConvertCharsetArray($tmp_row, SITE_CHARSET, 'UTF-8');
            }

            $rows[] = $tmp_row;
        }

        $writer = new XLSXWriter();
        $writer->setAuthor('bitrix'); //Автор документа

        $writer->writeSheetHeader($book, $header); //Установка шапки для указанной книги в документе
        //Добавление строки из ранее сформированного массива
        foreach ($rows as $row) {
            $writer->writeSheetRow($book, $row);
        }

        /**
         * Формируем заголовки отправляемые в браузер (Что бы пользователю был предложен диалог сохранения файла)
         */

        header('Content-disposition: attachment; filename="' . XLSXWriter::sanitize_filename($filename) . '"');
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');


        //Запись файла в поток вывода
        $writer->writeToStdOut();
        //$writer->writeToFile($filename);
    }
}