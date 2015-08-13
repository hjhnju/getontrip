<?php
require_once("env.inc.php");
switch (strtolower($argv[1])){
    case 'del':
        delImage();
        break;
    case 'add':
        addImage();
        break;
    default :
        print "参数错误，正确用法:php freshImage.php del|add\r\n";
        return 0;
}

function addImage(){
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
}

function delImage(){
    $listTopic = new Topic_List_Topic();
    $oss       = Oss_Adapter::getInstance();
    $listTopic->setPagesize(PHP_INT_MAX);
    $arrTopic = $listTopic->toArray();
    foreach ($arrTopic['list'] as $val){
        if(!empty($val['image'])){
            $filename = $val['image'].'.jpg';
            $res = $oss->remove($filename);           
        }
    }
    
    $model = new SightModel();
    $ret = $model->getSightList(1, PHP_INT_MAX);
    foreach ($ret as $val){
        if(!empty($val['image'])){
            $filename = $val['image'].'.jpg';
            $res = $oss->remove($filename);  
        }
    }
}