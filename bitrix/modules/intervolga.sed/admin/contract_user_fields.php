<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
define('ADMIN_MODULE_NAME', 'intervolga.sed');
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Intervolga\Sed\Tables\ContractUserFieldsTable;

Loc::loadMessages(__FILE__);

global $APPLICATION, $USER_FIELD_MANAGER;

if (
    !$USER->IsAdmin() ||
    !Loader::includeModule('intervolga.sed')
) {
    return;
}

$context = \Bitrix\Main\Context::getCurrent();
$request = $context->getRequest();

$processId = $request->get('PROCESS');
if (intval($processId) <= 0) {
    LocalRedirect('/bitrix/admin/intervolga.sed_process_list.php');
}

$dbContractUserFieldsResult = ContractUserFieldsTable::getList([
    'filter' => [
        'PROCESS_ID' => $processId
    ]
]);
$arContractUserFields = [];
while ($arContractUserFieldsResult = $dbContractUserFieldsResult->fetch()) {
    $arContractUserFields[$arContractUserFieldsResult['FIELD_NAME']] = $arContractUserFieldsResult;
}

if (check_bitrix_sessid() && strlen($request->getPost('save')) > 0) {
    $arPostContractUserFields = $request->getPost('CONTRACT_USER_FIELDS');
    if (is_array($arPostContractUserFields)) {
        foreach ($arPostContractUserFields as $key => $value) {
            $arFields = [
                'SORT' => $value['SORT'],
                'REQUIRED' => $value['REQUIRED'] == 'on' ? true : false,
                'SHOW' => $value['SHOW'] == 'on' ? true : false
            ];
            if (array_key_exists($key, $arContractUserFields)) {
                $id = $arContractUserFields[$key]['ID'];
                ContractUserFieldsTable::update($id, $arFields);
            } else {
                $arFields += ['PROCESS_ID' => $processId, 'FIELD_NAME' => $key];
                ContractUserFieldsTable::add($arFields);
            }
        }
    }

    LocalRedirect($APPLICATION->GetCurPageParam("PROCESS=$processId"));
}

$arUserFields = $USER_FIELD_MANAGER->GetUserFields(
    \Intervolga\Sed\Tables\ContractTable::getUfId(),
    0,
    LANGUAGE_ID
);

foreach ($arUserFields as $key => $value) {
    if (array_key_exists($key, $arContractUserFields)) {
        $arUserFields[$key]['SORT'] = $arContractUserFields[$key]['SORT'];
        $arUserFields[$key]['MANDATORY'] = $arContractUserFields[$key]['REQUIRED'];
        $arUserFields[$key]['SHOW'] = $arContractUserFields[$key]['SHOW'];
    } else {
        $arUserFields[$key]['SHOW'] = 'Y';
    }
}

$tabControl = new \CAdminTabControl('tabControl', []);
?>
    <form method="POST"
          action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($mid) ?>&lang=<?= LANGUAGE_ID ?>">
        <input type="hidden" name="PROCESS" value="<?= $processId ?>"/>
        <table width="100%">
            <tr>
                <td colspan="2" align="center">
                    <table class="internal" width="100%">
                        <tr class="heading">
                            <td class="stage-cell" colspan="6">
                                <h3><?= Loc::getMessage('INTERVOLGA_SED.CONTRACT_USER_FIELDS_HEADER_FIELD_LABEL') ?></h3>
                            </td>
                        </tr>
                        <tr class="heading" align="center">
                            <td>
                                <?= Loc::getMessage('INTERVOLGA_SED.CONTRACT_USER_FIELDS_HEADER_FIELD_LABEL') ?>
                            </td>
                            <td>
                                <?= Loc::getMessage('INTERVOLGA_SED.CONTRACT_USER_FIELDS_HEADER_FIELD_NAME') ?>
                            </td>
                            <td>
                                <?= Loc::getMessage('INTERVOLGA_SED.CONTRACT_USER_FIELDS_HEADER_FIELD_TYPE') ?>
                            </td>
                            <td>
                                <?= Loc::getMessage('INTERVOLGA_SED.CONTRACT_USER_FIELDS_HEADER_SORT') ?>
                            </td>
                            <td>
                                <?= Loc::getMessage('INTERVOLGA_SED.CONTRACT_USER_FIELDS_HEADER_REQUIRED') ?>
                            </td>
                            <td>
                                <?= Loc::getMessage('INTERVOLGA_SED.CONTRACT_USER_FIELDS_HEADER_SHOW') ?>
                            </td>
                        </tr>
                        <? foreach ($arUserFields as $arUserField) : ?>
                            <tr>
                                <td align="center">
                                    <label>
                                        <?= $arUserField['EDIT_FORM_LABEL'] ?>
                                    </label>
                                </td>
                                <td align="center">
                                    <label>
                                        <?= $arUserField['FIELD_NAME'] ?>
                                    </label>
                                </td>
                                <td align="center">
                                    <label>
                                        <?= $arUserField['USER_TYPE']['DESCRIPTION'] ?>
                                    </label>
                                </td>
                                <td align="center">
                                    <label>
                                        <input class="crm-tabs-by-group-sort"
                                               name="CONTRACT_USER_FIELDS[<?= $arUserField['FIELD_NAME'] ?>][SORT]"
                                               type="text"
                                               value="<?= $arUserField['SORT'] ?>"/>
                                    </label>
                                </td>
                                <td align="center">
                                    <label>
                                        <input class="crm-tabs-by-group-show"
                                               name="CONTRACT_USER_FIELDS[<?= $arUserField['FIELD_NAME'] ?>][REQUIRED]"
                                               type="checkbox"
                                            <?= $arUserField['MANDATORY'] == 'Y' ? 'checked' : '' ?>/>
                                    </label>
                                </td>
                                <td align="center">
                                    <label>
                                        <input class="crm-tabs-by-group-show"
                                               name="CONTRACT_USER_FIELDS[<?= $arUserField['FIELD_NAME'] ?>][SHOW]"
                                               type="checkbox"
                                            <?= $arUserField['SHOW'] == 'Y' ? 'checked' : '' ?>/>
                                    </label>
                                </td>
                            </tr>
                        <? endforeach; ?>
                    </table>
                </td>
            </tr>
        </table>
        <? $tabControl->Buttons(
            array(
                "btnApply" => false,
                "btnCancel" => false,
                "btnSaveAndAdd" => false
            ));
        ?>
        <?= bitrix_sessid_post(); ?>
        <? $tabControl->End(); ?>
    </form>
<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");