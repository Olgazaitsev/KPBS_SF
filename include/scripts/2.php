<?php
$quater = 1;
$year = 2021;
$user = 17;

$list = COption::GetOptionString('kpbs.custom', 'ib_id');

if($quater==1) {
    if($year == 2021) {
        $from = '13.03.'.$year;
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

$fromdate = date("d.m.Y",strtotime($from));
$todate = date("d.m.Y",strtotime($to));


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

echo "<pre>";
print_r($transfres2);
echo "</pre>";

$totime = strtotime($to);

$CRMactivity = 0;
$CRMactivityweight = 15;
$CRMactivityrate = 0;
$countalldays = 0;
$countmonfri = 0;
$countsat = 0;
$KVcurr = 0;
$KVq = 0;
$KVqweight = 15;
$KVqrate = 0;
$KVavg = 0;
$KVavgweight = 20;
$KVavgrate = 0;
$QualAct = 0;
$QualActweight = 15;
$QualActrate = 0;
$CNTLev = 0;
$CNTLevweight = 15;
$CNTLevrate = 0;
$CNTNet = 0;
$CNTNetweight = 20;
$CNTNetrate = 0;

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
    $KVqrate = 0;
} elseif ($KVq>10 && $KVq<15) {
    $KVqrate = $KVqweight*0.5;
} else {
    $KVqrate = $KVqweight*1;
}
$KVavg = round($KVavg/$countalldays,2);
if($KVavg<=20) {
    $KVavgrate  = 0;
} elseif ($KVavg>20 && $KVavg<40) {
    $KVavgrate = $KVavgweight*0.5;
} else {
    $KVavgrate = $KVavgweight*1;
}
$QualAct = round($QualAct/$countmonfri,2);
if($QualAct<=0.75) {
    $QualActrate = 0;
} elseif ($QualAct>0.75 && $QualAct<0.95) {
    $QualActrate = $QualActweight*0.5;
} else {
    $QualActrate = $QualActweight*1;
}
$CNTLev = round($CNTLev/$countalldays,2);
if($CNTLev<=1) {
    $CNTLevrate = 0;
} elseif ($CNTLev>1 && $CNTLev<2) {
    $CNTLevrate = $CNTLevweight*0.5;
} else {
    $CNTLevrate= $CNTLevweight*1;
}
$CNTNet = round($CNTNet/$countalldays,2);
if($CNTNet<=1) {
    $CNTNetrate = 0;
} elseif ($CNTNet>1 && $CNTNet<3) {
    $CNTNetrate = $CNTNetweight*0.5;
} else {
    $CNTNetrate= $CNTNetweight*1;
}
$CRMactivity = round($CRMactivity/$countsat,2);
if($CRMactivity<=0.5) {
    $CRMactivityrate = 0;
} elseif ($CRMactivity>0.5 && $CRMactivity<0.8) {
    $CRMactivityrate = $CRMactivityweight*0.5;
} else {
    $CRMactivityrate = $CRMactivityweight*1;
}

$resultstat = [];
$resultstat['KVq']['value']=$KVq;
$resultstat['KVq']['weight']=$KVqweight;
$resultstat['KVq']['rate']=$KVqrate;
$resultstat['KVavg']['value']=$KVavg;
$resultstat['KVavg']['weight']=$KVavgweight;
$resultstat['KVavg']['rate']=$KVavgrate;
$resultstat['QualAct']['value']=$QualAct;
$resultstat['QualAct']['weight']=$QualActweight;
$resultstat['QualAct']['rate']=$QualActrate;
$resultstat['CNTLev']['value']=$CNTLev;
$resultstat['CNTLev']['weight']=$CNTLevweight;
$resultstat['CNTLev']['rate']=$CNTLevrate;
$resultstat['CNTNet']['value']=$CNTNet;
$resultstat['CNTNet']['weight']=$CNTNetweight;
$resultstat['CNTNet']['rate']=$CNTNetrate;
$resultstat['CRMactivity']['value']=$CRMactivity;
$resultstat['CRMactivity']['weight']=$CRMactivityweight;
$resultstat['CRMactivity']['rate']=$CRMactivityrate;
echo "<pre>";
print_r($resultstat);
echo "</pre>";

