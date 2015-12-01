<?php
require_once "env.inc.php";
ini_set("default_charset","utf-8"); 
const TAG_PATH   = '/home/work/var/tag/all/';
const SIGHT_PATH = '/home/work/var/sight/all/';
$sightList = new Sight_List_Sight();
$sightList->setPagesize(PHP_INT_MAX);
$arrSight  = $sightList->toArray();
foreach ($arrSight['list'] as $sight){
    $listSightTopic = new Sight_List_Topic();
    $listSightTopic->setFilter(array('sight_id' => $sight['id']));
    $listSightTopic->setPagesize(PHP_INT_MAX);
    $arrSightTopic    = $listSightTopic->toArray();
    if(!empty($arrSightTopic['list'])){
        $path  = SIGHT_PATH.$sight['name'];
        if(!is_dir($path)){
            mkdir($path);
        }
        foreach ($arrSightTopic['list'] as $index => $val){
            $objTopic = new Topic_Object_Topic();
            $objTopic->fetch(array('id' => $val['topic_id']));
            $content = preg_replace('/<.*?>/is', "", $objTopic->content);
            $arrRet = Base_Util_String::ChineseAnalyzerAll($content);
            $content = implode("\t",$arrRet);
            $filename = $path."/".$index;
            file_put_contents($filename, $content);
        }
    }
}