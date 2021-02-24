<?php
//Подключаем autoload
use Bitrix\Main\Diag\Debug;
require_once($_SERVER["DOCUMENT_ROOT"]."/include/utility.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/local/vendor/autoload.php');
//\Bitrix\Main\Diag\Debug::writeToFile("init", "init", "__miros.log");
AddEventHandler('tasks', 'OnBeforeTaskAdd', Array("MyEventsHandler", "my_OnBeforeTaskAdd"));
AddEventHandler('tasks', 'OnTaskAdd', Array("MyEventsHandler", "my_OnTaskAdd"));
AddEventHandler('crm', 'OnBeforeCrmDealUpdate', Array("MyEventsHandler", "my_OnBeforeCrmDealUpdate"));
AddEventHandler('crm', 'OnBeforeCrmCompanyAdd', Array("MyEventsHandler", "my_OnBeforeCrmCompanyUpdate"));
AddEventHandler('crm', 'OnBeforeCrmCompanyUpdate', Array("MyEventsHandler", "my_OnBeforeCrmCompanyUpdate"));
AddEventHandler('socialnetwork', 'OnBeforeSocNetUserToGroupAdd', Array("MyEventsHandler", "my_OnBeforeSocNetUserToGroupAdd"));
class MyEventsHandler
{

    function my_OnBeforeTaskAdd(&$arFields){
        if($arFields["UF_CRM_TASK"][0]) {
//		Bitrix\Main\Diag\Debug::writeToFile($arFields["TITLE"],"TASK TITLE","/checkNda.log");
            if(substr($arFields["TITLE"], 0, 4) == 'CRM:')
                $arFields["TITLE"] = substr($arFields["TITLE"], 4, strlen($arFields["TITLE"]));

//		Bitrix\Main\Diag\Debug::writeToFile($arFields["TITLE"],"TASK TITLE 1","/checkNda.log");
            if (substr($arFields["UF_CRM_TASK"][0], 0, 2) == "D_" &&
                substr($arFields["TITLE"], 0, 3) != 'РОТ') {

                $dealId = substr($arFields["UF_CRM_TASK"][0], 2, strlen($arFields["UF_CRM_TASK"][0]));
                $potNo = Utility::GetUserFieldValueByLabel("РОТ №", "CRM_DEAL", $dealId);
                if($potNo && $potNo != ''){
                    $potNo = substr($potNo, 3, strlen($potNo));
                    if(substr($arFields["TITLE"], 0 , strlen($potNo)+1) != $potNo.":")
                        $arFields["TITLE"] = $potNo . ": " . $arFields["TITLE"];
//		Bitrix\Main\Diag\Debug::writeToFile($arFields["TITLE"],"TASK TITLE 2","/checkNda.log");
                }
            }
            if (substr($arFields["UF_CRM_TASK"][0], 0, 3) == "CO_" &&
                substr($arFields["TITLE"], 0, 3) != 'ACC') {

//		Bitrix\Main\Diag\Debug::writeToFile($arFields["UF_CRM_TASK"][0],"COMPANY ID","/checkNda.log");

                $companyId = substr($arFields["UF_CRM_TASK"][0], 3, strlen($arFields["UF_CRM_TASK"][0]));
                $company = CAllCrmCompany::GetByID($companyId);

//		Bitrix\Main\Diag\Debug::writeToFile($company["TITLE"],"COMPANY TITLE","/checkNda.log");

                if((isset($company["TITLE"]) && $company["TITLE"] != "") && (strpos($arFields["TITLE"], $company["TITLE"]) == false)){
                    $arFields["TITLE"] = $company["TITLE"].": ".$arFields["TITLE"];
//			Bitrix\Main\Diag\Debug::writeToFile($arFields["TITLE"],"TASK TITLE 3","/checkNda.log");
                }

                /*
                $accNo = Utility::GetUserFieldValueByLabel("Номер", "CRM_COMPANY", $companyId);
                if($accNo && $accNo != ''){
                    $accNo = Utility::GetUserFieldValueByLabel("Номер", "CRM_COMPANY", $companyId);
                    if(substr($arFields["TITLE"], 0 , strlen($accNo)+1) != $accNo.":")
                        $arFields["TITLE"] = $accNo . ":" . $arFields["TITLE"];
                }
                */
            }
        }
        Bitrix\Main\Diag\Debug::writeToFile($arFields["TITLE"],"TASK TITLE 4","/checkNda.log");
    }

    function my_OnTaskAdd($taskId){
        //Bitrix\Main\Diag\Debug::writeToFile("tasksadd", "upevent", "__miros.log");
        Utility::sendNewTaskNotification($taskId);
    }

    function my_OnBeforeCrmDealUpdate(&$arFields){
        global $APPLICATION;
        //Bitrix\Main\Diag\Debug::writeToFile("updateevent", "upevent", "__miros.log");
        //return false;
        //\Bitrix\Main\Diag\Debug::writeToFile($arFields, "dept2", "__miros.log");
        $dealId = $arFields["ID"];
        $modifiedById = $arFields["MODIFY_BY_ID"];

        $stagesarchitect = array('FINAL_INVOICE', '1', '2', '4', '3', 'WON', 'LOSE', 'APOLOGY');

        // Проверка наличия компании в сделке
        $companyId = $arFields["COMPANY_ID"];
        $deal = CAllCrmDeal::GetByID($dealId);
        if(!isset($companyId)){
            if(!isset($deal) || !isset($deal["COMPANY_ID"]) || $deal["COMPANY_ID"] == 0){
                $arFields['RESULT_MESSAGE'] = "Не заполнено поле 'Компания'";
                $APPLICATION->ThrowException($arFields['RESULT_MESSAGE']);
                return false;
            }
        }
        CModule::IncludeModule('crm');
        $arFilterDeal = array('ID'=>$dealId);
        // тут меняем код 'UF_CRM_1614162501453' на код проверки архитектора на бое
        $arSelectDeal = array('ID', 'STAGE_ID', 'UF_CRM_1611675525741', 'UF_CRM_1611675557650', 'UF_CRM_1599830407833', 'UF_CRM_1614162501453');

        $obResDeal = CCrmDeal::GetListEx(false,$arFilterDeal,false,false,$arSelectDeal)->Fetch();
        // проверка архитектора
        if(in_array(523, $arFields['UF_CRM_1599830407833']) || in_array(523, $obResDeal['UF_CRM_1599830407833'])) {
            //\Bitrix\Main\Diag\Debug::writeToFile('firstcond', "dept2", "__miros.log");
            if(in_array($arFields['STAGE_ID'], $stagesarchitect) || in_array($obResDeal['STAGE_ID'], $stagesarchitect)) {
                //\Bitrix\Main\Diag\Debug::writeToFile('secondcond', "dept2", "__miros.log");
                //\Bitrix\Main\Diag\Debug::writeToFile($obResDeal['UF_CRM_1614162501453'], "dept2", "__miros.log");
                //\Bitrix\Main\Diag\Debug::writeToFile($arFields['UF_CRM_1614162501453'], "dept2", "__miros.log");
                // тут меняем код ПП и его значения = нет в соответствие с продом
                if(!$obResDeal['UF_CRM_1614162501453'] || $obResDeal['UF_CRM_1614162501453']!=2041) {
                    if(!$arFields['UF_CRM_1614162501453'] || $arFields['UF_CRM_1614162501453']!=2041) {
                        \Bitrix\Main\Diag\Debug::writeToFile('third cond', "dept2", "__miros.log");
                        $arFields['RESULT_MESSAGE'] = "Поле проверка архитектора должно иметь значение да";
                        $APPLICATION->ThrowException($arFields['RESULT_MESSAGE']);
                        return false;
                    }
                }
            }
        }

        if(in_array(523, $arFields['UF_CRM_1599830407833']) || in_array(523, $obResDeal['UF_CRM_1599830407833'])) {
            //\Bitrix\Main\Diag\Debug::writeToFile('firstcond', "dept2", "__miros.log");
            if(in_array($arFields['STAGE_ID'], $stagesarchitect) || in_array($obResDeal['STAGE_ID'], $stagesarchitect)) {
                //\Bitrix\Main\Diag\Debug::writeToFile('secondcond', "dept2", "__miros.log");
                //\Bitrix\Main\Diag\Debug::writeToFile($obResDeal['UF_CRM_1614162501453'], "dept2", "__miros.log");
                //\Bitrix\Main\Diag\Debug::writeToFile($arFields['UF_CRM_1614162501453'], "dept2", "__miros.log");
                // тут меняем код ПП и его значения = нет в соответствие с продом
                if($arFields['UF_CRM_1614162501453']==2042) {
                    //\Bitrix\Main\Diag\Debug::writeToFile('third cond', "dept2", "__miros.log");
                    $arFields['RESULT_MESSAGE'] = "Поле проверка архитектора должно иметь значение да";
                    $APPLICATION->ThrowException($arFields['RESULT_MESSAGE']);
                    return false;
                }

            }
        }

        // заменить код UF_CRM_1612080094 на код поля пнр на бое, UF_CRM_1612080473 на код поля дата ПНР на бое
        // заменить значение 2039 на значение поля да поля ПНР на бое
        // пнр заполнена, дата нет (восклиц знак если нет)
        if($arFields['UF_CRM_1611675525741']==2041 && !$arFields['UF_CRM_1611675557650']) {
            if(!$obResDeal['UF_CRM_1611675557650']) {
                $arFields['RESULT_MESSAGE'] = "Поле дата ПНР должно быть заполнено";
                $APPLICATION->ThrowException($arFields['RESULT_MESSAGE']);
                return false;
            }
            // дата пнр заполнена, пнр нет
        } else if($arFields['UF_CRM_1611675525741'] != 2041 && $arFields['UF_CRM_1611675557650']) {
            if($obResDeal['UF_CRM_1611675525741'] != 2041) {
                $arFields['RESULT_MESSAGE'] = "Поле ПНР должно иметь значение да";
                $APPLICATION->ThrowException($arFields['RESULT_MESSAGE']);
                return false;
            }
        }

        if (array_key_exists('UF_CRM_1611675557650', $arFields) && $arFields['UF_CRM_1611675557650']=="") {
            if($arFields['UF_CRM_1611675525741']==2041 && $obResDeal['UF_CRM_1611675525741']==2041) {
                $arFields['RESULT_MESSAGE'] = "Дата ПНР не может быть пустой";
                $APPLICATION->ThrowException($arFields['RESULT_MESSAGE']);
                return false;
            }
        }

        global $USER_FIELD_MANAGER;
        if($dealId > 0 && $modifiedById > 0) {
            $dealContractSignPlanDateFieldName = Utility::GetUserFieldNameByTitle('Целевая дата подписания договора', 'ru', 'CRM_DEAL');
            $dealContractSignPlanDate = new DateTime($arFields[$dealContractSignPlanDateFieldName]); // ?? $USER_FIELD_MANAGER->GetUserFieldValue('CRM_DEAL', $dealContractSignPlanDateFieldName, $dealId));

//		if(!isset($arFields[$dealContractSignPlanDateFieldName]))
//			$dealContractSignPlanDate = new DateTime($USER_FIELD_MANAGER->GetUserFieldValue('CRM_DEAL', $dealContractSignPlanDateFieldName, $dealId));

            $dealPlanExecuteDateFieldName = Utility::GetUserFieldNameByTitle('Предполагаемая дата поставки/реализации', 'ru', 'CRM_DEAL');
            $dealPlanExecuteDate = new DateTime($arFields[$dealPlanExecuteDateFieldName]); // ?? $USER_FIELD_MANAGER->GetUserFieldValue('CRM_DEAL', $dealPlanExecuteDateFieldName, $dealId));

//		if(!isset($arFields[$dealPlanExecuteDateFieldName]))
//			$dealPlanExecuteDate = new DateTime($USER_FIELD_MANAGER->GetUserFieldValue('CRM_DEAL', $dealPlanExecuteDateFieldName, $dealId));

            $dealPlanCloseDate = new DateTime($arFields["CLOSEDATE"]);// ?? $deal["CLOSEDATE"]);
//		if(!isset($arFields[$dealPlanExecuteDateFieldName]))
//	            $dealPlanCloseDate = new DateTime($deal["CLOSEDATE"]);


            if(isset($dealContractSignPlanDate) && $dealContractSignPlanDate != '' && isset($dealPlanExecuteDate) && $dealPlanExecuteDate != '' && $dealContractSignPlanDate > $dealPlanExecuteDate){
                $arFields['RESULT_MESSAGE'] = "Предполагаемая дата поставки/реализации не может быть раньше целевой даты подписания договора";
                $APPLICATION->ThrowException($arFields['RESULT_MESSAGE']);
                return false;
            }

            if(isset($dealPlanExecuteDate) && $dealPlanExecuteDate != '' && isset($dealPlanCloseDate) && $dealPlanCloseDate != '' && $dealPlanExecuteDate > $dealPlanCloseDate){
                $arFields['RESULT_MESSAGE'] = "Целевая дата закрытия не может быть раньше предполагаемой даты поставки/реализации";
                $APPLICATION->ThrowException($arFields['RESULT_MESSAGE']);
                return false;
            }

            $dealPlanDeliveryDateUFName = Utility::GetUserFieldNameByTitle('Предполагаемая дата поставки/реализации', 'ru', 'CRM_DEAL');
            $dealNewsMessDateUFName = Utility::GetUserFieldNameByTitle('Дата начала работы над новостью', 'ru', 'CRM_DEAL');

            $dealPlanDeliveryDate = $arFields[$dealPlanDeliveryDateUFName];
            $dealNewsMessDate = $arFields[$dealNewsMessDateUFName];
            if (!isset($dealPlanDeliveryDate))
                $dealPlanDeliveryDate = $USER_FIELD_MANAGER->GetUserFieldValue('CRM_DEAL', $dealPlanDeliveryDateUFName, $dealId);
            if (!isset($dealNewsMessDate))
                $dealNewsMessDate = $USER_FIELD_MANAGER->GetUserFieldValue('CRM_DEAL', $dealNewsMessDateUFName, $dealId);

            if (isset($dealPlanDeliveryDate) && $dealPlanDeliveryDate != "") {
                if (!isset($dealNewsMessDate) ||
                    new DateTime($dealNewsMessDate) != (new DateTime($dealPlanDeliveryDate))->modify('-1 month')) {
                    $USER_FIELD_MANAGER->Update('CRM_DEAL', $dealId, array(
                        $dealNewsMessDateUFName => (new DateTime($dealPlanDeliveryDate))->modify('-1 month')->format('d.m.Y')
                    ));
                }
            } elseif (isset($dealNewsMessDate) && $dealNewsMessDate != "") {
                $USER_FIELD_MANAGER->Update('CRM_DEAL', $dealId, array(
                    $dealNewsMessDateUFName => ""
                ));
            }
        }
        if($dealId){
            if($dealId > 0 && $modifiedById > 0)
                Utility::sendUpdateDealNotification($arFields, $dealId, $modifiedById);
            // Bitrix\Main\Diag\Debug::writeToFile($dealId,"dealId","/debug.txt");
            // Bitrix\Main\Diag\Debug::writeToFile($arFields,"arFields","/debug.txt");
        }
    }

    /*
        function my_OnBeforeCrmDealUpdate(&$arFields){
            global $APPLICATION;

            $dealId = $arFields["ID"];
            $modifiedById = $arFields["MODIFY_BY_ID"];

            // Проверка наличия компании в сделке
            $companyId = $arFields["COMPANY_ID"];
            if(!isset($companyId)){
                $deal = CAllCrmDeal::GetByID($dealId);
                if(!isset($deal) || !isset($deal["COMPANY_ID"]) || $deal["COMPANY_ID"] == 0){
                    $arFields['RESULT_MESSAGE'] = "Не заполнено поле 'Компания'";
                    $APPLICATION->ThrowException($arFields['RESULT_MESSAGE']);
                    return false;
                }
            }


            if($dealId){
                if($dealId > 0 && $modifiedById > 0)
                    Utility::sendUpdateDealNotification($arFields, $dealId, $modifiedById);
                    // Bitrix\Main\Diag\Debug::writeToFile($dealId,"dealId","/debug.txt");
                    // Bitrix\Main\Diag\Debug::writeToFile($arFields,"arFields","/debug.txt");
            }


        }
    */
    function my_OnBeforeSocNetUserToGroupAdd($arFields){
        Utility::sendUserAddedToGroupNotify($arFields);
    }

    function my_OnBeforeCrmCompanyUpdate(&$arFields)
    {
        global $APPLICATION;
        $companyId = $arFields["ID"];
        $e = "";

        // "RQ_INN":"123","RQ_KPP":""
        $requisites_req_data = $_REQUEST["REQUISITES"];
        $reqEmpty = true;
        $res = true;

        if(!empty($requisites_req_data)) {
            foreach ($requisites_req_data as $d) {
                if (!(strpos($d["DATA"], '"RQ_INN":""') === false)) {
                    $res = false;
                }
                $reqEmpty = false;
            }
        }

        if($reqEmpty) {
            $r = new \Bitrix\Crm\EntityRequisite();
            $requisitesIds = $r->getEntityRequisiteIDs(4, $companyId);

            if (empty($requisitesIds)) {
                $res = false;
            }

            foreach ($requisitesIds as $requisiteId) {
                $requisites = $r->getById($requisiteId);
                if (!$requisites || !$requisites['RQ_INN']) {
                    $res = false;
                }
            }
        }

        if($res == false)
            $e = "Ошибка: Не заполнено поле ИНН компании в реквизитах..\r\n";

        // Проверка NDA
        $company = CAllCrmCompany::GetByID($companyId);
        $companyCreated = new DateTime($company["DATE_CREATE"]);
        $now = new DateTime('now');
        // Проверка на обязательность только для компаний, заведенных 2 недели назад
        if($now->modify("-14 day") > $companyCreated) {
            global $USER_FIELD_MANAGER;
            $ndaFieldName = Utility::GetUserFieldNameByTitle('Необходимость NDA', 'ru', 'CRM_COMPANY');
            $ndaValue = $arFields[$ndaFieldName];
            if (!$ndaValue) {
                $ndaValue = $USER_FIELD_MANAGER->GetUserFieldValue('CRM_COMPANY', $ndaFieldName, $companyId);
            }

            if(!isset($ndaValue)){
                $e = $e . "Не заполнено поле '".'Необходимость NDA'."'.\r\n";
                $res = false;
            }

            if (Utility::GetUserFieldEnumValue($ndaFieldName, $ndaValue, 'CRM_COMPANY') == 'Да') {
                Utility::CheckRequiredUserField($arFields, 'Есть действующий договор NDA', $companyId, $e, $res);

                $hasNDAFieldName = Utility::GetUserFieldNameByTitle('Есть действующий договор NDA', 'ru', 'CRM_COMPANY');
                $hasNDAFieldValue = $arFields[$hasNDAFieldName];
                if (!isset($hasNDAFieldValue)) {
                    $hasNDAFieldValue = $USER_FIELD_MANAGER->GetUserFieldValue('CRM_COMPANY', $hasNDAFieldName, $companyId);
                }

                if (Utility::GetUserFieldEnumValue($hasNDAFieldName, $hasNDAFieldValue, 'CRM_COMPANY') == 'Да') {
                    Utility::CheckRequiredUserField($arFields, 'Дата подписания', $companyId, $e, $res);
                    Utility::CheckRequiredUserField($arFields, 'Срок действия', $companyId, $e, $res);
                }

                Utility::CheckRequiredUserField($arFields, 'Условия продления договора NDA', $companyId, $e, $res);

//            $ndaSignDateFieldName = Utility::GetUserFieldNameByTitle('Дата подписания', 'ru', 'CRM_COMPANY');
//            $ndaDeadlineFieldName = Utility::GetUserFieldNameByTitle('Срок действия', 'ru', 'CRM_COMPANY');
//            $ndaProlongationTermsFieldName = Utility::GetUserFieldNameByTitle('Условия продления договора NDA', 'ru', 'CRM_COMPANY');
            }
        }

        if(!$res){
            $arFields['RESULT_MESSAGE'] = $e;
            $APPLICATION->ThrowException($arFields['RESULT_MESSAGE']);
        }

        return $res;
//        Bitrix\Main\Diag\Debug::writeToFile($company,"company","/debug.txt");
    }


}
