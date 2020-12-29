<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/include/utility.php");


class CompaniesUtility
{

    public static function GetCompaniesWithoutNDA()
    {
        $ndaFieldName = Utility::GetUserFieldNameByTitle('Необходимость NDA', 'ru', 'CRM_COMPANY');
        $hasNDAFieldName = Utility::GetUserFieldNameByTitle('Есть действующий договор NDA', 'ru', 'CRM_COMPANY');
        $dateNDASignFieldName = Utility::GetUserFieldNameByTitle('Дата подписания', 'ru', 'CRM_COMPANY');
        $NDATermFieldName = Utility::GetUserFieldNameByTitle('Срок действия', 'ru', 'CRM_COMPANY');
        $NDAProlongationConditionFieldName = Utility::GetUserFieldNameByTitle('Условия продления договора NDA', 'ru', 'CRM_COMPANY');

        $companiesIds = Array();

        $checkDate = (new DateTime('now'))->modify("-14 day");

        $connection = Bitrix\Main\Application::getConnection();
        $sql = "
select c.ID AS COMPANY_ID, c.TITLE, c.DATE_CREATE, buc.".$ndaFieldName.", c.ASSIGNED_BY_ID AS RESPONSIBLE_ID from
    b_crm_company c
        left join b_uts_crm_company buc on c.ID = buc.VALUE_ID
where
    buc.".$ndaFieldName." is null
AND c.DATE_CREATE < '".$checkDate->format("Ymd")."'
AND DATE_CREATE > '20200828'
;        
        ";

        $recordset = $connection->query($sql);

        while ($record = $recordset->fetch()) {
            $companiesIds[] = array(
                "COMPANY_ID" => $record['COMPANY_ID'],
                "RESPONSIBLE_ID" => $record['RESPONSIBLE_ID'],
                "TITLE" => $record['TITLE']);
        }

        return $companiesIds;
    }

    public static function GetCompaniesWithNDAMonthToEnd()
    {
        $ndaFieldName = Utility::GetUserFieldNameByTitle('Необходимость NDA', 'ru', 'CRM_COMPANY');
        $ndaTermFieldName = Utility::GetUserFieldNameByTitle('Срок действия', 'ru', 'CRM_COMPANY');

        $companiesIds = Array();
        $checkDate = (new DateTime('now'))->modify("+1 month");

        $connection = Bitrix\Main\Application::getConnection();
        $sql = "
select c.ID AS COMPANY_ID, c.TITLE, c.DATE_CREATE, buc.".$ndaTermFieldName." AS NDA_TERM, buc.".$ndaFieldName.", bufe.VALUE, c.ASSIGNED_BY_ID AS RESPONSIBLE_ID from
    b_crm_company c
        left join b_uts_crm_company buc on c.ID = buc.VALUE_ID
        left join b_user_field_enum bufe on bufe.ID = buc.".$ndaFieldName."
 where
-- c.id = 3016
         buc.".$ndaTermFieldName." < '".$checkDate->format("Ymd")."'
AND bufe.VALUE = 'Да'
;        
        ";
	// Bitrix\Main\Diag\Debug::writeToFile($sql,"Поиск со NDA сроком меньше месяца","/checkNda.log");
        $recordset = $connection->query($sql);

        while ($record = $recordset->fetch()) {
            $companiesIds[] = array(
                "COMPANY_ID" => $record['COMPANY_ID'],
                "RESPONSIBLE_ID" => $record['RESPONSIBLE_ID'],
                "TITLE" => $record['TITLE'],
                "NDA_TERM" => $record['NDA_TERM']
                );
        }

        return $companiesIds;
    }
}
