<?php
//	$arControllerOption = CControllerClient::GetInstalledOptions('kpbs.custom');
//print_r($arControllerOption);

$val = COption::GetOptionString('kpbs.custom', 'main_uf');
print_r($val);