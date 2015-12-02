<?php
require_once "config.php";
$arrSight   = file(WORK_PATH.INDEX_SIGHT);
$fp         = fopen(WORK_PATH.INPUT_VECTOR,"w");
$id         = '';
$sightId    = '';
$sightNames = '';
foreach ($arrSight as $sight){
    sscanf($sight,"%s\t%s\t%[^\r]",$id,$sightId,$sightNames);
    
    //对于每个景点，依次取出其话题，描述，百科的内容生成向量
    //要依次加载话题ID与内容全局ID的映射
    $arrTopic = file(WORK_PATH.INDEX_TOPIC);
    $arrTopicIds = array();
    foreach ($arrTopic as $val){
        $tmp = explode(":",$val);
        $arrTopicIds[$tmp[1]] = $tmp[0];
    }
    
    $listSightTopic = new Sight_List_Topic();
    $listSightTopic->setFilter(array('sight_id' => $sightId));
    $listSightTopic->setPagesize(PHP_INT_MAX);
    $arrSightTopic  = $listSightTopic->toArray();
    foreach ($arrSightTopic['list'] as $val){
        $objTopic   = new Topic_Object_Topic();
        $objTopic->fetch(array('id' => $val['topic_id']));
        $str        = '';
        foreach ($arrSight as $temp){
            $tmpid         = '';
            $tmpsightId    = '';
            $tmpsightNames = '';
            sscanf($temp,"%s\t%s\t%[^\r]",$tmpid,$tmpsightId,$tmpsightNames);
            $arrTemp = explode(",",$tmpsightNames);
            foreach ($arrTemp as $data){
                if(strstr($objTopic->title,$data) !== false){
                    $str .= $tmpid.":1\t";
                }
            }
        }
        if(!empty($str)){
            $str = sprintf("%s\t%s\t%s\r\n",$arrTopicIds[$val['topic_id']],$id,$str);
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
            $str        = '';
            foreach ($arrSight as $temp){
                $tmpid         = '';
                $tmpsightId    = '';
                $tmpsightNames = '';
                sscanf($temp,"%s\t%s\t%[^\r]",$tmpid,$tmpsightId,$tmpsightNames);
                $arrTemp = explode(",",$tmpsightNames);
                foreach ($arrTemp as $data){
                    if(strstr($objTopic->title,$data) !== false){
                        $str .= $tmpid.":1\t";
                    }
                }
            }
            if(!empty($str)){
                $str = sprintf("%s\t%s\t%s\r\n",$arrTopicIds[$val['topic_id']],$id,$str);
                fwrite($fp,$str);
            }           
        }
    }
    
    //描述对应的向量
    //要依次加载话题ID与内容全局ID的映射
    unset($arrTopicIds,$arrTopic);
    $arrDesc = file(WORK_PATH.INDEX_SIGHT_DESC);
    $arrDescIds = array();
    foreach ($arrDesc as $val){
        $tmp = explode(":",$val);
        $arrDescIds[$tmp[1]] = $tmp[0];
    }
    $objSightMeta = new Sight_Object_Meta();
    $objSightMeta->fetch(array('id' => $sightId));
    if(!empty($objSightMeta->describe)){
        $str = sprintf("%s\t%s\t%s:1\r\n",$arrDescIds[$sightId],$id,$id);
        fwrite($fp, $str);
    }    
    
    //百科对应的向量
}
fclose($fp);