<?php
require_once "config.php";

const PRE_TOPIC = "topic_";

const PRE_DESC  = "desc_";

const PRE_WIKI  = "wiki_";

if(!is_dir(MODEL_TAG_PATH)){
    mkdir(MODEL_TAG_PATH);  
}

$index = 0;

$fp_label = fopen(WORK_PATH.INDEX_LABEL_TAG,"w");

$listTag  = new Tag_List_Tag();
$listTag->setFilter(array('type' => Tag_Type_Tag::CLASSIFY));
$listTag->setOrder("`id` asc");
$listTag->setPagesize(PHP_INT_MAX);
$arrTag  = $listTag->toArray();
foreach ($arrTag['list'] as $tag){
   
    $strBuffer = "";
    
    //标签话题内容
    $listTopicTag = new Topic_List_Tag();
    $listTopicTag->setFilter(array('tag_id' => $tag['id']));
    $listTopicTag->setOrder("`id` asc");
    $listTopicTag->setPagesize(PHP_INT_MAX);
    $arrTopicTag  = $listTopicTag->toArray();
    foreach ($arrTopicTag['list'] as $TopicTag){
        $objTopic = new Topic_Object_Topic();
        $objTopic->fetch(array('id' => $TopicTag['topic_id']));
        $content = preg_replace( '/<.*?>/s', "", $objTopic->content.$objTopic->title.$objTopic->subtitle);
        if(empty($content)){
            continue;
        }
        $arrTopicVoc = Base_Util_String::ChineseAnalyzerAll($content);
        if(!empty($arrTopicVoc)){
            $strTopicVoc = implode("\t",$arrTopicVoc);
            $strBuffer .= sprintf(PRE_TOPIC."%d\t%s\r\n",$objTopic->id,$strTopicVoc);
        }
    }    
    
    if(!empty($strBuffer)){
        $str = sprintf("%d\ttag:%d\tname:%s\r\n",$index,$tag['id'],$tag['name']);
        fwrite($fp_label, $str);
        
        $fp_sight = fopen(MODEL_TAG_PATH."$index","w");
        fwrite($fp_sight, $strBuffer);
        fclose($fp_sight);
        
        $index += 1;
    }    
}
fclose($fp_label);