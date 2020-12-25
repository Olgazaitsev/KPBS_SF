<?
//echo '<pre>'.print_r($_REQUEST, TRUE).'</pre>';//exit;
$resp = 9;
$run = 'ALL'; // ALL все сделки с датой изменения больше 7 дней от текущей,
			   // ALL1 все сделки с датой изменения больше 7 дней от текущей, но задачу ставит только по одну, для теста
			   // DAY все сделки с датой изменения равной 7 дней от текущей

//require_once('b24.php');

$domain = '10.10.200.3';
if (strlen($_REQUEST['REFRESH_ID']) > 1) $ref = $_REQUEST['REFRESH_ID'];
	else $ref = file_get_contents('token');
if (!($ref)) {echo 'exit';exit;}
$par = array(
		'grant_type' => 'refresh_token',
		'client_id' => 'local.5fa2ef667346a1.92467318', 
		'client_secret' => 'NyT8wBkKtPbZARph34nsEqj7yJQf3EYNhvaOG7mPPSn0kqQGgT', 
		'refresh_token' => $ref
			);

$res = restToken($par);
$token = $res['access_token'];
$ref = $res['refresh_token'];
if ($ref) file_put_contents('token', $ref);

//$res = restCommand('crm.dealcategory.stage.list', array('id'=> 0), $token, $domain);
//[STATUS_ID] => 4

if ($_REQUEST['REFRESH_ID']) {

if ($_REQUEST['REFRESH_ID'] == 'Y') {
		$par = array('ENTITY' => 'APL',
			 'NAME' => 'APL');

		$res = restCommand('entity.get', $par, $token, $domain);
		if ($res['error'] == 'ERROR_ENTITY_NOT_FOUND') {
			$par = array('ENTITY' => 'APL',
						 'NAME' => 'APL');
			$res = restCommand('entity.add', $par, $token, $domain);}
	//echo '<pre>33 ' . print_r($res, true) . '</pre>';
$par = array('ENTITY' => 'APL', 'FILTER' => array ('NAME' => 'SETTING')); $res = restCommand('entity.item.get', $par, $token, $domain);	
	//echo '<pre>35 ' . print_r($res, true) . '</pre>';
if ($res['total'] == 0) {$par = array('ENTITY' => 'APL', 'NAME' => 'SETTING');	$res = restCommand('entity.item.add', $par, $token, $domain);}
	//echo '<pre>37 ' . print_r($res, true) . '</pre>';
$par = array('ENTITY' => 'APL', 'FILTER' => array ('NAME' => 'SETTING')); $res = restCommand('entity.item.get', $par, $token, $domain); $set_id = $res['result'][0]['ID'];
$par = array('ENTITY' => 'APL', 'ID' => $set_id, 'CODE' => $_REQUEST['inp'], 'PREVIEW_TEXT' => $_REQUEST['cfg'], 'DETAIL_TEXT' => $_REQUEST['ncfg']);	$res = restCommand('entity.item.update', $par, $token, $domain);	

}
$par = array('ENTITY' => 'APL', 'FILTER' => array ('NAME' => 'SETTING')); $res = restCommand('entity.item.get', $par, $token, $domain);	
$setting = $res['result'][0];
$cfg = $setting['PREVIEW_TEXT'];
$ncfg = $setting['DETAIL_TEXT'];
$inp = $setting['CODE'];

?>
<html lang="en">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    
    <title>Настройки</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<link href='https://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
	<script src="//api.bitrix24.com/api/v1/"></script>
	<link href="js/messagebox.css" rel="stylesheet">
	<script src="js/messagebox.js"></script>
	<link rel="stylesheet" type="text/css" href="style.css">

</head>
<body>
<script>
$(function() {
    $("img").each(function(b) {//работа с элементом (ссылка)
        if (this.title) {
            var c = this.title;
            var x = 0;//расположение по горизонтали(left)
            var y = 35;//расположение по вертикали (top)
            $(this).mouseover(function(d) {
                this.title = "";
                $("body").append('<div id="tooltip">' + c + "</div>");
                $("#tooltip").css({
                    left: (d.pageX + x) + "px",
                    top: (d.pageY + y) + "px"//,
                    //opacity: "0.8"//полупрозрачность
                }).show(300)//скорость появления подсказки
            }).mouseout(function() {
                this.title = c;
                $("#tooltip").remove()
            }).mousemove(function(d) {
                $("#tooltip").css({
                    left: (d.pageX + x) + "px",
                    top: (d.pageY + y) + "px"
                })
            })
        }
    })
    });
</script>


<table style="width:100%;  margin: 1px;">
	<tr style="height: 40px;">
		<td style="width: 2%; background: white;"></td>
		<td class = "item" align = "center" style="border-bottom: 2px solid #1058d0; text-align: center; background: #ffffff;">
		<font style="padding: 20px; white-space: nowrap; color: #1058d0;">Настройки приложения</font>
		</td>
		<td style="width: 70%; background: white;"></td>
	</tr>
</table>

<form id="formId">
<?
	//echo '<pre>set ' . print_r($setting, true) . '</pre>';
?>
	<input type="hidden" id="cfg" name="cfg" value="<?echo $cfg;?>">
	<input type="hidden" id="ncfg" name="ncfg" value="<?echo $ncfg;?>">
<input type="hidden" id="REFRESH_ID" name="REFRESH_ID" value="Y">
<br><br>
<table style="width: 100%;">	
	<tr>	    
		<td style="width: 5%;"></td>
		<td colspan="16" style="font-size:15px; color: #7C7A7F"><b>Выбор сотрудников</b><br style="font-size:7px;"><br style="font-size:7px;"></td>
	</tr>

<tr>
	<td style="width: 5%;"></td>
	<td style="width: 30%;font-size:13px; color: #7C7A7F">Выберите сотрудников по которым осуществляется поиск</td>
	<td style=""><div id="divu" style="display: table; height: 30px; padding: 5px; border: 1px solid #c6cdd3; white-space: nowrap;">
	<?
	$cfg = explode('~', $cfg);array_shift($cfg);
	$ncfg = explode('~', $ncfg);array_shift($ncfg);

	foreach ($cfg as $key => $cf)
	echo '<div id="u'.$cf.'" style="display: table-cell; height: 25px; background-color: #ccf2ff; color: #2067b0; padding: 5px;">'.$ncfg[$key].' <a href="#" onclick="delus(\'u'.$cf.'\');" style="text-decoration: none; font-size: 13px; color: #bbbbbb;">x</a> </div>';
	?>
	<a href="#" style="color: #2067b0;" onclick="addu();">+ Добавить ещё</a></div></td>
	<td style="width: 5%; "><img style="width:20px;" src="img/info.png" alt="info" title="Выберите сотрудников по которым осуществляется поиск"></td>
	<td></td>
</tr>
<tr>
	<td style="width: 5%;"></td>
	<td style="width: 30%;font-size:13px; color: #7C7A7F">Тэг, по которому задачи можно сортировать</td>
	<td style=""><input name="inp" style="height: 30px; padding: 5px; border: 1px solid #c6cdd3;" value="<?echo $inp;?>"></input></td>
	<td style="width: 5%; "><img style="width:20px;" src="img/info.png" alt="info" title="Выберите сотрудников по которым осуществляется поиск"></td>
	<td></td>
</tr>
</table>
<table align = "center" style="width: 100%;">
	<tr>
		<td align = "center"><button style="cursor: pointer; font-weight: 750; border: none; font-size:13px; color: #5A5A5A; width:230px; height: 40px;background: #BCED22" id="save" >СОХРАНИТЬ</button></td>
		
		
		<td align = "center" style="text-align: center; width: 50%;">
		<a href="#" style="font-weight: 750; font-size:13px; color: #CC0000; width:230px; height: 40px;" id="b3" onclick="location.href='help.php?DOMAIN=<?php echo $domain;?>&AUTH_ID=<?php echo $token;?>'">ПОМОЩЬ</a>
		</td>
	</tr>
</table>

<script>
function addu() {
	BX24.selectUser(function(){
   console.log(arguments[0]['id']);
   document.getElementById('divu').innerHTML = '<div id="u'+arguments[0]['id']+'" style="display: table-cell; height: 25px; background-color: #ccf2ff; color: #2067b0; padding: 5px;">' + arguments[0]['name'] + ' <a href="#" onclick="delus(\'u'+arguments[0]['id']+'\', \''+arguments[0]['name']+'\',);" style="text-decoration: none; font-size: 13px; color: #bbbbbb;">x</a> </div>' + document.getElementById('divu').innerHTML;
   document.getElementById('cfg').value = document.getElementById('cfg').value + '~' + arguments[0]['id'];
   document.getElementById('ncfg').value = document.getElementById('ncfg').value + '~' + arguments[0]['name'];

});	
}
function delus(us, usn) {
	usr = us.slice(1)
	document.getElementById(us).remove();
	str = document.getElementById('cfg').value;
	document.getElementById('cfg').value = str.replace('~'+usr, '');
	document.getElementById('ncfg').value = str.replace('~'+usn, '');
	console.log('~'+usr);
     console.log(document.getElementById('cfg'));
}
</script>
</form>

<?	
}
else {
$user = restlist('user.get', array('FILTER'=> array('ACTIVE'=> 'Y')), $token, $domain);
$users = array();
	foreach ($user as $us) {
		//$users[$us['ID']] = $us;
		if ($us['NAME'] or $us['LAST_NAME']) $users[$us['ID']]= trim($us['LAST_NAME'] . ' ' . $us['NAME']); 
			else $users[$us['ID']]= $us['EMAIL'];
		}
	//echo '<pre>users ' . print_r($users, true) . '</pre>';
	//exit;
$par = array('ENTITY' => 'APL', 'FILTER' => array ('NAME' => 'SETTING')); $res = restCommand('entity.item.get', $par, $token, $domain);	
$setting = $res['result'][0];
$cfg = $setting['PREVIEW_TEXT'];
$ncfg = $setting['DETAIL_TEXT'];
$inp = $setting['CODE'];
	//echo '<pre>' . print_r($res, true) . '</pre>';

$asus = explode('~', $cfg); array_shift($asus);
	//echo '<pre>' . print_r($asus, true) . '</pre>';

$datmod=date('Y-m-d',(strtotime(date('Y-m-d'))-7*24*60*60));
$deadline=date('Y-m-d',(strtotime(date('Y-m-d'))+7*24*60*60));
echo $datmod;


	foreach($asus as $us) {
if ($run == 'ALL' or $run == 'ALL1') $par = array('filter' => array(
					'ASSIGNED_BY_ID' => $us,
					'STAGE_SEMANTIC_ID' => 'P',
					'<DATE_MODIFY' => $datmod,
						));

if ($run == 'DAY') $par = array('filter' => array(
					'ASSIGNED_BY_ID' => $us,
					'OPENED' => 'Y',
					'>=DATE_MODIFY' => $datmod . ' 00:00:00',
					'<=DATE_MODIFY' => $datmod . ' 23:59:59',
					'!STAGE_ID' => 4
						));

$dls = restlist('crm.deal.list', $par, $token, $domain);

foreach ($dls as $dl) {$iii++;
	$discription = 'Сделка [url=http://' . $domain . '/crm/deal/details/'.$dl['ID'].'/]'.$dl['TITLE'].'[/URL] не редактировалась более 7-дней. Исправьте';
					   $title = $inp . ' забытая сделка Id'.$dl['ID'] . ' ответственный '.$users[$dl['ASSIGNED_BY_ID']] . ' #'.$dl['ASSIGNED_BY_ID'];
	//$resp = $dl['ASSIGNED_BY_ID'];
	$par = array('fields' => array(
		'DEADLINE' => $deadline,
		'DESCRIPTION' => $discription,
		'TITLE' => $title,
		'RESPONSIBLE_ID' => $resp,
		'UF_CRM_TASK' => array('D_'.$dl['ID'])
		));
		echo '<pre>' .$iii . print_r($par, true) . '</pre>';
					   $res = restCommand('tasks.task.add', $par, $token, $domain);
		if ($run == 'ALL1') exit;

	}
}
}
function restToken(array $params = array()) {
    $queryUrl = 'https://oauth.bitrix.info/oauth/token/';
    $queryData = http_build_query($params);
    $curl = curl_init();
    curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => true,
        CURLOPT_URL => $queryUrl,
        CURLOPT_POSTFIELDS => $queryData
    ));
	
    $result = curl_exec($curl);
    curl_close($curl);

    return json_decode($result, true);
}

function restCommand($method, array $params = array(), $tok, $dom) {
    $queryUrl  = 'http://'.$dom . '/rest/' . $method .'.json';
    $queryData = http_build_query(array_merge($params, array('auth' => $tok)));

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_POST           => 1,
        CURLOPT_HEADER         => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL            => $queryUrl,
        CURLOPT_POSTFIELDS     => $queryData,
    ));
    $result = curl_exec($curl);
    curl_close($curl);
    $result = json_decode($result, 1);
    return $result;
}

function restbatch(array $params = array(), $tok, $dom) {
    $queryUrl  = 'http://'.$dom . '/rest/batch.json';
    $queryData = http_build_query(array_merge($params, array('auth' => $tok)));

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_POST           => 1,
        CURLOPT_HEADER         => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL            => $queryUrl,
        CURLOPT_POSTFIELDS     => $queryData,
    ));
    $result = curl_exec($curl);
    curl_close($curl);
    $result = json_decode($result, 1);
    return $result;
}



function restlist($method, $params, $tok, $dom) {
    
	$res = restCommand($method, $params, $tok, $dom);
	$s=0; $arres = array();
	$total = $res['total'];
	for($x=0; $x<$total; $x=$x+50){
	$par = array('start' => $x);
	$par = array_merge($par, $params);
	$batch['b' . $s++] = $method . '?' . http_build_query($par);
	if (count($batch) == 50) {$ar = restbatch(array('cmd' => $batch), $tok, $dom);

							  $ar = $ar['result']['result'];
							  $arres = array_merge($arres, $ar);
							  $batch = array();
							  
							}
	}
	$ar = restbatch(array('cmd' => $batch), $tok, $dom); $ar = $ar['result']['result'];
	if ($ar) $arres = array_merge($arres, $ar);
	$result = array();
	foreach($arres as $ar)
		$result = array_merge($result, $ar);
    return $result;
}
function cmp($a, $b)
{	$namea = $a['LAST_NAME'] . ' '. $a['NAME'];
	$nameb = $b['LAST_NAME'] . ' '. $b['NAME'];
	if ($a['LAST_NAME'] . ' '. $a['NAME'] == ' ') $namea = $a['EMAIL'];
	if ($b['LAST_NAME'] . ' '. $b['NAME'] == ' ') $nameb = $b['EMAIL'];
    $namea = trim(mb_strtoupper($namea, 'UTF-8'));
	$nameb = trim(mb_strtoupper($nameb, 'UTF-8'));
	return strcmp($namea, $nameb);
}

function cmpd($a, $b)
{	
	$namea = trim(mb_strtoupper($a['NAME'], 'UTF-8'));
	$nameb = trim(mb_strtoupper($b['NAME'], 'UTF-8'));
    return strcmp($namea, $nameb);
}

function echolog($text) {
	$log = date('Y-m-d H:i:s') . ' : ' . $text;
	file_put_contents(__DIR__ . '/log.txt', $log . PHP_EOL, FILE_APPEND);

}	

?>