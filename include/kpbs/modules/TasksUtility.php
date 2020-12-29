<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/tasks/classes/general/taskitem.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");

class TasksUtility
{
    public static function CreateTask($taskTitle, $taskDescription, $taskResponsible)
    {
        if(CModule::IncludeModule("tasks"))
        {
            //здесь можно использовать функции классов модуля tasks
            $arFields = array("TITLE" => $taskTitle,
                "DESCRIPTION" => $taskDescription,
                "RESPONSIBLE_ID" => $taskResponsible,
                "CREATED_BY" => 1,
                "DEADLINE" => "25.08.2020"
    //          , "PRIORITY" => $priority_var 1
            );

            $newTask = \CTaskItem::add($arFields, 1);

            $arFields = Array(
                "TASK_ID" => $newTask["ID"],
                "USER_ID" => $newTask["RESPONSIBLE_ID"],
                "REMIND_DATE" => "26.08.2020 09:00:00",
                "TYPE" => CTaskReminders::REMINDER_TYPE_COMMON,
                "TRANSPORT" => CTaskReminders::REMINDER_TRANSPORT_EMAIL
            );

            $obTaskReminders = new CTaskReminders;
            $ID = $obTaskReminders->Add($arFields);
        }
    }

    public static function CheckTaskExists($responsibleId, $title){
        $connection = Bitrix\Main\Application::getConnection();
        $sql = "select COUNT(*) as COUNT from b_tasks where TITLE like '".rtrim($title)."' AND RESPONSIBLE_ID = ".$responsibleId." AND ZOMBIE <> 'Y';";

        $recordset = $connection->query($sql);

        if ($record = $recordset->fetch()) {
            if($record["COUNT"] != 0)
                return true;
        }

        return false;
    }

    public static function CreateTaskToFillNDA($cN)
    {
        $title = "NDA для ".$cN["TITLE"];
        $description = "Необходимо заполнить данные о NDA.";
        if(self::CheckTaskExists($cN["RESPONSIBLE_ID"], $title))
            return;

        $deadline = (new DateTime('now'))->modify("+7 day");

        if(CModule::IncludeModule("tasks")) {
            $arFields = array("TITLE" => $title,
                "DESCRIPTION" => $description,
                "RESPONSIBLE_ID" => $cN["RESPONSIBLE_ID"],
                "CREATED_BY" => 1,
                "DEADLINE" => $deadline->format("d.m.Y"),
                "UF_CRM_TASK" => array("CO_".$cN["COMPANY_ID"])
                //          , "PRIORITY" => $priority_var 1
            );

            $newTask = \CTaskItem::add($arFields, 1);
        }
    }

    public static function CreateTaskToProlongateNDA($cE)
    {
        $title = "Продление NDA для ".$cE["TITLE"];
        $description = "Необходимо продлить NDA, срок окончания ".$cE["NDA_TERM"].".";
        if(self::CheckTaskExists($cE["RESPONSIBLE_ID"], $title))
            return;

        $deadline = (new DateTime('now'))->modify("+7 day");

        if(CModule::IncludeModule("tasks")) {
            $arFields = array("TITLE" => $title,
                "DESCRIPTION" => $description,
                "RESPONSIBLE_ID" => $cE["RESPONSIBLE_ID"],
                "CREATED_BY" => 1,
                "DEADLINE" => $deadline->format("d.m.Y"),
                "UF_CRM_TASK" => array("CO_".$cE["COMPANY_ID"])
                //          , "PRIORITY" => $priority_var 1
            );

            $newTask = \CTaskItem::add($arFields, 1);
        }
    }
}
