<?php
require_once "config.php";
$arrSight   = file(WORK_PATH.INDEX_SIGHT);
define("SIGHT_COUNT",count($arrSight));
unset($arrSight);
$arrRet = array();
/*$listCityMeta = new City_List_Meta();
$listCityMeta->setFilterString("city_meta.`cityid` = 0 AND city_meta.`provinceid` != 0");
$listCityMeta->setPagesize(PHP_INT_MAX);
$arrCity      = $listCityMeta->toArray();
foreach ($arrCity['list'] as $val){
    
}*/

//景点相关字符集合
$listSight = new Sight_List_Meta();
$listSight->setPagesize(1000);
$arrSight  = $listSight->toArray();
foreach ($arrSight['list'] as $val){
    $objSight = new Sight_Object_Sight();
    $objSight->fetch(array('id' => $val['id']));
    if(!empty($objSight->name)){
        $arrRet = array_merge($arrRet,Base_Util_String::ChineseAnalyzerAll($objSight->name));
        if(!empty($objSight->describe)){
            $arrRet = array_merge($arrRet,Base_Util_String::ChineseAnalyzerAll($objSight->describe));
        }
    }
    $arrRet = array_merge($arrRet,Base_Util_String::ChineseAnalyzerAll($val['name']));
    $arrRet = array_merge($arrRet,Base_Util_String::ChineseAnalyzerAll($val['describe']));
    $arrRet = array_unique($arrRet);
}

$fp = fopen(WORK_PATH.INDEX_VOC, "w");
foreach ($arrRet as $index => $val){
    $str = sprintf("%d:%s\r\n",$index + SIGHT_COUNT + 1 , trim($val));
    fputs($fp,$str);
}
fclose($fp);