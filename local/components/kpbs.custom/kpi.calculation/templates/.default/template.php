<?php
defined('B_PROLOG_INCLUDED') || die;
use Bitrix\Main\Page\Asset;
\Bitrix\Main\UI\Extension::load("ui.forms");
\Bitrix\Main\UI\Extension::load("ui.buttons");
//\Bitrix\Main\UI\Extension::load("ui.icons");
\Bitrix\Main\UI\Extension::load("ui.notification");
\Bitrix\Main\UI\Extension::load("ui.hint");
\Bitrix\Main\UI\Extension::load("ui.alerts");
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
        <p>Выберите текущую дату, год, кварталы и сотрудников для целей построения отчета</p>
        <div class="block__date">
            <div>
                <p>Текущая дата</p>
                <input type="text" id="cur_date" name="datebegin" onclick="BX.calendar({node: this, field: this, bTime: false});">
            </div>
        </div>
        <!-- <p class="label_title">Текущая дата</p>
        <input type="text" id="cur_date" name="datebegin" onclick="BX.calendar({node: this, field: this, bTime: false});"> -->
        <p class="label_title">Год</p>
        <div id="years" class="container">
        </div>
        <!--<p class="label_title">Квартал</p>
        <div id="quarters"> -->
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
        var options = {
            year: 'numeric',
            month: 'numeric',
            day: 'numeric',
            timezone: 'UTC'
        };

        $("#cur_date").val(new Date().toLocaleString("ru", options))

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

        /*var json2 = [
            {value: "1", text: "1 квартал"},
            {value: "2", text: "2 квартал"},
            {value: "3", text: "3 квартал"},
            {value: "4", text: "4 квартал"}
        ];


        var select = $("<select class=\"js-select2\"></select>").attr("id", "quarter").attr("name", "quarter").attr("multiple", "multiple");
        $.each(json2,function(index,json){
            select.append($("<option></option>").attr("value", json.value).text(json.text));
        });
        $("#quarters").html(select);

        $(".js-select2").select2({
            closeOnSelect: false,
            placeholder: "Кварталы",
            allowHtml: true,
            allowClear: true
        });*/

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
            //var curdate = $("#cur_date").val()
            var curdate = $("#cur_date").val()
            //var quarter = $("#quarter").val()
            if(!curdate) {
                alert('Нужно указать текущую дату')
            } else {
                setusersf.forEach(function (setuserf) {
                    if(setuserf!='all') {
                        BX.ajax.runAction('kpbs:custom.api.signal.getSignal', {
                            data: {
                                user: setuserf,
                                year: year,
                                //quarters: quarter,
                                curdate: curdate
                            }
                        }).then(function (response) {
                            console.log(response);
                            var resultarr = response.data
                            drawfact(resultarr, users, setuserf, curdate)

                        }, function (error) {
                            //сюда будут приходить все ответы, у которых status !== 'success'
                            console.log(error);

                        });
                    }
                })
            }
        });
    })

    function drawfact(resultarr, users, setuserf, curdate) {
        console.log(resultarr)
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
        if(curdate) {
            tr.append($("<th></th>").text("Текущий квартал").width(100))
        }
        var quarters = resultarr['Q']
        $.each(quarters,function(index,quarter) {
            tr.append($("<th></th>").text(quarter + "квартал").width(100))
        })
        table.append(tr)

        for (var key in resultarr) {
            if(key!='Q') {
                tr = $("<tr></tr>")
                var kpiname
                var needempty = false
                if(key=='X1') {
                    kpiname = 'КВ - интегральный, рост за период %'
                } else if(key=='X2') {
                    kpiname = 'КВ - средний по продавцу, диапазон'
                } else if(key=='X3') {
                    kpiname = 'Качество работы с системой - актуальность'
                } else if(key=='X4') {
                    kpiname = 'Качество работы с системой - вовлеченность'
                } else if(key=='X5') {
                    kpiname = 'Средний уровень контакта, диапазон'
                } else if(key=='X6') {
                    kpiname = 'Средняя сеть контактов по заказчику, диапазон'
                } else if(key=='X_ALL') {
                    kpiname = 'TOTAL POINTS'
                    needempty = true
                } else if(key=='X_BONUS1') {
                    kpiname = 'Плановая маржа на год'
                } else if(key=='X_BONUS2') {
                    kpiname = 'Текущая маржа'
                } else if(key=='X_BONUS3') {
                    kpiname = 'БАЗА для формирования бонуса'
                } else if(key=='X_BONUS4') {
                    kpiname = 'Бонусные БАЛЛЫ'
                } else if(key=='X_BONUS5') {
                    kpiname = 'Сумма выплат в этом году'
                } else if(key=='X_BONUS6') {
                    kpiname = 'Начисление за текущий квартал'
                } else if(key=='X_BONUS7') {
                    kpiname = 'К выплате в конце квартала (с учетом кв. к-та)'
                }

                tr.append($("<td></td>").text(kpiname))
                if(curdate) {
                    var color = 'SpringGreen'
                    if(resultarr[key]['c']['kach']==0) {
                        color = 'red'
                    } else if(resultarr[key]['c']['kach']==0.5) {
                        color = 'yellow'
                    }
                    tr.append($("<td></td>").text(resultarr[key]['c']['rate']).width(100).attr('bgcolor', color))
                }
                if(key=='X1' || key=='X2' || key=='X3' || key=='X4' || key=='X5' || key=='X6' || key=='X_ALL') {
                    $.each(quarters,function(index,quarter) {
                        var color = 'SpringGreen'
                        if(resultarr[key][quarter]['kach']==0) {
                            color = 'red'
                        } else if(resultarr[key][quarter]['kach']==0.5) {
                            color = 'yellow'
                        }
                        tr.append($("<td></td>").text(resultarr[key][quarter]['rate']).width(100).attr('bgcolor', color))
                    })
                }

                table.append(tr)

                if(needempty) {
                    tr = $("<tr></tr>")
                    tr.append($("<td></td>").text("Бонус:").css('font-weight', 'bold'))
                    table.append(tr)
                }
            }
        }
        $("#resultfactdate").append(table)
    }
</script>





