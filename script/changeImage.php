<?php
require_once("env.inc.php");
$listTopic = new Topic_List_Topic();
$oss       = Oss_Adapter::getInstance();
$listTopic->setPagesize(PHP_INT_MAX);
$arrTopic = $listTopic->toArray();
foreach ($arrTopic['list'] as $val){
    if(!empty($val['image'])){
        $image = $val['image'];    
        $content  = file_get_contents("http://123.57.67.165:8301/Pic/".$image.".jpg");    
        $filename = $image . '.jpg';
        $res = $oss->writeFileContent($filename, $content);
    }
}

$model = new SightModel();
$ret = $model->getSightList(1, PHP_INT_MAX);
foreach ($ret as $val){
    if(!empty($val['image'])){
        $image = $val['image'];
        $content  = file_get_contents("http://123.57.67.165:8301/Pic/".$image.".jpg");
        $filename = $image . '.jpg';
        $res = $oss->writeFileContent($filename, $content);
    }
}