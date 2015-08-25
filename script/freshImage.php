<?php
require_once("env.inc.php");
if($argc < 2 || $argc > 3){
    print "参数错误，正确用法:php freshImage.php del|add\r\n";
    return 0;
}
$action   = strtolower($argv[1]);
$sightId  = isset($argv[2])?intval($argv[2]):'';

switch ($action){
    case 'del':
        delImage($sightId);
        break;
    case 'add':
        addImage($sightId);
        break;
    default :
        print "参数错误，正确用法:php freshImage.php del|add\r\n";
        return 0;
}

function addImage($sightId = ''){
     $arrTopics  = array();
     $listTopic = new Topic_List_Topic();
     if(!empty($sightId)){
         $logicTopic = new Topic_Logic_Topic();
         $arrTopics  = $logicTopic->getTopicBySight($sightId);
     }
     $oss       = Oss_Adapter::getInstance();
     $listTopic->setPagesize(PHP_INT_MAX);
     $arrTopic = $listTopic->toArray();
     foreach ($arrTopic['list'] as $val){
         if(!empty($arrTopics)){
             if(!in_array($val['id'],$arrTopics)){
                 continue;
             }
         }
         if(!empty($val['image'])){
             $image = $val['image'];
             $content  = file_get_contents("http://123.57.67.165:8301/Pic/".$image);
             $filename = $image ;
             $res = $oss->writeFileContent($filename, $content);
         }
         if(isset($val['content'])){
             $content = $val['content'];
         }
         preg_match_all('/<img.*?data-image="(.*?)">/', $content,$match);
         foreach ($match[1] as $val){
             $content  = file_get_contents("http://123.57.67.165:8301/Pic/".$val);
             $filename = $val ;
             $res = $oss->writeFileContent($filename, $content);
         }
     }
    
     $model = new SightModel();
     if(!empty($sightId)){
         $ret[] = $model->getSightById($sightId);
     }else{
         $ret = $model->getSightList(1, PHP_INT_MAX);
     }
     foreach ($ret as $val){
         if(!empty($val['image'])){
             $image = $val['image'];
             $content  = file_get_contents("http://123.57.67.165:8301/Pic/".$image);
             $filename = $image ;
             $res = $oss->writeFileContent($filename, $content);
         }
     }
}

function delImage($sightId = ''){
    $arrTopics  = array();
    $listTopic  = new Topic_List_Topic();
    if(!empty($sightId)){
        $logicTopic = new Topic_Logic_Topic();
        $arrTopics  = $logicTopic->getTopicBySight($sightId);
    }
    $oss       = Oss_Adapter::getInstance();
    $listTopic->setPagesize(PHP_INT_MAX);
    $arrTopic = $listTopic->toArray();
    foreach ($arrTopic['list'] as $val){
        if(!empty($arrTopics)){
            if(!in_array($val['id'],$arrTopics)){
                continue;
            }
        }
        if(!empty($val['image'])){
            $filename = $val['image'];
            $res = $oss->remove($filename);           
        }
        if(isset($val['content'])){
            $content = $val['content'];
        }        
        preg_match_all('/<img.*?data-image="(.*?)">/', $content,$match);
        foreach ($match[1] as $val){
            $oss->remove($val);
        }
    }
    
    $model = new SightModel();
    if(!empty($sightId)){
        $ret[] = $model->getSightById($sightId);
    }else{
        $ret = $model->getSightList(1, PHP_INT_MAX);
    }
    foreach ($ret as $val){
        if(!empty($val['image'])){
            $filename = $val['image'];
            $res = $oss->remove($filename);  
        }
    }
}