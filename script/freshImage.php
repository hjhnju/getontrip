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
         /*if(!empty($val['image'])){
             $image = $val['image'];
             $content  = file_get_contents("http://123.57.67.165:8301/Pic/".$image);
             $filename = $image ;
             $res = $oss->writeFileContent($filename, $content);
         }*/
         if(isset($val['content'])){
             $content = $val['content'];
         }
         preg_match_all('/<img src="\/pic\/(.*?)">/', $content,$match);
         foreach ($match[1] as $val){
             $content  = file_get_contents("http://123.57.67.165:8301/Pic/".$val);
             $filename = $val ;
             $res = $oss->writeFileContent($filename, $content);
         }
     }
     
     /*$listBook = new Book_List_Book();
     $listBook->setPagesize(PHP_INT_MAX);
     $arrBook = $listBook->toArray();
     foreach ($arrBook['list'] as $val){
         if(!empty($val['image'])){
             $image = $val['image'];
             $content  = file_get_contents("http://123.57.67.165:8301/Pic/".$image);
             $filename = $image ;
             $res = $oss->writeFileContent($filename, $content);
         }
     }
    
     $listVideo = new Video_List_Video();
     $listVideo->setPagesize(PHP_INT_MAX);
     $arrVideo = $listVideo->toArray();
     foreach ($arrVideo['list'] as $val){
         if(!empty($val['image'])){
             $image = $val['image'];
             $content  = file_get_contents("http://123.57.67.165:8301/Pic/".$image);
             $filename = $image ;
             $res = $oss->writeFileContent($filename, $content);
         }
     }
     
     $listKeyword = new Keyword_List_Keyword();
     $listKeyword->setPagesize(PHP_INT_MAX);
     $arrKeyword = $listKeyword->toArray();
     foreach ($arrKeyword['list'] as $val){
         if(!empty($val['image'])){
             $image = $val['image'];
             $content  = file_get_contents("http://123.57.67.165:8301/Pic/".$image);
             $filename = $image ;
             $res = $oss->writeFileContent($filename, $content);
         }
     }
     
     $listSight = new Sight_List_Sight();
     $listSight->setPagesize(PHP_INT_MAX);
     $arrSight = $listSight->toArray();
     foreach ($arrSight['list'] as $val){
         if(!empty($val['image'])){
             $image = $val['image'];
             $content  = file_get_contents("http://123.57.67.165:8301/Pic/".$image);
             $filename = $image ;
             $res = $oss->writeFileContent($filename, $content);
         }
     }
     
     $listCity = new City_List_City();
     $listCity->setPagesize(PHP_INT_MAX);
     $arrCity = $listCity->toArray();
     foreach ($arrCity['list'] as $val){
         if(!empty($val['image'])){
             $image = $val['image'];
             $content  = file_get_contents("http://123.57.67.165:8301/Pic/".$image);
             $filename = $image ;
             $res = $oss->writeFileContent($filename, $content);
         }
     }
     
     $logic = new Base_Logic();
     $redis = Base_Redis::getInstance();
     $arrKeys = $redis->keys("sight_topic_*");
     foreach ($arrKeys as $key){
         $redis->delete($key);
     }
      
     $arrKeys = $redis->keys("topic_tag_*");
     foreach ($arrKeys as $key){
         $redis->delete($key);
     }*/
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
        preg_match_all('/<img src="\/pic\/(.*?)">/', $content,$match);
        foreach ($match[1] as $val){
            $oss->remove($val);
        }
    }
    
    $listSight = new Sight_List_Sight();
    $listSight->setPagesize(PHP_INT_MAX);
    $arrSight = $listSight->toArray();
    foreach ($arrSight['list'] as $val){
        if(!empty($val['image'])){
            $filename = $val['image'];
            $res = $oss->remove($filename);  
        }
    }
    
    $listCity = new City_List_City();
    $listCity->setPagesize(PHP_INT_MAX);
    $arrCity = $listCity->toArray();
    foreach ($arrCity['list'] as $val){
        if(!empty($val['image'])){
            $filename = $val['image'];
            $res = $oss->remove($filename);
        }
    }
    
    
    $listBook = new Book_List_Book();
    $listBook->setPagesize(PHP_INT_MAX);
    $arrBook = $listBook->toArray();
    foreach ($arrBook['list'] as $val){
        if(!empty($val['image'])){
            $filename = $val['image'];
            $res = $oss->remove($filename);  
        }
    }
    
    $listVideo = new Video_List_Video();
    $listVideo->setPagesize(PHP_INT_MAX);
    $arrVideo = $listVideo->toArray();
    foreach ($arrVideo['list'] as $val){
        if(!empty($val['image'])){
            $filename = $val['image'];
            $res = $oss->remove($filename);  
        }
    }
     
    $listKeyword = new Keyword_List_Keyword();
    $listKeyword->setPagesize(PHP_INT_MAX);
    $arrKeyword = $listKeyword->toArray();
    foreach ($arrKeyword['list'] as $val){
        if(!empty($val['image'])){
            $filename = $val['image'];
            $res = $oss->remove($filename);  
        }
    }
}