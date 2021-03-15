<?php
defined('B_PROLOG_INCLUDED') || die;
use Bitrix\Main\Page\Asset;
\Bitrix\Main\UI\Extension::load("ui.buttons");
Asset::getInstance()->addJs("//code.jquery.com/ui/1.12.1/jquery-ui.js");
Asset::getInstance()->addJs("//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js");
\Bitrix\Main\Loader::includeModule('kpbs.custom');
?>

<html lang="ru">
<head>
    <title>Отчет по квартальным КПЭ менеджеров</title>
    <meta charset="utf-8">
</head>
<body>
<div class="db">
    <div id="planinput">
        <p>Выберите сотрудника, год и квартал для целей построения отчета</p>
        <p class="label_title">Год</p>
        <div id="container"></div>
        <p class="label_title">Квартал</p>
        <div id="container"></div>
        <p class="label_title">Сотрудник</p>
        <div id="employees"></div>
        <input id="btnCalcfact" class="ui-btn ui-btn-primary" type="submit" value="Вывести отчет"/>
        <div id="resultfact">
            <div id="resultfacttext"></div>
            <div id="resultfactdate"></div>
        </div>
    </div>
    <?/*echo "<pre>";
    print_r($arResult);
    echo "</pre>";*/?>
</div>
<script>
    $(document).ready(function() {
        $("#btnCalcfact").click(function(){
            BX.ajax.runAction('kpbs:custom.api.kpicalcul.getStat', {
                data: {
                    user: '1',
                    from: '01.01'
                }
            }).then(function (response) {
                console.log(response);
            }, function (error) {
                //сюда будут приходить все ответы, у которых status !== 'success'
                console.log(error);

            });


            /*$("#resultfacttext").empty()
            $("#resultfactdate").empty()
            $(".tabs").width('100%')
            var setusersf = $("#userf").val()
            setusersf.forEach(function (setuserf) {
                if(setuserf!='all') {
                    generatefact(setuserf)
                }
            })*/
        });
    })
</script>





