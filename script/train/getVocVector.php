<?php
require_once "config.php";
$arrSight   = file(WORK_PATH.INDEX_SIGHT);
$fp         = fopen(WORK_PATH.VOC_VECTOR,"w");
$id         = '';
$sightId    = '';
$sightNames = '';

//要依次加载话题ID与内容全局ID的映射
$arrVoc = file(WORK_PATH.INDEX_VOC);
$arrVocs = array();
foreach ($arrVoc as $val){
    $tmp      = explode(":",$val);
    $arrVocs[]= trim($tmp[1]);
}

//要依次加载话题ID与内容全局ID的映射
$arrTopic = file(WORK_PATH.INDEX_TOPIC);
$arrTopicIds = array();
foreach ($arrTopic as $val){
    $tmp   = explode(":",$val);
    $index = trim($tmp[1]);
    $arrTopicIds[$index]= $tmp[0];
}

//要依次加载话题ID与内容全局ID的映射
$arrDesc = file(WORK_PATH.INDEX_SIGHT_DESC);
$arrDescIds = array();
foreach ($arrDesc as $val){
    $tmp = explode(":",$val);
    $index = trim($tmp[1]);
    $arrDescIds[$index] = $tmp[0];
}

foreach ($arrSight as $sight){
    sscanf($sight,"%s\t%s\t%[^\r]",$id,$sightId,$sightNames);
    
    //对于每个景点，依次取出其话题，描述，百科的内容生成向量
    $listSightTopic = new Sight_List_Topic();
    $listSightTopic->setFilter(array('sight_id' => $sightId));
    $listSightTopic->setPagesize(PHP_INT_MAX);
    $arrSightTopic  = $listSightTopic->toArray();
    foreach ($arrSightTopic['list'] as $val){
        $objTopic   = new Topic_Object_Topic();        
        $objTopic->fetch(array('id' => $val['topic_id']));
        if($objTopic->status !== Topic_Type_Status::PUBLISHED){
            continue;
        }
        $arrVec        = array();
        $objTopic->content  = preg_replace( '/<.*?>/s', "", $objTopic->content);
        $objTopic->content .= $objTopic->title;
        $arrTopicVoc = Base_Util_String::ChineseAnalyzerAll($objTopic->content);
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
            $str = sprintf("%s\t%s\t%s:1\r\n",$arrTopicIds[$val['topic_id']],$id,$str);
            fwrite($fp,$str);
        }
    }
    
    $listSightTag = new Sight_List_Tag();
    $listSightTag->setFilter(array('sight_id' => $sightId));
    $listSightTag->setPagesize(PHP_INT_MAX);
    $arrSightTag  = $listSightTag->toArray();
    foreach ($arrSightTag['list'] as $val){
        $listTopicTag = new Topic_List_Tag();
        $listTopicTag->setFilter(array('tag_id' => $val['tag_id']));
        $listTopicTag->setPagesize(PHP_INT_MAX);
        $arrTopicTag  = $listTopicTag->toArray();
        foreach ($arrTopicTag['list'] as $topictag){
            $objTopic   = new Topic_Object_Topic();
            $objTopic->fetch(array('id' => $topictag['topic_id']));
            if($objTopic->status !== Topic_Type_Status::PUBLISHED){
                continue;
            }
            $arrVec        = array();
            $objTopic->content  = preg_replace( '/<.*?>/s', "", $objTopic->content);
            $objTopic->content .= $objTopic->title;
            $arrTopicVoc = Base_Util_String::ChineseAnalyzerAll($objTopic->content);
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
                $str = sprintf("%s\t%s\t%s:1\r\n",$arrTopicIds[$topictag['topic_id']],$id,$str);
                fwrite($fp,$str);
            }          
        }
    }
    
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