<?php


namespace kpbs\custom;

use Bitrix\Main\Config\Option;

class filldatawarehouse
{
    public static function executefilling()
    {
        \Bitrix\Main\Diag\Debug::writeToFile(date("Y.m.d G:i:s") ."agent", "agent", "__miros.log");
        \Bitrix\Main\Loader::includeModule('crm');

        $kbuf = \COption::GetOptionString('kpbs.custom', 'kb_id');
        $cnuf = \COption::GetOptionString('kpbs.custom', 'cn_id');
        $cluf = \COption::GetOptionString('kpbs.custom', 'cl_id');
        $list = \COption::GetOptionString('kpbs.custom', 'ib_id');

        $curdate = date("d.m.Y");
        $curmonthdate = date("d.m");
        $curkv = intval((date('m')+2)/3);
        $curyear = date('Y');
        $curweekday = date("w");
        $curweek = date("W");
        $terminatedate = date("d.m.Y", strtotime('31.12.2020'));

        echo $list;
        echo $curmonthdate;

        $rsUser = \CUser::GetList(($by="ID"), ($order="desc"), array("SELECT"=>array("ID")));

        while ($arResUser = $rsUser->Fetch()) {
            $kvavg = 0;
            $kvcurr = 0;
            $kvopen = 0;
            $countdeals = 0;
            $countmoddeals = 0;
            $countopen = 0;
            // анализ сделок
            $arResDeals = array();

            $arFilter = array('ASSIGNED_BY_ID'=> $arResUser['ID'], 'CLOSED'=>'Y', '>CLOSEDATE'=>$terminatedate);
            $arSelect = array('ID', 'ASSIGNED_BY_ID', 'CLOSED', 'CLOSEDATE', 'DATE_MODIFY',  $kbuf);
            $obResDeal = \CCrmDeal::GetListEx(false,$arFilter,false,false,$arSelect);
            while ($arResDealfirst = $obResDeal->Fetch()) {
                array_push($arResDeals, $arResDealfirst);
            }

            $arFilter = array('ASSIGNED_BY_ID'=> $arResUser['ID'], 'CLOSED'=>'N');
            $arSelect = array('ID', 'ASSIGNED_BY_ID', 'CLOSED', 'CLOSEDATE', 'DATE_MODIFY',  $kbuf);
            $obResDeal = \CCrmDeal::GetListEx(false,$arFilter,false,false,$arSelect);
            while ($arResDealsecond = $obResDeal->Fetch()) {
                array_push($arResDeals, $arResDealsecond);
            }

            foreach ($arResDeals as $arResDeal) {
                if($arResDeal['CLOSED']!='Y') {
                    $kvcurr += $arResDeal[$kbuf];
                    $kvopen += $arResDeal[$kbuf];
                    $countdeals++;
                    $countopen++;
                } else {
                    $kv = intval((date('m', strtotime($arResDeal['CLOSEDATE'])) + 2)/3);
                    $year = date("Y.", strtotime($arResDeal['CLOSEDATE']));
                    if ($year ==$curyear && $kv==$curkv) {
                        $kvcurr += $arResDeal[$kbuf];
                        $countdeals++;
                    }
                }

                if($curweekday == 5) {
                    $compdate = strtotime('-3 days');
                    if(strtotime($arResDeal['DATE_MODIFY']) > $compdate) {
                        $countmoddeals++;
                    }
                } elseif($curweekday == 2) {
                    $compdate = strtotime('-4 days');
                    if(strtotime($arResDeal['DATE_MODIFY']) > $compdate) {
                        $countmoddeals++;
                    }
                }
                /*echo "<pre>";
                print_r($arResDeal);
                echo "</pre>";*/
            }
            $kvavg = round($kvcurr / $countdeals,2);


            // анализ компаний
            $cntnetwork = 0;
            $cntlevel = 0;
            $countcnt = 0;
            $arFilter = array('ASSIGNED_BY_ID'=> $arResUser['ID']);
            $arSelect = array('ID', 'ASSIGNED_BY_ID', $cnuf, $cluf);
            $obResCompany = \CCrmCompany::GetListEx(false,$arFilter,false,false,$arSelect);
            while ($arResCompany = $obResCompany->Fetch()) {
                $countcnt++;
                $cntnetwork += $arResCompany[$cnuf];
                $cntlevel += $arResCompany[$cluf];
            }

            // заполнение показателей
            $add = new \CIBlockElement();
            if($countdeals > 0) {
                /*echo "<pre>";
                print_r($arResUser['ID']);
                echo "</pre>";*/
                $data = [
                    'IBLOCK_ID' => $list,
                    'ACTIVE' => 'Y',
                    'NAME' => 'KVcurr',
                    'PROPERTY_VALUES' => [
                        'DATA_POKAZ'=> $curdate,
                        'MENEDZHER'=> $arResUser['ID'],
                        'ZNACHENIE_POKAZATELYA'=> $kvcurr
                    ]
                ];

                $id = $add->Add($data);

                $data = [
                    'IBLOCK_ID' => $list,
                    'ACTIVE' => 'Y',
                    'NAME' => 'KVavg',
                    'PROPERTY_VALUES' => [
                        'DATA_POKAZ'=> $curdate,
                        'MENEDZHER'=> $arResUser['ID'],
                        'ZNACHENIE_POKAZATELYA'=> $kvavg
                    ]
                ];

                $id = $add->Add($data);

                if($curmonthdate == '01.01' || $curmonthdate == '01.04' || $curmonthdate == '01.07' ||
                    $curmonthdate == '01.10') {
                    $data = [
                        'IBLOCK_ID' => $list,
                        'ACTIVE' => 'Y',
                        'NAME' => 'KVq',
                        'PROPERTY_VALUES' => [
                            'DATA_POKAZ'=> $curdate,
                            'MENEDZHER'=> $arResUser['ID'],
                            'ZNACHENIE_POKAZATELYA'=> $kvopen
                        ]
                    ];

                    $id = $add->Add($data);
                }
                /*echo "<pre>";
                print_r($countopen);
                echo "</pre>";
                echo "<pre>";
                print_r($countmoddeals);
                echo "</pre>";
                echo "<pre>";
                print_r($kvopen);
                echo "</pre>";
                echo "<pre>";
                print_r($kvcurr);
                echo "</pre>";
                echo "<pre>";
                print_r($kvavg);
                echo "</pre>";*/
            }
            if ($countopen>0 && $countmoddeals>0) {
                $qualact = round($countmoddeals / $countopen,2);
                $data = [
                    'IBLOCK_ID' => $list,
                    'ACTIVE' => 'Y',
                    'NAME' => 'QualAct',
                    'PROPERTY_VALUES' => [
                        'DATA_POKAZ'=> $curdate,
                        'MENEDZHER'=> $arResUser['ID'],
                        'ZNACHENIE_POKAZATELYA'=> $qualact
                    ]
                ];
                $id = $add->Add($data);
            }

            if ($countcnt>0) {
                $cntlevelavg = round($cntlevel / $countcnt,2);
                $cntnetwavg = round($cntnetwork / $countcnt,2);
                $data = [
                    'IBLOCK_ID' => $list,
                    'ACTIVE' => 'Y',
                    'NAME' => 'CNTLev',
                    'PROPERTY_VALUES' => [
                        'DATA_POKAZ'=> $curdate,
                        'MENEDZHER'=> $arResUser['ID'],
                        'ZNACHENIE_POKAZATELYA'=> $cntlevelavg
                    ]
                ];
                $id = $add->Add($data);

                $data = [
                    'IBLOCK_ID' => $list,
                    'ACTIVE' => 'Y',
                    'NAME' => 'CNTNet',
                    'PROPERTY_VALUES' => [
                        'DATA_POKAZ'=> $curdate,
                        'MENEDZHER'=> $arResUser['ID'],
                        'ZNACHENIE_POKAZATELYA'=> $cntnetwavg
                    ]
                ];
                $id = $add->Add($data);
            }

        }
        return 'kpbs\custom\filldatawarehouse::executefilling();';
    }
}