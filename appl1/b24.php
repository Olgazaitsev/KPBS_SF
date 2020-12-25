<?
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
    $queryUrl  = 'https://'.$dom . '/rest/' . $method .'.json';
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
    $queryUrl  = 'https://'.$dom . '/rest/batch.json';
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