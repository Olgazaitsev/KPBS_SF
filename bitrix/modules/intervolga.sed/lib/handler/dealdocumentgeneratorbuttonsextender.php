<?php
namespace intervolga\sed\handler;

use Bitrix\Crm\Integration\DocumentGenerator\DataProvider\Deal;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use CUtil;


class DealDocumentGeneratorButtonsExtender {
    public static function extendDealDocumentGeneratorButtons() {
        $contractDealReferenceUserFieldCode = Option::get('intervolga.sed', 'intervolga_sed_contract_deal_code');
        if (empty($contractDealReferenceUserFieldCode)) {
            return;
        }

        \CJSCore::Init('deal_document_generator_buttons_extension');

        $asset = Asset::getInstance();
        ob_start();
        ?>
        <script>
            BX.message({
                SED_CREATE_CONTRACT_PROCESS_BUTTON_TITLE:'<?=Loc::getMessage("SED_CREATE_CONTRACT_PROCESS_BUTTON_TITLE")?>',
                SED_CREATE_CONTRACT_PROCESS_BUTTON_DEAL_UF_CODE:<?=CUtil::PhpToJSObject($contractDealReferenceUserFieldCode)?>,
                DOCUMENT_GENERATOR_DEAL_DATA_PROVIDER_CLASS: <?=CUtil::PhpToJSObject(Deal::class)?>
            });
        </script>
        <?php
        $result = ob_get_clean();
        $asset->addString($result);
    }
}
