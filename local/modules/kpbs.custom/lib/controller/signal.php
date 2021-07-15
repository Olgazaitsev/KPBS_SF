<?php
namespace kpbs\custom\Controller;

use Bitrix\Main;
use Bitrix\Crm;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Config\Option;
use \Bitrix\Crm\DealTable;
use \Bitrix\Main\Loader;

class Signal extends Controller
{
    public function getSignalAction($user, $year, $curdate)
    {
        $resultstat = [];
        $list = \COption::GetOptionString('kpbs.custom', 'ib_id');
        $KVqweight = \COption::GetOptionString('kpbs.custom', 'w1_val');
        $KVavgweight = \COption::GetOptionString('kpbs.custom', 'w2_val');
        $QualActweight = \COption::GetOptionString('kpbs.custom', 'w3_val');
        $CRMactivityweight = \COption::GetOptionString('kpbs.custom', 'w4_val');
        $CNTNetweight = \COption::GetOptionString('kpbs.custom', 'w5_val');
        $CNTLevweight = \COption::GetOptionString('kpbs.custom', 'w6_val');
        $KVavgmax = \COption::GetOptionString('kpbs.custom', 'm2_val');
        $QualActmax = \COption::GetOptionString('kpbs.custom', 'm3_val')/100;
        $CRMactivitytmax = \COption::GetOptionString('kpbs.custom', 'm4_val')/100;
        $CNTLevmax = \COption::GetOptionString('kpbs.custom', 'm6_val');
        $planf = \COption::GetOptionString('kpbs.custom', 'pl_id');
        $markf = \COption::GetOptionString('kpbs.custom', 'mk_id');
        $maxbf = \COption::GetOptionString('kpbs.custom', 'mb_id');
        $minplf = \COption::GetOptionString('kpbs.custom', 'mp_id');
        $listb = \COption::GetOptionString('kpbs.custom', 'ib_bon_id');
        $listuu = \COption::GetOptionString('kpbs.custom', 'ib_uu_id');
        $quarters = [];
        $quaterbonus = [
            '1' => \COption::GetOptionString('kpbs.custom', 'q1')/100,
            '2' => \COption::GetOptionString('kpbs.custom', 'q2')/100,
            '3' => \COption::GetOptionString('kpbs.custom', 'q3')/100,
            '4' => \COption::GetOptionString('kpbs.custom', 'q4')/100
        ];
        $userslist = explode(',', \COption::GetOptionString('kpbs.custom', 'users_list'));

        // подсчет показателей по текущей дате
        if($curdate) {

            $curkv = intval((date('m', strtotime($curdate)) + 2)/3);
            if($curkv == 2) {
                $quarters = [1];
            } elseif($curkv == 3) {
                $quarters = [1,2];
            } elseif($curkv == 4) {
                $quarters = [1,2,3];
            }


            $to = $curdate;
            if($curkv==1) {
                if($year == 2021) {
                    $from = '15.03.'.$year;
                } else {
                    $from = '01.01.'.$year;
                }
            } else if($curkv==2) {
                $from = '01.04.'.$year;
            } else if($curkv==3) {
                $from = '01.07.'.$year;
            } else if($curkv==4) {
                $from = '01.10.'.$year;
            }

            $from2 = $from;
            $from3 = '01.01.'.$year;

            $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_DATA_POKAZ", "PROPERTY_MENEDZHER", "PROPERTY_ZNACHENIE_POKAZATELYA");
            $arFilter = Array("IBLOCK_ID"=>$list, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y",
                "PROPERTY_MENEDZHER" => $user,
                ">="."PROPERTY_DATA_POKAZ" => date("Y-m-d",strtotime($from)),
                "<="."PROPERTY_DATA_POKAZ" => date("Y-m-d",strtotime($to)),

            );

            $res = \CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
            $transfres = [];

            $arSelect2 = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_DATA_POKAZ", "PROPERTY_MENEDZHER", "PROPERTY_ZNACHENIE_POKAZATELYA");
            $arFilter2 = Array("IBLOCK_ID"=>$list, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y",
                "PROPERTY_MENEDZHER" => $userslist,
                "NAME" => 'CRMactivity',
                ">="."PROPERTY_DATA_POKAZ" => date("Y-m-d",strtotime($from)),
                "<="."PROPERTY_DATA_POKAZ" => date("Y-m-d",strtotime($to)),

            );

            $res2 = \CIBlockElement::GetList(Array(), $arFilter2, false, false, $arSelect2);

            $transfres2 = [];

            while ($arResList = $res->fetch()) {
                $transfres[$arResList['PROPERTY_DATA_POKAZ_VALUE']][$arResList['NAME']] = $arResList['PROPERTY_ZNACHENIE_POKAZATELYA_VALUE'];
            }

            while ($arResList2 = $res2->fetch()) {
                $transfres2[$arResList2['PROPERTY_DATA_POKAZ_VALUE']][$arResList2['PROPERTY_MENEDZHER_VALUE']] = $arResList2['PROPERTY_ZNACHENIE_POKAZATELYA_VALUE'];
            }

            $totime = strtotime($to);

            $countalldays = 0;
            $countmonfri = 0;
            $countsat = 0;
            $KVcurr = 0;
            $KVq = 0;
            $KVqkach = 0;
            $KVqrate = 0;
            $KVavg = 0;
            $KVavgkach = 0;
            $KVavgrate = 0;
            $QualAct = 0;
            $QualActkach = 0;
            $QualActrate = 0;
            $CRMactivity = 0;
            $CRMactivitykach = 0;
            $CRMactivityrate = 0;
            $CNTNet = 0;
            $CNTNetkach = 0;
            $CNTNetrate = 0;
            $CNTLev = 0;
            $CNTLevkach = 0;
            $CNTLevrate = 0;
            $totalbonustopay = 0;

            do {
                if($from == $from2) {
                    if($transfres[$from]['KVq']) {
                        $KVq = $transfres[$from]['KVq'];
                    }
                }
                $countalldays++;
                $weekday = date('w', strtotime($from));
                if($transfres[$from]['KVcurr']) {
                    $KVcurr += $transfres[$from]['KVcurr'];
                }

                if($transfres[$from]['KVavg']) {
                    $KVavg += $transfres[$from]['KVavg'];
                }

                if($transfres[$from]['CNTLev']) {
                    $CNTLev += $transfres[$from]['CNTLev'];
                }

                if($transfres[$from]['CNTNet']) {
                    $CNTNet += $transfres[$from]['CNTNet'];
                }

                if($weekday == 2 || $weekday == 5) {
                    $countmonfri++;
                    if($transfres[$from]['QualAct']) {
                        $QualAct += $transfres[$from]['QualAct'];
                    }
                } elseif ($weekday == 6) {
                    $countsat++;
                    if($transfres2[$from][$user]) {
                        $lim = max($transfres2[$from]);
                        $perclim = round($transfres2[$from][$user]/$lim,2);
                        $CRMactivity += $perclim;
                    }
                }

                $fromtime = strtotime('+1 day', strtotime($from));
                $from = date('d.m.Y', $fromtime);

            } while($fromtime <= $totime);

            $KVcurr = round($KVcurr/$countalldays,2);
            if($KVq!=0) {
                $KVq = (round($KVcurr/$KVq,2)-1)*100;
            }
            if($KVq<=10) {
                $KVqkach = 0;
            } elseif ($KVq>10 && $KVq<15) {
                $KVqkach = 0.5;
            } else {
                $KVqkach = 1;
            }
            $KVqrate = round($KVqweight*$KVqkach,2);
            $KVavg = round($KVavg/$countalldays,2);
            if($KVavg<=20) {
                $KVavgkach = 0;
            } elseif ($KVavg>20 && $KVavg<40) {
                $KVavgkach = 0.5;
            } else {
                $KVavgkach = 1;
            }
            $KVavgrate = round(($KVavg/$KVavgmax)*($KVavgweight*$KVavgkach),2);
            $QualAct = round($QualAct/$countmonfri,2);
            if($QualAct<=0.75) {
                $QualActkach = 0;
            } elseif ($QualAct>0.75 && $QualAct<0.95) {
                $QualActkach = 0.5;
            } else {
                $QualActkach = 1;
            }
            $QualActrate = round(($QualAct/$QualActmax)*($QualActweight*$QualActkach),2);
            $CNTLev = round($CNTLev/$countalldays,2);
            $CRMactivity = round($CRMactivity/$countsat,2);
            if($CRMactivity<=0.5) {
                $CRMactivitykach = 0;
            } elseif ($CRMactivity>0.5 && $CRMactivity<0.8) {
                $CRMactivitykach = 0.5;
            } else {
                $CRMactivitykach = 1;
            }
            $CRMactivityrate = round(($CRMactivity/$CRMactivitytmax)*($CRMactivityweight*$CRMactivitykach),2);

            $CNTNet = round($CNTNet/$countalldays,2);
            if($CNTNet<=1) {
                $CNTNetkach = 0;
            } elseif ($CNTNet>1 && $CNTNet<3) {
                $CNTNetkach = 0.5;
            } else {
                $CNTNetkach = 1;
            }
            $CNTNetrate = $CNTNetweight*$CNTNetkach;
            if($CNTLev<=1) {
                $CNTLevkach = 0;
            } elseif ($CNTLev>1 && $CNTLev<2) {
                $CNTLevkach = 0.5;
            } else {
                $CNTLevkach = 1;
            }
            $CNTLevrate = round(($CNTLev/$CNTLevmax)*($CNTLevweight*$CNTLevkach),2);

            $totalkach = ($KVqkach+$KVavgkach+$QualActkach+$CRMactivitykach+$CNTNetkach+$CNTLevkach)/6;
            if($totalkach<0.25) {
                $totalkach = 0;
            } else if($totalkach>=0.25 && $totalkach<0.75) {
                $totalkach = 0.5;
            } else {
                $totalkach = 1;
            }

            $totalpoints = round($KVqrate+$KVavgrate+$QualActrate+$CRMactivityrate+$CNTNetrate+$CNTLevrate,2);

            $resultstat['Q'] = $quarters;
            $resultstat['X1']['c']['value']=$KVq;
            $resultstat['X1']['c']['weight']=$KVqweight;
            $resultstat['X1']['c']['rate']=$KVqrate."%";
            $resultstat['X1']['c']['kach']=$KVqkach;
            $resultstat['X2']['c']['value']=$KVavg;
            $resultstat['X2']['c']['weight']=$KVavgweight;
            $resultstat['X2']['c']['rate']=$KVavgrate.'%';
            $resultstat['X2']['c']['kach']=$KVavgkach;
            $resultstat['X3']['c']['value']=$QualAct;
            $resultstat['X3']['c']['weight']=$QualActweight;
            $resultstat['X3']['c']['rate']=$QualActrate.'%';
            $resultstat['X3']['c']['kach']=$QualActkach;
            $resultstat['X4']['c']['value']=$CRMactivity;
            $resultstat['X4']['c']['weight']=$CRMactivityweight;
            $resultstat['X4']['c']['rate']=$CRMactivityrate.'%';
            $resultstat['X4']['c']['kach']=$CRMactivitykach;
            $resultstat['X5']['c']['value']=$CNTNet;
            $resultstat['X5']['c']['weight']=$CNTNetweight;
            $resultstat['X5']['c']['rate']=$CNTNetrate.'%';
            $resultstat['X5']['c']['kach']=$CNTNetkach;
            $resultstat['X6']['c']['value']=$CNTLev;
            $resultstat['X6']['c']['weight']=$CNTLevweight;
            $resultstat['X6']['c']['rate']=$CNTLevrate.'%';
            $resultstat['X6']['c']['kach']=$CNTLevkach;
            $resultstat['X_ALL']['c']['kach']=$totalkach;
            $resultstat['X_ALL']['c']['rate']=$totalpoints.'%';

            Loader::includeModule('crm');
            Loader::includeModule('iblock');

            $rsUser = \CUser::GetByID($user);
            $arUser = $rsUser->Fetch();

            $deals = DealTable::getList([
                'filter' => [
                    'ASSIGNED_BY_ID'=> $user, 'CLOSED'=>'Y', '>=CLOSEDATE'=>$from2, '<=CLOSEDATE'=>$to, 'STAGE_ID'=>'WON'
                ],
                'select' => [
                    'ID'
                ]
            ]);

            $totalmargin = 0;
            $bonuspaid = 0;

            $pattern = '/[^0-9]/';

            while ($arResDeals = $deals->fetch()) {


                $dealid = $arResDeals['ID'];
                $arSelect = Array("ID", "PROPERTY_SDELKA", "PROPERTY_MARZHA_FAKTICHESKAYA");
                $arFilter = Array("IBLOCK_ID"=>$listuu, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y",
                    "PROPERTY_SDELKA" => $dealid
                );
                $res = \CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect)->fetch();
                $totalmargin += preg_replace($pattern, "", $res['PROPERTY_MARZHA_FAKTICHESKAYA_VALUE']);

            }

            $arSelect = Array("ID", "PROPERTY_SDELKA", "PROPERTY_SUMMA_VYPLATY");
            $arFilter = Array("IBLOCK_ID"=>$listb, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y",
                "PROPERTY_SOTRUDNIK" => $user,
                ">="."PROPERTY_DATA_VYPLATY" => date("Y-m-d",strtotime($from3)),
                "<="."PROPERTY_DATA_VYPLATY" => date("Y-m-d",strtotime($to))
            );
            $res = \CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
            while ($arResDeals = $res->fetch()) {
                $bonuspaid += $arResDeals['PROPERTY_SUMMA_VYPLATY_VALUE'];
            }


            if($arUser[$planf]>0) {
                $planperc = $totalmargin/$arUser[$planf]*100;

                if($planperc>=$arUser[$minplf]) {
                    $bonusbase = round($totalmargin*$arUser[$markf],0);
                    $bonustopay = round($bonusbase*($totalpoints/100)*($arUser[$maxbf]/100),0);
                }
            }

            $resultstat['X_BONUS1']['c']['kach'] = 1;
            $resultstat['X_BONUS1']['c']['rate'] = $arUser[$planf].'p.';
            $resultstat['X_BONUS2']['c']['kach'] = 1;
            $resultstat['X_BONUS2']['c']['rate'] = $totalmargin.'p.='.$planperc.'% от плана';
            $resultstat['X_BONUS3']['c']['kach'] = 1;
            $resultstat['X_BONUS3']['c']['rate'] = $bonusbase.'p.';
            $resultstat['X_BONUS4']['c']['kach'] = 1;
            $resultstat['X_BONUS4']['c']['rate'] = $totalpoints.'%';
            $resultstat['X_BONUS5']['c']['kach'] = 1;
            $resultstat['X_BONUS5']['c']['rate'] = $bonuspaid.'p.';
            $resultstat['X_BONUS6']['c']['kach'] = 1;
            $resultstat['X_BONUS6']['c']['rate'] = $bonustopay.'p.';
            $totalbonustopay = $bonustopay;
        }

        // подсчет показателей по кварталами
        foreach ($quarters as $quater) {
            if($quater==1) {
                if($year == 2021) {
                    $from = '15.03.'.$year;
                } else {
                    $from = '01.01.'.$year;
                }
                $to = '31.03.'.$year;
            } else if($quater==2) {
                $from = '01.04.'.$year;
                $to = '30.06.'.$year;
            } else if($quater==3) {
                $from = '01.07.'.$year;
                $to = '30.09.'.$year;
            } else if($quater==4) {
                $from = '01.10.'.$year;
                $to = '31.12.'.$year;
            }

            $from2 = $from;

            $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_DATA_POKAZ", "PROPERTY_MENEDZHER", "PROPERTY_ZNACHENIE_POKAZATELYA");
            $arFilter = Array("IBLOCK_ID"=>$list, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y",
                "PROPERTY_MENEDZHER" => $user,
                ">="."PROPERTY_DATA_POKAZ" => date("Y-m-d",strtotime($from)),
                "<="."PROPERTY_DATA_POKAZ" => date("Y-m-d",strtotime($to)),

            );

            $res = \CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
            $transfres = [];

            $arSelect2 = Array("ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_DATA_POKAZ", "PROPERTY_MENEDZHER", "PROPERTY_ZNACHENIE_POKAZATELYA");
            $arFilter2 = Array("IBLOCK_ID"=>$list, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y",
                "PROPERTY_MENEDZHER" => $userslist,
                "NAME" => 'CRMactivity',
                ">="."PROPERTY_DATA_POKAZ" => date("Y-m-d",strtotime($from)),
                "<="."PROPERTY_DATA_POKAZ" => date("Y-m-d",strtotime($to)),

            );

            $res2 = \CIBlockElement::GetList(Array(), $arFilter2, false, false, $arSelect2);

            $transfres2 = [];

            while ($arResList = $res->fetch()) {
                $transfres[$arResList['PROPERTY_DATA_POKAZ_VALUE']][$arResList['NAME']] = $arResList['PROPERTY_ZNACHENIE_POKAZATELYA_VALUE'];
            }

            while ($arResList2 = $res2->fetch()) {
                $transfres2[$arResList2['PROPERTY_DATA_POKAZ_VALUE']][$arResList2['PROPERTY_MENEDZHER_VALUE']] = $arResList2['PROPERTY_ZNACHENIE_POKAZATELYA_VALUE'];
            }

            $totime = strtotime($to);

            $countalldays = 0;
            $countmonfri = 0;
            $countsat = 0;
            $KVcurr = 0;
            $KVq = 0;
            $KVqkach = 0;
            $KVqrate = 0;
            $KVavg = 0;
            $KVavgkach = 0;
            $KVavgrate = 0;
            $QualAct = 0;
            $QualActkach = 0;
            $QualActrate = 0;
            $CRMactivity = 0;
            $CRMactivitykach = 0;
            $CRMactivityrate = 0;
            $CNTNet = 0;
            $CNTNetkach = 0;
            $CNTNetrate = 0;
            $CNTLev = 0;
            $CNTLevkach = 0;
            $CNTLevrate = 0;

            do {
                if($from == $from2) {
                    if($transfres[$from]['KVq']) {
                        $KVq = $transfres[$from]['KVq'];
                    }
                }
                $countalldays++;
                $weekday = date('w', strtotime($from));
                if($transfres[$from]['KVcurr']) {
                    $KVcurr += $transfres[$from]['KVcurr'];
                }

                if($transfres[$from]['KVavg']) {
                    $KVavg += $transfres[$from]['KVavg'];
                }

                if($transfres[$from]['CNTLev']) {
                    $CNTLev += $transfres[$from]['CNTLev'];
                }

                if($transfres[$from]['CNTNet']) {
                    $CNTNet += $transfres[$from]['CNTNet'];
                }

                if($weekday == 2 || $weekday == 5) {
                    $countmonfri++;
                    if($transfres[$from]['QualAct']) {
                        $QualAct += $transfres[$from]['QualAct'];
                    }
                } elseif ($weekday == 6) {
                    $countsat++;
                    if($transfres2[$from][$user]) {
                        $lim = max($transfres2[$from]);
                        $perclim = round($transfres2[$from][$user]/$lim,2);
                        $CRMactivity += $perclim;
                    }
                }

                $fromtime = strtotime('+1 day', strtotime($from));
                $from = date('d.m.Y', $fromtime);

            } while($fromtime <= $totime);

            $KVcurr = round($KVcurr/$countalldays,2);
            if($KVq!=0) {
                $KVq = (round($KVcurr/$KVq,2)-1)*100;
            }
            if($KVq<=10) {
                $KVqkach = 0;
            } elseif ($KVq>10 && $KVq<15) {
                $KVqkach = 0.5;
            } else {
                $KVqkach = 1;
            }
            $KVqrate = round($KVqweight*$KVqkach,2);
            $KVavg = round($KVavg/$countalldays,2);
            if($KVavg<=20) {
                $KVavgkach = 0;
            } elseif ($KVavg>20 && $KVavg<40) {
                $KVavgkach = 0.5;
            } else {
                $KVavgkach = 1;
            }
            $KVavgrate = round(($KVavg/$KVavgmax)*($KVavgweight*$KVavgkach),2);
            $QualAct = round($QualAct/$countmonfri,2);
            if($QualAct<=0.75) {
                $QualActkach = 0;
            } elseif ($QualAct>0.75 && $QualAct<0.95) {
                $QualActkach = 0.5;
            } else {
                $QualActkach = 1;
            }
            $QualActrate = round(($QualAct/$QualActmax)*($QualActweight*$QualActkach),2);
            $CNTLev = round($CNTLev/$countalldays,2);
            $CRMactivity = round($CRMactivity/$countsat,2);
            if($CRMactivity<=0.5) {
                $CRMactivitykach = 0;
            } elseif ($CRMactivity>0.5 && $CRMactivity<0.8) {
                $CRMactivitykach = 0.5;
            } else {
                $CRMactivitykach = 1;
            }
            $CRMactivityrate = round(($CRMactivity/$CRMactivitytmax)*($CRMactivityweight*$CRMactivitykach),2);

            $CNTNet = round($CNTNet/$countalldays,2);
            if($CNTNet<=1) {
                $CNTNetkach = 0;
            } elseif ($CNTNet>1 && $CNTNet<3) {
                $CNTNetkach = 0.5;
            } else {
                $CNTNetkach = 1;
            }
            $CNTNetrate = $CNTNetweight*$CNTNetkach;
            if($CNTLev<=1) {
                $CNTLevkach = 0;
            } elseif ($CNTLev>1 && $CNTLev<2) {
                $CNTLevkach = 0.5;
            } else {
                $CNTLevkach = 1;
            }
            $CNTLevrate = round(($CNTLev/$CNTLevmax)*($CNTLevweight*$CNTLevkach),2);

            $totalkach = ($KVqkach+$KVavgkach+$QualActkach+$CRMactivitykach+$CNTNetkach+$CNTLevkach)/6;
            if($totalkach<0.25) {
                $totalkach = 0;
            } else if($totalkach>=0.25 && $totalkach<0.75) {
                $totalkach = 0.5;
            } else {
                $totalkach = 1;
            }

            $resultstat['X1'][$quater]['value']=$KVq;
            $resultstat['X1'][$quater]['weight']=$KVqweight;
            $resultstat['X1'][$quater]['rate']=$KVqrate.'%';
            $resultstat['X1'][$quater]['kach']=$KVqkach;
            $resultstat['X2'][$quater]['value']=$KVavg;
            $resultstat['X2'][$quater]['weight']=$KVavgweight;
            $resultstat['X2'][$quater]['rate']=$KVavgrate.'%';
            $resultstat['X2'][$quater]['kach']=$KVavgkach;
            $resultstat['X3'][$quater]['value']=$QualAct;
            $resultstat['X3'][$quater]['weight']=$QualActweight;
            $resultstat['X3'][$quater]['rate']=$QualActrate.'%';
            $resultstat['X3'][$quater]['kach']=$QualActkach;
            $resultstat['X4'][$quater]['value']=$CRMactivity;
            $resultstat['X4'][$quater]['weight']=$CRMactivityweight;
            $resultstat['X4'][$quater]['rate']=$CRMactivityrate.'%';
            $resultstat['X4'][$quater]['kach']=$CRMactivitykach;
            $resultstat['X5'][$quater]['value']=$CNTNet;
            $resultstat['X5'][$quater]['weight']=$CNTNetweight;
            $resultstat['X5'][$quater]['rate']=$CNTNetrate.'%';
            $resultstat['X5'][$quater]['kach']=$CNTNetkach;
            $resultstat['X6'][$quater]['value']=$CNTLev;
            $resultstat['X6'][$quater]['weight']=$CNTLevweight;
            $resultstat['X6'][$quater]['rate']=$CNTLevrate.'%';
            $resultstat['X6'][$quater]['kach']=$CNTLevkach;
            $resultstat['X_ALL'][$quater]['kach']=$totalkach;
            $resultstat['X_ALL'][$quater]['rate']=round($KVqrate+$KVavgrate+$QualActrate+$CRMactivityrate+$CNTNetrate+$CNTLevrate,2).'%';

            $totalpoints = round($KVqrate+$KVavgrate+$QualActrate+$CRMactivityrate+$CNTNetrate+$CNTLevrate,2);

            $deals = DealTable::getList([
                'filter' => [
                    'ASSIGNED_BY_ID'=> $user, 'CLOSED'=>'Y', '>=CLOSEDATE'=>$from2, '<=CLOSEDATE'=>$to, 'STAGE_ID'=>'WON'
                ],
                'select' => [
                    'ID'
                ]
            ]);

            $totalmargin = 0;
            $bonustopay = 0;

            $pattern = '/[^0-9]/';

            while ($arResDeals = $deals->fetch()) {
                $dealid = $arResDeals['ID'];
                $arSelect = Array("ID", "PROPERTY_SDELKA", "PROPERTY_MARZHA_FAKTICHESKAYA");
                $arFilter = Array("IBLOCK_ID"=>$listuu, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y",
                    "PROPERTY_SDELKA" => $dealid
                );
                $res = \CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect)->fetch();
                $totalmargin += preg_replace($pattern, "", $res['PROPERTY_MARZHA_FAKTICHESKAYA_VALUE']);

            }

            if($arUser[$planf]>0) {
                $planperc = $totalmargin/$arUser[$planf]*100;

                if($planperc>=$arUser[$minplf]) {
                    $bonusbase = $totalmargin*$arUser[$markf];
                    $bonustopay = $bonusbase*($totalpoints/100)*($arUser[$maxbf]/100);
                }
            }
            $totalbonustopay += $bonustopay;
        }

        $totalbonustopay = $totalbonustopay*$quaterbonus[$curkv];

        if($totalbonustopay>$bonuspaid) {
            $resultstat['X_BONUS7']['c']['kach'] = 1;
            $resultstat['X_BONUS7']['c']['rate'] = round(($totalbonustopay-$bonuspaid),0)."p.";
        } else {
            $resultstat['X_BONUS7']['c']['kach'] = 1;
            $resultstat['X_BONUS7']['c']['rate'] = 0;
        }


        return $resultstat;
    }
}

