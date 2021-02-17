<?php

namespace kpbs\custom;

require_once($_SERVER["DOCUMENT_ROOT"]."/include/kpbs_fields_settings.php");

use Bitrix\Main\Mail\Event;
use Bitrix\Main\Localization\Loc;

class Utility
{

    public static function TestFunction(){
        echo 'test';
    }

    public function GetUserFieldValueByLabel($fieldLabel, $entityType, $entityId)
    {
        $connection = Bitrix\Main\Application::getConnection();
        $sqlHelper = $connection->getSqlHelper();
        $sql = "SELECT DISTINCT f.FIELD_NAME FROM b_user_field_lang l JOIN b_user_field f on f.ID = l.USER_FIELD_ID WHERE l.LIST_COLUMN_LABEL = '" . $fieldLabel . "' and f.ENTITY_ID = '" . $entityType . "'";
        $recordset = $connection->query($sql);

        if ($record = $recordset->fetch()) {
            global $USER_FIELD_MANAGER;
            $value = $USER_FIELD_MANAGER->GetUserFieldValue($entityType, $record['FIELD_NAME'], $entityId);
            return $value;
        }

        return null;
    }

    public static function GetUserFieldTitle($ufName, $lang = "ru", $entityTypeId = "CRM_DEAL"){
        $connection = Bitrix\Main\Application::getConnection();
        $sqlHelper = $connection->getSqlHelper();
        $sql = "SELECT DISTINCT l.EDIT_FORM_LABEL FROM b_user_field_lang l JOIN b_user_field f on f.ID = l.USER_FIELD_ID WHERE f.ENTITY_ID = '" . $entityTypeId . "' and  f.FIELD_NAME = '" . $ufName . "' AND l.LANGUAGE_ID = '" . $lang . "'" ;
        $recordset = $connection->query($sql);

        if ($record = $recordset->fetch()) {
            return $record["EDIT_FORM_LABEL"];
        }

        return null;
    }

    public static function GetUserFieldNameByTitle($ufTitle, $lang = "ru", $entityTypeId = "CRM_DEAL"){
        $connection = Bitrix\Main\Application::getConnection();
        $sqlHelper = $connection->getSqlHelper();
        $sql = "select distinct f.field_name from sitemanager.b_user_field_lang l JOIN sitemanager.b_user_field f on f.ID = l.USER_FIELD_ID where ENTITY_ID = '" . $entityTypeId . "' and  lower(l.edit_form_label) = lower('" . $ufTitle . "') AND l.LANGUAGE_ID = '" . $lang . "'" ;
        $recordset = $connection->query($sql);

        if ($record = $recordset->fetch()) {
            return $record["field_name"];
        }

        return null;
    }


    public static function GetUserFieldEnumValue($ufName, $enumId, $entityTypeId){
        if (!($enumId))
            return;

        if (!($enumId > 0))
            return;

	if (is_array($enumId))
	    return;

        $connection = Bitrix\Main\Application::getConnection();
        $sql = "SELECT DISTINCT bufe.VALUE FROM b_user_field_enum bufe JOIN b_user_field f on f.ID = bufe.USER_FIELD_ID WHERE f.ENTITY_ID = '" . $entityTypeId . "' and  f.FIELD_NAME = '" . $ufName . "' AND bufe.ID = ".$enumId;
        $recordset = $connection->query($sql);

        if ($record = $recordset->fetch()) {
            return $record["VALUE"];
        }

        return null;

    }
	
	public function GetDealsCount(){
		$connection = Bitrix\Main\Application::getConnection();
        // $sqlHelper = $connection->getSqlHelper();
        $sql = "SELECT COUNT(*) AS COUNT FROM b_crm_deal;";
        $recordset = $connection->query($sql);

        if ($record = $recordset->fetch()) {
            $value = $record['COUNT'];
            return $value;
        }

        return -1;
	}

	public function GetCompaniesCount(){
		$connection = Bitrix\Main\Application::getConnection();
        // $sqlHelper = $connection->getSqlHelper();
        $sql = "SELECT COUNT(*) AS COUNT FROM b_crm_company;";
        $recordset = $connection->query($sql);

        if ($record = $recordset->fetch()) {
            $value = $record['COUNT'];
            return $value;
        }

        return -1;
	}

	public static function GetUserAssignedByDealsIds($userId){
        $dealsIds = Array();

        $connection = Bitrix\Main\Application::getConnection();
        $sql = "SELECT d.ID AS DEAL_ID FROM b_crm_deal d where d.ASSIGNED_BY_ID = ".$userId.";";

        $recordset = $connection->query($sql);

        while ($record = $recordset->fetch()) {
            $dealsIds[] = $record['DEAL_ID'];
        }

        return $dealsIds;
    }

	public static function sendNewTaskNotification($taskId){
        /*
		 * #EMAIL_FROM#
		 * #EMAIL_TO#
		 * #TASK_TITLE#
		 */

        $task = new \Bitrix\Tasks\Item\Task($taskId);
        $task->getData();

        // Bitrix\Main\Diag\Debug::writeToFile($task->export(),"task","/debug.txt");

        // Получение информации о пользователях
        $responsibleUser = null;
        $responsibleEmail = "";
        $taskCreatorUser = null;
        foreach ($task['SE_MEMBER'] as $taskUser){
            $user = CUser::GetByID($taskUser["USER_ID"]);
            if($taskUser["TYPE"] == 'O')
                $taskCreatorUser = $user->Fetch();
            if($taskUser["TYPE"] == 'R')
                $responsibleUser = $user->Fetch();
        }

        // Получение информации о проекте
        $projectName = '-';
        if($task["GROUP_ID"] && $task["GROUP_ID"] > 0){
            $project = CSocNetGroup::GetByID($task["GROUP_ID"]);
            $projectName = $project["NAME"];
        }

        // Строим URL для задачи
        $taskUrl = "/company/personal/user/".$responsibleUser["ID"]."/tasks/task/view/".$taskId."/";

        /*
         * #TASK_TITLE#
         * #TASK_CREATOR_USER_NAME#
         * #PROJECT_NAME#
         * #TASK_URL#
         */

        Event::sendImmediate(array(
            "EVENT_NAME" => "TASKS_ADD_TASK",
            "LID" => "s1",
            "C_FIELDS" => array(
                "EMAIL" => $responsibleUser["EMAIL"],
                "EMAIL_TO" => $responsibleUser["EMAIL"],
                "TASK_TITLE" => $task['TITLE'],
                "TASK_CREATOR_USER_NAME" => $taskCreatorUser["NAME"]." ".$taskCreatorUser["LAST_NAME"],
                "PROJECT_NAME" => $projectName,
                "TASK_URL" => $taskUrl,
                "TASK_ID" => $taskId,
                "USER_ID" => 1
            ),
        ));
    }

    public static function sendUpdateDealNotification($changedArFields, $dealId, $modifiedById)
    {
        // Проверяем поле KB - если поменялось только оно - это автоматическое обновление
        if (count($changedArFields) == 4 && $changedArFields["UF_CRM_1579077455200"]) {
            return;
        }


        // Сделка
        $deal = CAllCrmDeal::GetByID($dealId);

        // Если сделку меняет ответственный - ничего не делаем
        if($modifiedById == $deal["ASSIGNED_BY"])
            return;

            // Пользователь, внесший изменения
        $modifiedByUser = CUser::GetByID($modifiedById)->Fetch();
        // Ответственный за сделку пользователь
        $assignedByUser = CUser::GetByID($deal["ASSIGNED_BY"])->Fetch();

        global $USER_FIELD_MANAGER;
        $UFDealFields = $USER_FIELD_MANAGER->GetUserFields("CRM_DEAL");

        $dateModified = null;
        $res = array();
        foreach ($changedArFields as $userField => &$val) {
            if ($userField == "~DATE_MODIFY") {
                // #DATE_MODIFIED#
                $dateModified = $val;
                continue;
            }
            if ($userField == "MODIFY_BY_ID" || $userField == "ID")
                continue;


            if (substr($userField, 0, 7) == "UF_CRM_") {
                $isFieldEnum = $UFDealFields[$userField]["USER_TYPE_ID"] == "enumeration";
                $isFieldFile = $UFDealFields[$userField]["USER_TYPE_ID"] == "file";
                $v["FIELD"] = $userField;
                $v["TITLE"] = self::GetUserFieldTitle($userField);
                $v["OLD_VALUE"] = $USER_FIELD_MANAGER->GetUserFieldValue("CRM_DEAL", $userField, $dealId);
                $v["NEW_VALUE"] = $val;
                if ($isFieldFile) {
                    $newFile = null;
                    $oldFile = null;

                    $fileName = "";
                    $oldFileNames = "";
                    foreach ($v["OLD_VALUE"] as $fileId) {
                        if ($fileId > 0) {
                            $fileName = CFile::GetByID($fileId)->Fetch()["FILE_NAME"];
                            $oldFileNames = $oldFileNames . $fileName . ", ";
                        }
                    }

                    $fileName = "";
                    $newFileNames = "";
                    foreach ($v["NEW_VALUE"] as $fileId) {
                        if ($fileId > 0) {
                            $fileName = CFile::GetByID($fileId)->Fetch()["FILE_NAME"];
                            $newFileNames = $newFileNames . $fileName . ", ";
                        }
                    }

                    $v["OLD_VALUE"] = str_pad($oldFileNames, 2, ", ");
                    $v["NEW_VALUE"] = str_pad($newFileNames, 2, ", ");;
                }
                if ($isFieldEnum) {
                    $v["OLD_VALUE"] = self::GetUserFieldEnumValue($userField, $v["OLD_VALUE"], "CRM_DEAL");
                    $v["NEW_VALUE"] = self::GetUserFieldEnumValue($userField, $v["NEW_VALUE"], "CRM_DEAL");
                }
                if ($v["OLD_VALUE"] != $v["NEW_VALUE"]) {
                    $res[$userField] = $v;
                }

                unset($v);
            } else {
                $v["FIELD"] = $userField;
                $v["TITLE"] = Loc::getMessage("CRM_DEAL_FIELD_".$userField); // self::getDealFieldName($userField);
                $v["OLD_VALUE"] = $deal[$userField];
                $v["NEW_VALUE"] = $val;
                if ($v["OLD_VALUE"] != $v["NEW_VALUE"])
                    $res[$userField] = $v;
                unset($v);
            }
        }

        // #DEAL_NUMBER#
        $potNo = self::GetUserFieldValueByLabel("РОТ №", "CRM_DEAL", $dealId);
        // #DEAL_CHANGES#
        $changes = "";
        foreach ($res as $change => $f){
            $changes = $changes . $f["TITLE"].": ".$f["OLD_VALUE"]." => ".$f["NEW_VALUE"].";\r\n";
        }

        // #MODIFIED_BY_USER_NAME#
        $modifiedByUserName = $modifiedByUser["NAME"]." ".$modifiedByUser["LAST_NAME"];

        // #DEAL_URL#
        $dealUrl = "/crm/deal/details/".$dealId."/";

        Event::sendImmediate(array(
            "EVENT_NAME" => "CRM_DEAL_CHANGED",
            "LID" => "s1",
            "C_FIELDS" => array(
                "EMAIL" => $assignedByUser["EMAIL"],
                "EMAIL_TO" => $assignedByUser["EMAIL"],
                "DEAL_NUMBER" => $potNo,
                "DEAL_TITLE" => $deal["TITLE"],
                "DEAL_CHANGES" => $changes,
                "DATE_MODIFIED" => $dateModified,
                "MODIFIED_BY_USER_NAME" => $modifiedByUserName,
                "DEAL_URL" => $dealUrl,
            ),
        ));
/*
        Bitrix\Main\Diag\Debug::writeToFile("*******************************************************", "$dealId", "/debug.txt");
        Bitrix\Main\Diag\Debug::writeToFile($deal, "deal", "/debug.txt");
        Bitrix\Main\Diag\Debug::writeToFile($changedArFields, "changedArFields", "/debug.txt");
        Bitrix\Main\Diag\Debug::writeToFile($res, "arUFDeal", "/debug.txt");
        Bitrix\Main\Diag\Debug::writeToFile($changes, "changes", "/debug.txt");
*/
    }

    public static function sendUserAddedToGroupNotify($arFields){
        /*
         * EMAIL_TO
         * #SOCNET_GROUP_NAME#
         * SOCNET_GROUP_URL
         * */

        $userToAdd = CUser::GetByID($arFields["USER_ID"])->Fetch();
        $project = CSocNetGroup::GetByID($arFields["GROUP_ID"]);
        $groupUrl = "/workgroups/group/" . $arFields["GROUP_ID"] ."/";
        $joinGroupUrl = "/company/personal/user/".$arFields["USER_ID"]."/requests/?INVITE_GROUP=".$arFields["GROUP_ID"]."&CONFIRM=Y";
        $rejectGroupUrl = "/company/personal/user/".$arFields["USER_ID"]."/requests/?INVITE_GROUP=".$arFields["GROUP_ID"]."&CONFIRM=N";

        // Определение роли (E - модератор) (Z - Участник)
        $role = "";
        switch ($arFields["ROLE"]){
            case "E":
                $role = "Модератор";
                break;
            case "Z":
                $role = "Участник";
                break;
        }


        Event::sendImmediate(array(
            "EVENT_NAME" => "SOCNET_USER_ADD_TO_GROUP",
            "LID" => "s1",
            "C_FIELDS" => array(
                "EMAIL" => $userToAdd["EMAIL"],
                "EMAIL_TO" => $userToAdd["EMAIL"],
                "SOCNET_GROUP_NAME" => $project["NAME"],
                "SOCNET_GROUP_URL" => $groupUrl,
                "ROLE" => $role,
                "JOIN_GROUP_URL" => $joinGroupUrl,
                "REJECT_URL" => $rejectGroupUrl
            ),
        ));
    }

    static function getDealFieldName($field){
        $fieldsName = Array();
        $fieldsName["ID"] = "Идентификатор";
        $fieldsName["TITLE"] = "Название";
        $fieldsName["TYPE_ID"] = "Тип сделки";
        $fieldsName["STAGE_ID"] = "";
        $fieldsName["PROBABILITY"] = "";
        $fieldsName["CURRENCY_ID"] = "Валюта";
        $fieldsName["EXCH_RATE"] = "";
        $fieldsName["OPPORTUNITY"] = "Планируемая маржа";
        $fieldsName["TAX_VALUE"] = "";
        $fieldsName["ACCOUNT_CURRENCY_ID"] = "";
        $fieldsName["OPPORTUNITY_ACCOUNT"] = "";
        $fieldsName["TAX_VALUE_ACCOUNT"] = "";
        $fieldsName["LEAD_ID"] = "";
        $fieldsName["COMPANY_ID"] = "";
        $fieldsName["COMPANY_TITLE"] = "";
        $fieldsName["COMPANY_INDUSTRY"] = "";
        $fieldsName["COMPANY_EMPLOYEES"] = "";
        $fieldsName["COMPANY_REVENUE"] = "";
        $fieldsName["COMPANY_CURRENCY_ID"] = "";
        $fieldsName["COMPANY_TYPE"] = "";
        $fieldsName["COMPANY_ADDRESS"] = "";
        $fieldsName["COMPANY_ADDRESS_LEGAL"] = "";
        $fieldsName["COMPANY_BANKING_DETAILS"] = "";
        $fieldsName["COMPANY_LOGO"] = "";
        $fieldsName["CONTACT_ID"] = "";
        $fieldsName["CONTACT_TYPE_ID"] = "";
        $fieldsName["CONTACT_HONORIFIC"] = "";
        $fieldsName["CONTACT_NAME"] = "";
        $fieldsName["CONTACT_SECOND_NAME"] = "";
        $fieldsName["CONTACT_LAST_NAME"] = "";
        $fieldsName["CONTACT_FULL_NAME"] = "";
        $fieldsName["CONTACT_POST"] = "";
        $fieldsName["CONTACT_ADDRESS"] = "";
        $fieldsName["CONTACT_SOURCE_ID"] = "";
        $fieldsName["CONTACT_PHOTO"] = "";
        $fieldsName["QUOTE_ID"] = "";
        $fieldsName["QUOTE_TITLE"] = "";
        $fieldsName["BEGINDATE"] = "";
        $fieldsName["CLOSEDATE"] = "";
        $fieldsName["ASSIGNED_BY_ID"] = "";
        $fieldsName["ASSIGNED_BY_LOGIN"] = "";
        $fieldsName["ASSIGNED_BY_NAME"] = "";
        $fieldsName["ASSIGNED_BY_LAST_NAME"] = "";
        $fieldsName["ASSIGNED_BY_SECOND_NAME"] = "";
        $fieldsName["ASSIGNED_BY_WORK_POSITION"] = "";
        $fieldsName["ASSIGNED_BY_PERSONAL_PHOTO"] = "";
        $fieldsName["CREATED_BY_ID"] = "";
        $fieldsName["CREATED_BY_LOGIN"] = "";
        $fieldsName["CREATED_BY_NAME"] = "";
        $fieldsName["CREATED_BY_LAST_NAME"] = "";
        $fieldsName["CREATED_BY_SECOND_NAME"] = "";
        $fieldsName["MODIFY_BY_ID"] = "";
        $fieldsName["MODIFY_BY_LOGIN"] = "";
        $fieldsName["MODIFY_BY_NAME"] = "";
        $fieldsName["MODIFY_BY_LAST_NAME"] = "";
        $fieldsName["MODIFY_BY_SECOND_NAME"] = "";
        $fieldsName["DATE_CREATE"] = "";
        $fieldsName["DATE_MODIFY"] = "";
        $fieldsName["OPENED"] = "";
        $fieldsName["CLOSED"] = "";
        $fieldsName["COMMENTS"] = "";
        $fieldsName["ADDITIONAL_INFO"] = "";
        $fieldsName["LOCATION_ID"] = "";
        $fieldsName["CATEGORY_ID"] = "";
        $fieldsName["STAGE_SEMANTIC_ID"] = "";
        $fieldsName["IS_NEW"] = "";
        $fieldsName["IS_RECURRING"] = "";
        $fieldsName["IS_RETURN_CUSTOMER"] = "";
        $fieldsName["IS_REPEATED_APPROACH"] = "";
        $fieldsName["SOURCE_ID"] = "";
        $fieldsName["SOURCE_DESCRIPTION"] = "";
        $fieldsName["WEBFORM_ID"] = "";
        $fieldsName["ORIGINATOR_ID"] = "";
        $fieldsName["ORIGIN_ID"] = "";
        $fieldsName["PRODUCT_ID"] = "";
        $fieldsName["EVENT_ID"] = "";
        $fieldsName["EVENT_DATE"] = "";
        $fieldsName["EVENT_DESCRIPTION"] = "";
        $fieldsName["ASSIGNED_BY"] = "";
        $fieldsName["CREATED_BY"] = "";
        $fieldsName["MODIFY_BY"] = "";
        $fieldsName["UTM_SOURCE"] = "";
        $fieldsName["UTM_MEDIUM"] = "";
        $fieldsName["UTM_CAMPAIGN"] = "";
        $fieldsName["UTM_CONTENT"] = "";
        $fieldsName["UTM_TERM"] = "";
        
        return $fieldsName[$field];
    }

    // $groupResults[0]["DUPLICATES"][0]["ENTITIES"][0]

    public static function FillINNAndKPPForDupResult(&$groupResults){
        foreach ($groupResults as &$i1){
            if($i1["DUPLICATES"]){
                foreach ($i1["DUPLICATES"] as &$i2){
                    if($i2["ENTITIES"]){
                        foreach ($i2["ENTITIES"] as &$i3){
                            $companyId = $i3["ENTITY_ID"];
                            $company = CAllCrmCompany::GetByID($companyId);
                            $i3["INN"] = self::GetUserFieldValueByLabel("ИНН", "CRM_COMPANY", $companyId);
                            $i3["KPP"] = self::GetUserFieldValueByLabel("КПП", "CRM_COMPANY", $companyId);
                        }
                    }
                }
            }
        }

        return $groupResults;
    }

    public static function UpdateDealFieldSettings(){
        global $USER;
        $curUserID = $USER->GetID();

        $connection = Bitrix\Main\Application::getConnection();
        $sql = "SELECT DISTINCT s.hide_deal_money FROM m_user_settings s WHERE s.user_id = " . $curUserID;
        $recordset = $connection->query($sql);

        if ($record = $recordset->fetch()) {
            $res = !($record["hide_deal_money"] == 1);
            $val = $res ? "1" : "0";
            $sql = "UPDATE m_user_settings SET hide_deal_money = ".$val." WHERE user_id = " . $curUserID;
            $connection->query($sql);
            return $res;
        }

        $sql = "INSERT INTO m_user_settings (user_id, hide_deal_money) VALUES (".$curUserID.", ".true.")";
        $connection->query($sql);

        return true;
    }

    public static function IsDealFieldsInvisibleEnabled($userId){
        $connection = Bitrix\Main\Application::getConnection();
        $sql = "SELECT DISTINCT s.hide_deal_money FROM m_user_settings s WHERE s.user_id = " . $userId;
        $recordset = $connection->query($sql);

        if ($record = $recordset->fetch()) {
            return $record["hide_deal_money"];
        }

        return false;
    }

    public static function PrepareFieldsAccess($arSelectFields){
        $FIELDS_ACCESS_DENIED = $GLOBALS["FIELDS_ACCESS_DENIED"];

        $userId = CUser::GetID();
        $userGroups = CUser::GetUserGroup($userId);

        foreach ($FIELDS_ACCESS_DENIED as $item) {
            if (in_array($item["GroupId"], $userGroups)) {
                if($arSelectFields[$item["UserField"]])
                    unset($arSelectFields[$item["UserField"]]);

                if(in_array($item["UserField"], $arSelectFields)){
                    $key = array_search($item["UserField"], $arSelectFields);
                    if($key){
                        unset($arSelectFields[$key]);
                    }
                }
            }
        }

        return $arSelectFields;
    }

    public static function ClearAccessDeniedFields(&$rows){
        $FIELDS_ACCESS_DENIED = $GLOBALS["FIELDS_ACCESS_DENIED"];

        $userId = CUser::GetID();
        $userGroups = CUser::GetUserGroup($userId);

        foreach ($FIELDS_ACCESS_DENIED as $item) {
            if (in_array($item["GroupId"], $userGroups)) {
                foreach ($rows as &$r){
                    if($r[$item["UserField"]])
                        $r[$item["UserField"]] = "0";
                }
            }
        }

        return $rows;
    }

    public static function CheckRequiredUserField($arFields, $ufFieldTitle, $entityId, &$e, &$res, $lang = 'ru', $entityTypeId = 'CRM_COMPANY'){
        global $USER_FIELD_MANAGER;
        $ufFieldName = Utility::GetUserFieldNameByTitle($ufFieldTitle, $lang, $entityTypeId);
        $ufFieldValue = $arFields[$ufFieldName];
        if(!isset($ufFieldValue)) {
            $ufFieldValue = $USER_FIELD_MANAGER->GetUserFieldValue($entityTypeId, $ufFieldName, $entityId);
        }
        if($ufFieldValue == null){
            $e = $e . "Не заполнено поле '".$ufFieldTitle."'.\r\n";
            $res = false;
        }
    }


    /*
     * Скрипт для создания таблицы:
     *
       CREATE TABLE m_user_settings (
        user_id int not null primary key,
        hide_deal_money bit not null default 0
    );
     *
     */
}


