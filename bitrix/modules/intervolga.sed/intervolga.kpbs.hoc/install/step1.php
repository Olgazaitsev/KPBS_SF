<?php
/**
 * @author Nikita Kalinin <kalinin@intervolga.ru>
 * @var CMain $APPLICATION
 */
$id = $_REQUEST['id'];
?>
<form action="<?= $APPLICATION->GetCurPage()?>">
    <?= bitrix_sessid_post() ?>
    <input type="hidden" name="lang" value="<?= LANGUAGE_ID ?>">
    <input type="hidden" name="id" value="<?= $id ?>">
    <input type="hidden" name="install" value="Y">
    <input type="hidden" name="step" value="1">

    <p><?= GetMessage("IV_SED_INSTALL_TITLE")?></p>
    <p>
        <label for="license"><?echo GetMessage('IV_SED_LICENSE')?>&nbsp;</label>
        <input type="text" name="sedLicense" id="license" size="50" value="<?= htmlspecialchars($_REQUEST['sedLicense']) ?>">
    </p>

    <input type="submit" name="inst" value="<?echo GetMessage('MOD_INSTALL')?>">
</form>
