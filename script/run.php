<?php
require_once("env.inc.php");
$list = new Topic_List_Topic();
$list->setPagesize(PHP_INT_MAX);
$arrRet = $list->toArray();

foreach ($arrRet['list'] as $val){
    $objTopic = new Topic_Object_Topic();
    $objTopic->fetch(array('id' => $val['id']));
    if(!empty($objTopic->image)){
        $objTopic->image = $objTopic->image.".jpg";
    }
    $objTopic->save();
}

$model  = new SightModel();
$arrRet = $model->getSightList(1, PHP_INT_MAX);
foreach ($arrRet as $data){
    $sightId = $data['id'];
    $image   = $data['image'];
    if(!empty($image)){
        $model->eddSight($sightId, array('image' => $image."jpg"));
    }
}