<?php namespace Intervolga\Sed\Tools;

use Bitrix\Main\Application;
use Intervolga\Sed\Tools\Utils;
use Intervolga\Sed\Entities\TaskStatusField;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class AjaxUtils
{
    public static function processRequest()
    {
        $request = \Bitrix\Main\Context::getCurrent()->getRequest();

        if(bitrix_sessid() != $request->getPost('sessId')) {
            static::throwAjaxError(Loc::getMessage('C.AJAXUTILS.ER_BAD_SSID'), 'sessId');
        }

        $action = 'action' . (string)$request->getPost('action');

        if(method_exists(get_class(), $action)) {
            $connection = Application::getConnection();
            try {
                $connection->startTransaction();
                $result = static::$action($request->getPost('params'));
                $connection->commitTransaction();
                static::throwAjaxAnswer($result);
            } catch (\Exception $e) {
                $connection->rollbackTransaction();
                static::throwAjaxError($e->getMessage(), 'comment');
            }
        }
        static::throwAjaxError(Loc::getMessage('C.AJAXUTILS.ER_METHOD_NOT_FOUND') . ' \'' . $action . '()\'', 'action');
    }

    /**
     * @param string $description
     * @param string $type
     */
    protected static function throwAjaxError($description, $type = '')
    {
        static::throwAjaxAnswer(null, array(
            'type' => $type,
            'description' => $description
        ));
    }

    /**
     * @param $resultData
     * @param mixed $errorInfo
     */
    protected static function throwAjaxAnswer($resultData, $errorInfo = null)
    {
        echo json_encode(array(
            'resultData' => $resultData,
            'errorInfo' => $errorInfo,
        ));
        die();
    }

    /**
     * @param $params
     */
    protected static function actionSetTaskUfStatus($params)
    {
        if(!empty($params['needComment']) && !Utils::checkComments($params['taskId'], $params['userId'])) {
            static::throwAjaxError(Loc::getMessage('C.AJAXUTILS.ER_ADD_COMMENT_FOR_STATUS'), 'comment');
        }

        $statusFieldName = TaskStatusField::getOneByEntityFilter($params['taskTypeId'])->getFieldName();
        Utils::updateTaskItemStatus($params['taskId'], $params['userId'], $statusFieldName, $params['newUfStatusId'], $params['nativeStatusId']);
    }

    protected static function actionUpdateTaskTypeElement($params)
    {
        if(empty($params['fieldToUpdate']) || empty($params['taskTypeId'])) {
            static::throwAjaxError(Loc::getMessage('C.AJAXUTILS.ER_NO_REQ_PARAMS') . ': \'fieldToUpdate\',  \'taskTypeId\'', 'emptyParam');
        }

        $action = 'set' . static::mbUcfirst((string)$params['fieldToUpdate']);
        $taskType = \Intervolga\Sed\Entities\TaskTypeElement::getById($params['taskTypeId']);
        if(method_exists($taskType, $action)) {
            $taskType->$action($params['fieldValue']);
            $taskType->save();
        }
        else {
            static::throwAjaxError(Loc::getMessage('C.AJAXUTILS.ER_METHOD_NOT_FOUND') . ' \'' . $action . '()\'', 'action');
        }
    }

    protected static function actionUpdateTaskStatusElement($params)
    {
        if(empty($params['fieldToUpdate']) || empty($params['taskTypeId']) || empty($params['taskStatusId'])) {
            static::throwAjaxError(Loc::getMessage('C.AJAXUTILS.ER_NO_REQ_PARAMS') . ': \'fieldToUpdate\',  \'taskTypeId\', \'taskStatusId\'', 'emptyParam');
        }

        $taskType = \Intervolga\Sed\Entities\TaskTypeElement::getById($params['taskTypeId']);
        $taskStatus = \Intervolga\Sed\Entities\TaskStatusElement::getById($params['taskStatusId'], $taskType);
        $action = 'set' . static::mbUcfirst((string)$params['fieldToUpdate']);
        if(method_exists($taskStatus, $action)) {
            $taskStatus->$action($params['fieldValue']);
            $taskStatus->save();
        }
        else {
            static::throwAjaxError(Loc::getMessage('C.AJAXUTILS.ER_METHOD_NOT_FOUND') . ' \'' . $action . '()\'', 'action');
        }
    }

    protected static function actionCreateTaskTypeElement($params)
    {
        $taskType = \Intervolga\Sed\Entities\TaskTypeElement::createEmpty()
            ->setName($params['name'])
            ->setCode($params['code'])
            ->setSort($params['sort'])
            ->save();

        return array(
            'id' => $taskType->getId(),
            'name' => $taskType->getName(),
            'code' => $taskType->getCode(),
            'sort' => $taskType->getSort(),
        );
    }

    protected static function actionCreateTaskStatusElement($params)
    {
        $taskStatus = \Intervolga\Sed\Entities\TaskStatusElement::createEmpty($params['taskTypeId'])
            ->setName($params['name'])
            ->setCode($params['code'])
            ->setSort($params['sort'])
            ->setNativeTaskStatus($params['nativeTaskStatus'])
            ->save();

        return array(
            'id' => $taskStatus->getId(),
            'name' => $taskStatus->getName(),
            'code' => $taskStatus->getCode(),
            'sort' => $taskStatus->getSort(),
            'nativeTaskStatus' => $taskStatus->getNativeTaskStatus()
        );
    }

    protected static function actionDeleteTaskType($params)
    {
        $params['entityId'] = (int)$params['entityId'];
        if($params['entityId'] < 1) {
            static::throwAjaxError(Loc::getMessage('C.AJAXUTILS.ER_NO_OR_EMPTY_REQ_PARAMS') . ' \'entityId\'', 'emptyParam');
        }

        try {
            $processTaskType = \Intervolga\Sed\Entities\ProcessTaskType::getOneByFilter(array('TASK_TYPE_ID' => $params['entityId']));
            $taskType = \Intervolga\Sed\Entities\TaskTypeElement::getById($params['entityId']);

            $processTaskType->deleteSelf();
            $result = $taskType->delete();
            if(!$result) {
                static::throwAjaxError(Loc::getMessage('C.AJAXUTILS.ER_WHILE_DEL_TASK_TYPE'), 'delete');
            }
        }
        catch (\Bitrix\Main\ObjectNotFoundException $e) {}
    }

    protected static function actionDeleteTaskStatus($params)
    {
        $params['taskTypeId'] = (int)$params['taskTypeId'];
        $params['entityId'] = (int)$params['entityId'];
        if($params['taskTypeId'] < 1 || $params['entityId'] < 1) {
            static::throwAjaxError(Loc::getMessage('C.AJAXUTILS.ER_NO_OR_EMPTY_REQ_ONE_OF_PARAMS') . ': \'taskTypeId\', \'entityId\'', 'emptyParam');
        }

        $taskStatus = \Intervolga\Sed\Entities\TaskStatusElement::getById($params['entityId'], $params['taskTypeId']);
        $result = $taskStatus->delete();
        if(!$result) {
            static::throwAjaxError(Loc::getMessage('C.AJAXUTILS.ER_WHILE_DEL_STATUS_TYPE'), 'delete');
        }
    }

    protected static function mbUcfirst($str)
    {
        return mb_strtoupper(mb_substr($str, 0, 1)) . mb_substr($str, 1);
    }
    /*
     * Class Custom END
     */
    
    
    /**
     * @param $params
     * @return string
     */
    protected static function actionGetUserSelector($params)
    {
        ob_start();
        global $APPLICATION;
        $APPLICATION->IncludeComponent(
            "bitrix:intranet.user.selector.new",
            "abp",
            array(
                "MULTIPLE" => "N",
                "NAME" => "TMP_TMP_SELECTOR",
                "POPUP" => "N",
                "ON_CHANGE" => "onTmpChange",
                "SITE_ID" => SITE_ID,
                'SHOW_EXTRANET_USERS' => 'FROM_MY_GROUPS',
                'DISPLAY_TAB_GROUP' => 'Y',
                'SHOW_LOGIN' => 'Y',
                'VALUE' => 1 // реальный id пользователя
            ),
            null,
            array("HIDE_ICONS" => "Y")
        );
        return ob_get_clean();
    }

    protected static function checkParamInteger($paramKey, $params)
    {
        if($params[$paramKey] < 1) {
            static::throwAjaxError(Loc::getMessage('C.AJAXUTILS.ER_NO_OR_EMPTY_REQ_PARAMS') . ' \'' . $paramKey . '\'', 'emptyParam');
        }
    }

    protected static function actionDeleteContract($params)
    {
        $params['entityId'] = (int)$params['entityId'];
        static::checkParamInteger('entityId', $params);
        \Intervolga\Sed\Entities\Contract::delete($params['entityId']);
    }

    protected static function actionDeleteProcess($params)
    {
        $params['entityId'] = (int)$params['entityId'];
        static::checkParamInteger('entityId', $params);
        \Intervolga\Sed\Entities\Process::delete($params['entityId']);
    }

    protected static function actionDeleteRole($params)
    {
        $params['entityId'] = (int)$params['entityId'];
        static::checkParamInteger('entityId', $params);
        \Intervolga\Sed\Entities\ParticipantRole::delete($params['entityId']);
    }

    protected static function actionDeleteProcessStatus($params)
    {
        $params['entityId'] = (int)$params['entityId'];
        static::checkParamInteger('entityId', $params);
        \Intervolga\Sed\Entities\ProcessStatus::delete($params['entityId']);
    }

    protected static function actionDeleteProcessTaskType($params)
    {
        $params['entityId'] = (int)$params['entityId'];
        static::checkParamInteger('entityId', $params);
        \Intervolga\Sed\Entities\ProcessTaskType::delete($params['entityId']);
    }

    protected static function actionDeleteProcessTaskGroup($params)
    {
        $params['entityId'] = (int)$params['entityId'];
        static::checkParamInteger('entityId', $params);
        \Intervolga\Sed\Entities\ProcessTaskGroup::delete($params['entityId']);
    }

    protected static function actionDeleteTaskStatusTrigger($params)
    {
        $params['entityId'] = (int)$params['entityId'];
        static::checkParamInteger('entityId', $params);
        \Intervolga\Sed\Entities\TaskStatusTrigger::delete($params['entityId']);
    }

    protected static function actionDeleteTaskGroupStatusTrigger($params)
    {
        $params['entityId'] = (int)$params['entityId'];
        static::checkParamInteger('entityId', $params);
        \Intervolga\Sed\Entities\TaskGroupStatusTrigger::delete($params['entityId']);
    }

    protected static function actionDeleteContractStatusTrigger($params)
    {
        $params['entityId'] = (int)$params['entityId'];
        static::checkParamInteger('entityId', $params);
        \Intervolga\Sed\Entities\ContractStatusTrigger::delete($params['entityId']);
    }
}