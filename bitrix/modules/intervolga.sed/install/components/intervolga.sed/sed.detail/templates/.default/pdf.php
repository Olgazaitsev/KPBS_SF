<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var array $arResult */
/** @var $APPLICATION */
/** @var $component */
?>
<h2><?php echo $arResult['CONTRACT_INFO']['HEADER']?></h2>
<table width="100%">
    <tr>
        <td style="white-space: nowrap; padding: 10px;"><?=GetMessage('SED_DET_TPL.CONTR_NAME');?></td>
        <td width="100%"><?php echo $arResult['CONTRACT_INFO']['NAME']?></td>
    </tr>
    <tr>
        <td style="white-space: nowrap; padding: 10px;"><?=GetMessage('SED_DET_TPL.START_NEGOTIATION');?></td>
        <td><?php echo $arResult['CONTRACT_INFO']['HARMONIZATION_START']?></td>
    </tr>
    <tr>
        <td style="white-space: nowrap; padding: 10px;"><?=GetMessage('SED_DET_TPL.NEGOTIATION_INIT');?></td>
        <td><?php echo $arResult['CONTRACT_INFO']['INITIATOR_FULL_NAME']?></td>
    </tr>
    <tr>
        <td style="white-space: nowrap; padding: 10px;"><?=GetMessage('SED_DET_TPL.CUR_NEGOTIATION_STATUS');?></td>
        <td><?php echo $arResult['CONTRACT_INFO']['STATUS']?></td>
    </tr>
</table>
<br/>
<h2><?=GetMessage('SED_DET_TPL.NEGOTIATION_PROC');?></h2>
<table width="100%">
    <?php foreach ($arResult['CONTRACT_INFO']['PARTICIPANTS'] as $index => $participant):?>
        <tr>
            <td colspan="4" valign="top"><?php echo $index + 1 ?>. <?php echo $participant['USER']['ROLE_NAME']?>:</td>
        </tr>
        <tr>
            <td valign="top" width="33%"><?php echo $participant['USER']['FULL_NAME']?></td>
            <td valign="top" width="33%"><?php echo $participant['TASK']['STATUS_NAME']?></td>
            <td valign="top" width="33%"><?php echo $participant['TASK']['STATUS_CHANGED_DATE']?></td>
        </tr>
    <?php endforeach;?>
</table>