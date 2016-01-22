<?php
require_once("env.inc.php");
$modelGis  = new GisModel();
$listSight = new Sight_List_Sight();
$listSight->setFilter(array('status' => Sight_Type_Status::PUBLISHED));
$listSight->setPagesize(PHP_INT_MAX);
$arrSight  = $listSight->toArray();
foreach ($arrSight['list'] as $val){
    if(!empty($val['x']) && !empty($val['y'])){
        $modelGis->insertSight($val['id']);
    }
}

$listKeyword = new Keyword_List_Keyword();
$listKeyword->setFilter(array('status' => Keyword_Type_Status::PUBLISHED));
$listKeyword->setPagesize(PHP_INT_MAX);
$arrKeyword  = $listKeyword->toArray();
foreach ($arrKeyword['list'] as $val){
    if(!empty($val['x']) && !empty($val['y'])){
        $modelGis->insertLandscape($val['id']);
    }
}