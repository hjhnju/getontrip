<?php
require_once("env.inc.php");
$redis = Base_Redis::getInstance();
//热度更新后，要删除掉有些缓存 
//热度计算公式:一个月内或一周内(收藏数+内回复数+访问数)
if(isset($argv[1])){
    $arrTopic = $argv[1];
}else{
    $list  = new Topic_List_Topic();
    $list->setPagesize(PHP_INT_MAX);
    $arrRet = $list->toArray();
    foreach ($arrRet['list'] as $val){
        $arrTopic[] = $val['id'];
    }
}
foreach ($arrTopic as $topic){
    //一个月内话题收藏数
    $logicCollect    = new Collect_Logic_Collect();
    $collectTopicNum = $logicCollect->getLateCollectNum(Collect_Type::TOPIC, $topic,30);
    
    //一个月内回复数
    $logicComment    = new Comment_Logic_Comment();
    $commentNum      = $logicComment->getLateCommentNum($topic,30);
    
    //一个月内访问数
    $logicTopic      = new Topic_Logic_Topic();
    $topicUv         = $logicTopic->getLateTopicVistUv($topic,30);
    
    $hot2            = $collectTopicNum + $commentNum + $topicUv;
    
    //一周内话题收藏数
    $collectTopicNum = $logicCollect->getLateCollectNum(Collect_Type::TOPIC, $topic,7);
    
    //一周内回复数
    $commentNum      = $logicComment->getLateCommentNum($topic,7);
    
    //一周内访问数
    $topicUv         = $logicTopic->getLateTopicVistUv($topic,7);
    
    $hot1            = $collectTopicNum + $commentNum + $topicUv;

    $obj = new Topic_Object_Topic();
    $obj->fetch(array('id' => $topic));
    $obj->hot1 = $hot1; 
    $obj->hot2 = $hot2;
    $obj->save();
    
    $listSightTopic = new Sight_List_Topic();
    $listSightTopic->setPagesize(PHP_INT_MAX);
    $listSightTopic->setFilter(array('topic_id' => $arrRet['id']));
    $ret = $listSightTopic->toArray();
    foreach ($ret['list'] as $val){
        $arrKeys = $redis->keys(Sight_Keys::getHotTopicKey($val['sight_id'], '*'));
        foreach ($arrKeys as $key){
            $redis->delete($key);
        }
    }
}