<?php
namespace kpbs\custom\Controller;

use Bitrix\Main;
use Bitrix\Crm;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Config\Option;

class Signal extends Controller
{
    public function getSignalAction($user, $year, $quarters, $curdate)
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
        $QualActmax = \COption::GetOptionString('kpbs.custom', 'm3_val');
        $CRMactivitytmax = \COption::GetOptionString('kpbs.custom', 'm4_val');
        $CNTLevmax = \COption::GetOptionString('kpbs.custom', 'm6_val');

        // подсчет показателей по текущей дате
        if($curdate) {
            $curyear = date("Y.", $curdate);
            $curkv = intval((date('m', strtotime($curdate)) + 2)/3);
            $to = $curdate;
            if($curkv==1) {
                if($curyear == 2021) {
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
                $KVq = round($KVcurr/$KVq,2);
            }
            if($KVq<=10) {
                $KVqkach = 0;
            } elseif ($KVq>10 && $KVq<15) {
                $KVqkach = 0.5;
            } else {
                $KVqkach = 1;
            }
            $KVqrate = $KVqweight*$KVqkach;
            $KVavg = round($KVavg/$countalldays,2);
            if($KVavg<=20) {
                $KVavgkach = 0;
            } elseif ($KVavg>20 && $KVavg<40) {
                $KVavgkach = 0.5;
            } else {
                $KVavgkach = 1;
            }
            $KVavgrate = ($KVavg/$KVavgmax)*($KVavgweight*$KVavgkach);
            $QualAct = round($QualAct/$countmonfri,2);
            if($QualAct<=0.75) {
                $QualActkach = 0;
            } elseif ($QualAct>0.75 && $QualAct<0.95) {
                $QualActkach = 0.5;
            } else {
                $QualActkach = 1;
            }
            $QualActrate = ($QualAct/$QualActmax)*($QualActweight*$QualActkach);
            $CNTLev = round($CNTLev/$countalldays,2);
            $CRMactivity = round($CRMactivity/$countsat,2);
            if($CRMactivity<=0.5) {
                $CRMactivitykach = 0;
            } elseif ($CRMactivity>0.5 && $CRMactivity<0.8) {
                $CRMactivitykach = 0.5;
            } else {
                $CRMactivitykach = 1;
            }
            $CRMactivityrate = ($CRMactivity/$CRMactivitytmax)*($CRMactivityweight*$CRMactivitykach);

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
            $CNTLevrate = ($CNTLev/$CNTLevmax)*$CNTLevweight*$CNTLevkach;

            $totalkach = ($KVqkach+$KVavgkach+$QualActkach+$CRMactivitykach+$CNTNetkach+$CNTLevkach)/6;
            if($totalkach<0.25) {
                $totalkach = 0;
            } else if($totalkach>=0.25 && $totalkach<0.75) {
                $totalkach = 0.5;
            } else {
                $totalkach = 1;
            }

            $resultstat['X1']['c']['value']=$KVq;
            $resultstat['X1']['c']['weight']=$KVqweight;
            $resultstat['X1']['c']['rate']=$KVqrate;
            $resultstat['X1']['c']['kach']=$KVqkach;
            $resultstat['X2']['c']['value']=$KVavg;
            $resultstat['X2']['c']['weight']=$KVavgweight;
            $resultstat['X2']['c']['rate']=$KVavgrate;
            $resultstat['X2']['c']['kach']=$KVavgkach;
            $resultstat['X3']['c']['value']=$QualAct;
            $resultstat['X3']['c']['weight']=$QualActweight;
            $resultstat['X3']['c']['rate']=$QualActrate;
            $resultstat['X3']['c']['kach']=$QualActkach;
            $resultstat['X4']['c']['value']=$CRMactivity;
            $resultstat['X4']['c']['weight']=$CRMactivityweight;
            $resultstat['X4']['c']['rate']=$CRMactivityrate;
            $resultstat['X4']['c']['kach']=$CRMactivitykach;
            $resultstat['X5']['c']['value']=$CNTNet;
            $resultstat['X5']['c']['weight']=$CNTNetweight;
            $resultstat['X5']['c']['rate']=$CNTNetrate;
            $resultstat['X5']['c']['kach']=$CNTNetkach;
            $resultstat['X6']['c']['value']=$CNTLev;
            $resultstat['X6']['c']['weight']=$CNTLevweight;
            $resultstat['X6']['c']['rate']=$CNTLevrate;
            $resultstat['X6']['c']['kach']=$CNTLevkach;
            $resultstat['X_ALL']['c']['kach']=$totalkach;
            $resultstat['X_ALL']['c']['rate']=$KVqrate+$KVavgrate+$QualActrate+$CRMactivityrate+$CNTNetrate+$CNTLevrate;
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
                $KVq = round($KVcurr/$KVq,2);
            }
            if($KVq<=10) {
                $KVqkach = 0;
            } elseif ($KVq>10 && $KVq<15) {
                $KVqkach = 0.5;
            } else {
                $KVqkach = 1;
            }
            $KVqrate = $KVqweight*$KVqkach;
            $KVavg = round($KVavg/$countalldays,2);
            if($KVavg<=20) {
                $KVavgkach = 0;
            } elseif ($KVavg>20 && $KVavg<40) {
                $KVavgkach = 0.5;
            } else {
                $KVavgkach = 1;
            }
            $KVavgrate = ($KVavg/$KVavgmax)*($KVavgweight*$KVavgkach);
            $QualAct = round($QualAct/$countmonfri,2);
            if($QualAct<=0.75) {
                $QualActkach = 0;
            } elseif ($QualAct>0.75 && $QualAct<0.95) {
                $QualActkach = 0.5;
            } else {
                $QualActkach = 1;
            }
            $QualActrate = ($QualAct/$QualActmax)*($QualActweight*$QualActkach);
            $CNTLev = round($CNTLev/$countalldays,2);
            $CRMactivity = round($CRMactivity/$countsat,2);
            if($CRMactivity<=0.5) {
                $CRMactivitykach = 0;
            } elseif ($CRMactivity>0.5 && $CRMactivity<0.8) {
                $CRMactivitykach = 0.5;
            } else {
                $CRMactivitykach = 1;
            }
            $CRMactivityrate = ($CRMactivity/$CRMactivitytmax)*($CRMactivityweight*$CRMactivitykach);

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
            $CNTLevrate = ($CNTLev/$CNTLevmax)*$CNTLevweight*$CNTLevkach;

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
            $resultstat['X1'][$quater]['rate']=$KVqrate;
            $resultstat['X1'][$quater]['kach']=$KVqkach;
            $resultstat['X2'][$quater]['value']=$KVavg;
            $resultstat['X2'][$quater]['weight']=$KVavgweight;
            $resultstat['X2'][$quater]['rate']=$KVavgrate;
            $resultstat['X2'][$quater]['kach']=$KVavgkach;
            $resultstat['X3'][$quater]['value']=$QualAct;
            $resultstat['X3'][$quater]['weight']=$QualActweight;
            $resultstat['X3'][$quater]['rate']=$QualActrate;
            $resultstat['X3'][$quater]['kach']=$QualActkach;
            $resultstat['X4'][$quater]['value']=$CRMactivity;
            $resultstat['X4'][$quater]['weight']=$CRMactivityweight;
            $resultstat['X4'][$quater]['rate']=$CRMactivityrate;
            $resultstat['X4'][$quater]['kach']=$CRMactivitykach;
            $resultstat['X5'][$quater]['value']=$CNTNet;
            $resultstat['X5'][$quater]['weight']=$CNTNetweight;
            $resultstat['X5'][$quater]['rate']=$CNTNetrate;
            $resultstat['X5'][$quater]['kach']=$CNTNetkach;
            $resultstat['X6'][$quater]['value']=$CNTLev;
            $resultstat['X6'][$quater]['weight']=$CNTLevweight;
            $resultstat['X6'][$quater]['rate']=$CNTLevrate;
            $resultstat['X6'][$quater]['kach']=$CNTLevkach;
            $resultstat['X_ALL'][$quater]['kach']=$totalkach;
            $resultstat['X_ALL'][$quater]['rate']=$KVqrate+$KVavgrate+$QualActrate+$CRMactivityrate+$CNTNetrate+$CNTLevrate;
        }
        return $resultstat;
    }
}

