<?php
defined('B_PROLOG_INCLUDED') || die;
use Bitrix\Main\Page\Asset;
Asset::getInstance()->addCss("//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css");
Asset::getInstance()->addCss("//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css");
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
        <p>Выберите год, квартал и сотрудников для целей построения отчета</p>
        <p class="label_title">Год</p>
        <div id="years" class="container">
        </div>
        <p class="label_title">Квартал</p>
        <div id="quarters" class="container">
        </div>
        <p class="label_title">Сотрудник</p>
        <div id="employees">
        </div>
        <br>
        <input id="btnCalcfact" class="ui-btn ui-btn-primary" type="submit" value="Вывести отчет"/>
        <div id="resultfact">
            <div id="resultfacttext"></div>
            <div id="resultfactdate"></div>
        </div>
</div>
</body>
<script>
    $(document).ready(function() {
        var year = new Date();
        var json = [
            {value: year.getFullYear(), text: year.getFullYear()},
            {value: year.getFullYear()+1, text: year.getFullYear()-1},
            {value: year.getFullYear()+2, text: year.getFullYear()-2}
        ];
        //console.log(json)
        //console.log(typeof json)

        var select = $("<select></select>").attr("id", "year").attr("name", "year");
        $.each(json,function(index,json){
            select.append($("<option></option>").attr("value", json.value).text(json.text));
        });
        $("#years").html(select);
        $("#year :first").attr("selected", "selected");

        var json = [
            {value: "1", text: "I"},
            {value: "2", text: "II"},
            {value: "3", text: "III"},
            {value: "4", text: "IV"}
        ];
        //console.log(json)
        //console.log(typeof json)

        var select = $("<select></select>").attr("id", "quarter").attr("name", "quarter");
        $.each(json,function(index,json){
            select.append($("<option></option>").attr("value", json.value).text(json.text));
        });
        $("#quarters").html(select);

        var users =  <?= \CUtil::phpToJSObject($arResult['USERS']);?>;

        var select3 = $("<select class=\"js-select3\" multiple=\"multiple\"></select>").attr("id", "userf").attr("name", "userf").attr("multiple", "multiple");
        select3.append($("<option></option>").attr("value", 'all').text('Выбрать всех'));
        $.each(users,function(index,users){
            //console.log(users)
            select3.append($("<option></option>").attr("value", users.ID).text(users.NAME + ' ' + users.LAST_NAME));
        });
        $("#employees").html(select3);

        $(".js-select3").select2({
            closeOnSelect: false,
            placeholder: "Сотрудники",
            allowHtml: true,
            allowClear: true
        });

        $('.js-select3').on("select2:select", function (e) {
            var data = e.params.data.text;
            if(data=='Выбрать всех'){
                $(".js-select3 > option").prop("selected","selected");
                $(".js-select3").trigger("change");
            }
        });

        $("#btnCalcfact").click(function(){
            $("#resultfacttext").empty()
            $("#resultfactdate").empty()
            var setusersf = $("#userf").val()
            var year = $("#year").val()
            var quarter = $("#quarter").val()
            setusersf.forEach(function (setuserf) {
                if(setuserf!='all') {
                    BX.ajax.runAction('kpbs:custom.api.signal.getSignal', {
                        data: {
                            user: setuserf,
                            year: year,
                            quater: quarter
                        }
                    }).then(function (response) {
                        console.log(response);
                        var resultarr = response.data
                        drawfact(resultarr, users, setuserf)

                    }, function (error) {
                        //сюда будут приходить все ответы, у которых status !== 'success'
                        console.log(error);

                    });
                }
            })
        });
    })

    function drawfact(resultarr, users, setuserf) {
        //console.log(resultarr)
        //console.log(users)
        //console.log(setuserf)
        var managername
        $.each(users,function(index,users) {
            if (users.ID == setuserf) {
                managername = users.NAME + ' ' + users.LAST_NAME
            }
        })
        var manager = $("<p></p>").text("Менеджер "+ managername)
        $("#resultfactdate").append(manager)
        var table = $("<table></table>").attr("id", "tablefact").attr("name", "tablefact").attr("border", 1).attr("cellspacing",0)
        var tr = $("<tr></tr>")
        tr.append($("<th></th>").text("Показатель").width(200))
        tr.append($("<th></th>").text("Вес").width(70))
        tr.append($("<th></th>").text("Значение").width(70))
        tr.append($("<th></th>").text("Балл").width(70))
        table.append(tr)
        for (var key in resultarr) {
            tr = $("<tr></tr>")
            var kpiname
            if(key=='KVq') {
                kpiname = 'КВ - интегральный, рост за период %'
            } else if(key=='KVavg') {
                kpiname = 'КВ - средний по продавцу, диапазон'
            } else if(key=='QualAct') {
                kpiname = 'Качество работы с системой - актуальность'
            } else if(key=='CRMactivity') {
                kpiname = 'Качество работы с системой - вовлеченность'
            } else if(key=='CNTLev') {
                kpiname = 'Средний уровень контакта, диапазон'
            } else if(key=='CNTNet') {
                kpiname = 'Средняя сеть контактов по заказчику, диапазон'
            }

            tr.append($("<td></td>").text(kpiname))
            tr.append($("<td></td>").text(resultarr[key]['weight']))
            tr.append($("<td></td>").text(resultarr[key]['value']))
            tr.append($("<td></td>").text(resultarr[key]['rate']))
            table.append(tr)
        }
        $("#resultfactdate").append(table)
    }
</script>





