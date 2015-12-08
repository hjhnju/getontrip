<?php
require_once "config.php";
$arrSight   = file(WORK_PATH.INDEX_SIGHT);
$fp         = fopen(WORK_PATH.DESC_VECTOR,"w");
$id         = '';
$sightId    = '';
$sightNames = '';

//要依次加载话题ID与内容全局ID的映射
$arrVoc = file(WORK_PATH.INDEX_VOC);
$arrVocs = array();
foreach ($arrVoc as $val){
    $tmp      = explode("\t",$val);
    $arrVocs[]= trim($tmp[1]);
}

//要依次加载话题ID与内容全局ID的映射
$arrTopic = file(WORK_PATH.INDEX_TOPIC);
$arrTopicIds = array();
foreach ($arrTopic as $val){
    $tmp   = explode("\t",$val);
    $index = trim($tmp[1]);
    $arrTopicIds[$index]= $tmp[0];
}

//要依次加载话题ID与内容全局ID的映射
$arrDesc = file(WORK_PATH.INDEX_SIGHT_DESC);
$arrDescIds = array();
foreach ($arrDesc as $val){
    $tmp = explode("\t",$val);
    $index = trim($tmp[1]);
    $arrDescIds[$index] = $tmp[0];
}

foreach ($arrSight as $sight){
    sscanf($sight,"%d\t%d\t:%s",$id,$sightId,$sightNames);
    
    //描述对应的向量    
    $objSightMeta = new Sight_Object_Meta();
    $objSightMeta->fetch(array('id' => $sightId));
    if(!empty($objSightMeta->describe)){
        $arrVec        = array();
        $objSightMeta->describe = preg_replace( '/<.*?>/s', "", $objSightMeta->describe);
        $arrTopicVoc = Base_Util_String::ChineseAnalyzerAll($objSightMeta->describe);
        $arrTopicVoc = array_unique($arrTopicVoc);
        foreach ($arrTopicVoc as $data){
            $ret =  array_search($data,$arrVocs);
            if($ret !== false){
                $arrVec[] = $ret + 1;
            }
        }
        if(!empty($arrVec)){
            sort($arrVec);
            $str = implode(":1\t",$arrVec);
            $str = sprintf("%s\t%s\t%s:1\r\n",$arrDescIds[$sightId],$id,$str);
            fwrite($fp,$str);
        }
    }    
    
    //百科对应的向量
}
fclose($fp);