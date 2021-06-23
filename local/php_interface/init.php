<?php
//Подключаем autoload
use Bitrix\Main\Diag\Debug;
use Bitrix\Disk\Internals\AttachedObjectTable;
require_once($_SERVER["DOCUMENT_ROOT"]."/include/utility.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/local/vendor/autoload.php');
//\Bitrix\Main\Diag\Debug::writeToFile("init", "init", "__miros.log");
AddEventHandler('tasks', 'OnBeforeTaskAdd', Array("MyEventsHandler", "my_OnBeforeTaskAdd"));
AddEventHandler('tasks', 'OnTaskAdd', Array("MyEventsHandler", "my_OnTaskAdd"));
AddEventHandler('tasks', 'OnTaskUpdate', Array("MyEventsHandler", "my_OnTaskUpdate"));
AddEventHandler('crm', 'OnBeforeCrmDealUpdate', Array("MyEventsHandler", "my_OnBeforeCrmDealUpdate"));
AddEventHandler('crm', 'OnBeforeCrmCompanyAdd', Array("MyEventsHandler", "my_OnBeforeCrmCompanyUpdate"));
AddEventHandler('crm', 'OnBeforeCrmCompanyUpdate', Array("MyEventsHandler", "my_OnBeforeCrmCompanyUpdate"));
AddEventHandler('socialnetwork', 'OnBeforeSocNetUserToGroupAdd', Array("MyEventsHandler", "my_OnBeforeSocNetUserToGroupAdd"));
//AddEventHandler('disk', 'onAfterAddFile', Array("MyEventsHandler", "my_onAfterAddFile"));

class MyEventsHandler
{
    /*function my_onAfterAddFile(&$file) {
        \Bitrix\Main\Diag\Debug::writeToFile('event', "filename1", "__miros.log");
        if($file instanceof \Bitrix\Disk\File)
        {
            \Bitrix\Main\Diag\Debug::writeToFile($file->getName(), "filename2", "__miros.log");
            \Bitrix\Main\Diag\Debug::writeToFile($file->getId(), "filename2", "__miros.log");
            //\Bitrix\Main\Diag\Debug::writeToFile($file->getEntityTyoe(), "filename3", "__miros.log");
            //\Bitrix\Main\Diag\Debug::writeToFile($file->isEditable(), "filename4", "__miros.log");
        }
    }*/

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
        Bitrix\Main\Diag\Debug::writeToFile($taskId, "addvent", "__miros.log");
        Utility::sendNewTaskNotification($taskId);
        /*AttachedObjectTable::updateBatch(
            array(
                'ALLOW_EDIT' => 1,
            ),
            array(
                'MODULE_ID' => 'tasks',
                'ENTITY_ID' => $taskId,
                'ALLOW_EDIT' => 0
            )
        );*/
    }

    function my_OnTaskUpdate($taskId){
        Bitrix\Main\Diag\Debug::writeToFile($taskId, "upevent", "__miros.log");
        Utility::sendUpdateTaskNotification($taskId);
        $attachedList = \Bitrix\Disk\AttachedObject::getList(array(
            'filter' => array(
                '=MODULE_ID' => 'tasks',
                '=ENTITY_ID' => $taskId
                //'=ALLOW_EDIT' => 0
            ),
            'select' => array('ID', 'ALLOW_EDIT'),
            //'limit' => 1,
        ))->fetch();
        if($attachedList['ALLOW_EDIT']==1) {
            AttachedObjectTable::updateBatch(
                array(
                    'ALLOW_EDIT' => 1,
                ),
                array(
                    'MODULE_ID' => 'tasks',
                    'ENTITY_ID' => $taskId,
                    'ALLOW_EDIT' => 0
                )
            );
        }
        /**/
    }

    function my_OnBeforeCrmDealUpdate(&$arFields){
        global $APPLICATION;
        //Bitrix\Main\Diag\Debug::writeToFile("updateevent", "upevent", "__miros.log");
        //return false;
        //\Bitrix\Main\Diag\Debug::writeToFile($arFields, "dept2", "__miros.log");
        $dealId = $arFields["ID"];
        $modifiedById = $arFields["MODIFY_BY_ID"];

        $stagesarchitect = array('FINAL_INVOICE', '1', '2', '4', '3', 'WON');
        $stagespnr = array('1', '2', '4', '3', 'WON');

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
        $arSelectDeal = array('ID', 'STAGE_ID', 'UF_CRM_1611675525741', 'UF_CRM_1611675557650', 'UF_CRM_1599830407833', 'UF_CRM_1614278967');

        $obResDeal = CCrmDeal::GetListEx(false,$arFilterDeal,false,false,$arSelectDeal)->Fetch();
        // проверка архитектора
        if ($arFields['UF_CRM_1614278967']=='2089' && !$arFields['UF_CRM_1614862006']) {
            CModule::IncludeModule('im');
            $arFieldschat = array(
                "MESSAGE_TYPE" => "S", # P - private chat, G - group chat, S - notification
                "TO_USER_ID" => $arFields['MODIFY_BY_ID'],
                "FROM_USER_ID" => 1,
                "MESSAGE" => "СДЕЛКА НЕ СОХРАНЕНА! При выборе в поле Проверка архитектора значения нет, нужно указать причину отказа",
                "AUTHOR_ID" => 1
                //"EMAIL_TEMPLATE" => "some",
                //"NOTIFY_TYPE" => 2,  # 1 - confirm, 2 - notify single from, 4 - notify single
                //"NOTIFY_MODULE" => "main", # module id sender (ex: xmpp, main, etc)
                //"NOTIFY_EVENT" => "IM_GROUP_INVITE", # module event id for search (ex, IM_GROUP_INVITE)
                //"NOTIFY_TITLE" => "title to send email", # notify title to send email
            );
            CIMMessenger::Add($arFieldschat);
            $arFields['RESULT_MESSAGE'] = "При выборе в поле Проверка архитектора значения нет, нужно указать причину отказа";
            $APPLICATION->ThrowException($arFields['RESULT_MESSAGE']);
            return false;
        }

        if(in_array(523, $arFields['UF_CRM_1599830407833']) || in_array(523, $obResDeal['UF_CRM_1599830407833']) ||
            in_array(525, $arFields['UF_CRM_1599830407833']) || in_array(525, $obResDeal['UF_CRM_1599830407833'])) {
            //\Bitrix\Main\Diag\Debug::writeToFile('firstcond', "dept2", "__miros.log");
            $exactstage = false;
            $exactstage2 = false;
            if($arFields['STAGE_ID'] && in_array($arFields['STAGE_ID'], $stagesarchitect)) {
                $exactstage = true;
            } elseif(!$arFields['STAGE_ID'] && in_array($obResDeal['STAGE_ID'], $stagesarchitect)) {
                $exactstage = true;
            }
            if($exactstage) {
                //\Bitrix\Main\Diag\Debug::writeToFile('secondcond', "dept2", "__miros.log");
                //\Bitrix\Main\Diag\Debug::writeToFile($obResDeal['UF_CRM_1614278967'], "dept2", "__miros.log");
                //\Bitrix\Main\Diag\Debug::writeToFile($arFields['UF_CRM_1614278967'], "dept2", "__miros.log");
                // тут меняем код ПП и его значения = нет в соответствие с продом
                $errorfield = [];
                if (!$arFields['UF_CRM_1614278967']) {
                    if(!$obResDeal['UF_CRM_1614278967']) {
                        $errorfield[] = "&quot;Проверка архитектора&quot;";
                    }
                }
                if($arFields['STAGE_ID'] && in_array($arFields['STAGE_ID'], $stagespnr)) {
                    $exactstage2 = true;
                } elseif(!$arFields['STAGE_ID'] && in_array($obResDeal['STAGE_ID'], $stagespnr)) {
                    $exactstage2 = true;
                }
                if ($exactstage2 && !$arFields['UF_CRM_1611675525741']) {
                    if(!$obResDeal['UF_CRM_1611675525741']) {
                        $errorfield[] = "&quot;ПНР&quot;";
                    }
                }
                if($errorfield) {
                    if(count($errorfield)==1) {
                        $errormsg = "Чтобы сделка сохранила изменения поле ".current($errorfield)." должно быть заполнено!";
                    } else {
                        $errormsg = "Чтобы сделка сохранила изменения поля ".implode(",", $errorfield)." должны быть заполнены!";
                    }
                    $arFields['RESULT_MESSAGE'] = $errormsg;
                    CModule::IncludeModule('im');
                    $arFieldschat = array(
                        "MESSAGE_TYPE" => "S", # P - private chat, G - group chat, S - notification
                        "TO_USER_ID" => $arFields['MODIFY_BY_ID'],
                        "FROM_USER_ID" => 1,
                        "MESSAGE" => $arFields['RESULT_MESSAGE'],
                        "AUTHOR_ID" => 1
                        //"EMAIL_TEMPLATE" => "some",
                        //"NOTIFY_TYPE" => 2,  # 1 - confirm, 2 - notify single from, 4 - notify single
                        //"NOTIFY_MODULE" => "main", # module id sender (ex: xmpp, main, etc)
                        //"NOTIFY_EVENT" => "IM_GROUP_INVITE", # module event id for search (ex, IM_GROUP_INVITE)
                        //"NOTIFY_TITLE" => "title to send email", # notify title to send email
                    );
                    CIMMessenger::Add($arFieldschat);
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

            $dealPlanCloseDate = new DateTime($arFields["UF_CRM_1617457299824"]);// ?? $deal["UF_CRM_1617457299824"]);
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

            /* $dealPlanDeliveryDateUFName = Utility::GetUserFieldNameByTitle('Предполагаемая дата поставки/реализации', 'ru', 'CRM_DEAL');
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
            } */
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