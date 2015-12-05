<?php
#!/home/work/local/php/bin -q
require_once("env.inc.php");
define("FROM_TIME", mktime(0,0,0,1,1,2015));
$redis = Base_Redis::getInstance();
//热度更新后，要删除掉一些缓存 
//热度计算公式:一个月内或一周内(收藏数+回复数+访问数)
$arrTopicIds = array();
if(isset($argv[1])){
    $arrTopicIds = array($argv[1]);
}

//15分钟内需要更新的话题
$time = time() - 900;
$listCollect = new Collect_List_Collect();
$filter      = '';
$listCollect->setFilterString("`type` =".Collect_Type::TOPIC." and create_time >=".$time);
$listCollect->setFilterString("`type` =".Collect_Type::TOPIC);
$listCollect->setPagesize(PHP_INT_MAX);
$arrCollect  = $listCollect->toArray();
foreach ($arrCollect['list'] as $val){
    $arrTopicIds[] = $val['obj_id'];
}

$listComment = new Comment_List_Comment();
$listComment->setFilterString("`type` =".Comment_Type_Type::TOPIC." and create_time >=".$time);
$listComment->setPagesize(PHP_INT_MAX);
$arrComment  = $listComment->toArray();
foreach ($arrComment['list'] as $val){
    $arrTopicIds[] = $val['obj_id'];
}

$listPraise = new Praise_List_Praise();
$listPraise->setFilterString("`type` =".Praise_Type_Type::TOPIC." and create_time >=".$time);
$listPraise->setPagesize(PHP_INT_MAX);
$arrPraise  = $listPraise->toArray();
foreach ($arrPraise['list'] as $val){
    $arrTopicIds[] = $val['obj_id'];
}

$listTopic = new Topic_List_Topic();
$listTopic->setFilterString("`status` =".Topic_Type_Status::PUBLISHED." and create_time >=".$time);    
$listTopic->setPagesize(PHP_INT_MAX);
$arrTopic  = $listTopic->toArray();
foreach ($arrTopic['list'] as $val){
    $arrTopicIds[] = $val['id'];
}
$arrTopicIds = array_unique($arrTopicIds);
foreach ($arrTopicIds as $topic){       
    $hot1  = getHot($topic,7*24*60);   
    $hot2  = getHot($topic,time(),'TOTAL');
    $hot3  = getHot($topic,15);
    
    $objTopic        = new Topic_Object_Topic();
    $objTopic->fetch(array('id' => $topic));
    if(!empty($objTopic->id)){
        $objTopic->hot1  = $hot1;
        $objTopic->hot2  = $hot2;
        $objTopic->hot3  = $hot3;
        $objTopic->save();
    }
}


function getHot($topic,$time,$type='LATE'){
    $logicCollect = new Collect_Logic_Collect();
    $logicComment = new Comment_Logic_Comment();
    $logicTopic   = new Topic_Logic_Topic();
    $logicPraise  = new Praise_Logic_Praise();
    
    if($type == 'LATE'){
        if($time == 15){
            $collectTopicNum = $logicCollect->getLateCollectNum(Collect_Type::TOPIC, $topic,$time,'MINUTE');            
            $commentNum      = $logicComment->getLateCommentNum($topic,$time,Comment_Type_Type::TOPIC,'MINUTE');            
            $topicUv         = $logicTopic->getLateTopicVistUv($topic,$time,'MINUTE');
            $praiseNum       = $logicPraise->getLatePraiseNum(Praise_Type_Type::TOPIC,$topic,$time,'MINUTE');
        }else{
            $collectTopicNum = $logicCollect->getLateCollectNum(Collect_Type::TOPIC, $topic,$time);            
            $commentNum      = $logicComment->getLateCommentNum($topic,$time/(24*60));           
            $topicUv         = $logicTopic->getLateTopicVistUv($topic,$time/(24*60));
            $praiseNum       = $logicPraise->getLatePraiseNum(Praise_Type_Type::TOPIC,$topic,$time/(24*60));
        }    
    }else{
        $collectTopicNum = $logicCollect->getTotalCollectNum(Collect_Type::TOPIC, $topic);        
        $commentNum      = $logicComment->getTotalCommentNum($topic);       
        $topicUv         = $logicTopic->getTotalTopicVistUv($topic);
        $praiseNum       = $logicPraise->getPraiseNum($topic);
    }
    
    $objTopic        = new Topic_Object_Topic();
    $objTopic->fetch(array('id' => $topic));
    $publishTime     = $objTopic->createTime - FROM_TIME;
    
    //最近收藏时间
    $listCollect     = new Collect_List_Collect();
    $filter          = "`create_time` >=".time() - $time*60 ." and `obj_id`=".$topic." and `type`=".Collect_Type::TOPIC;
    $listCollect->setFilterString($filter);
    $listCollect->setOrder('`create_time` desc');
    $listCollect->setPage(1);
    $listCollect->setPagesize(1);
    $arrCollect = $listCollect->toArray();
    $collectTime     = isset($arrCollect['list'][0])?$arrCollect['list'][0]['create_time']-FROM_TIME:$publishTime;
    
    //最近评论时间
    $listComment     = new Comment_List_Comment();
    $filter          = "`create_time` >=".time() - $time*60 ." and `obj_id`=".$topic." and `type`=".Comment_Type_Type::TOPIC;
    $listComment->setFilterString($filter);
    $listComment->setOrder('`create_time` desc');
    $listComment->setPage(1);
    $listComment->setPagesize(1);
    $arrComment = $listComment->toArray();
    $commentTime     = isset($arrComment['list'][0])?$arrComment['list'][0]['create_time']-FROM_TIME:$publishTime; 

    //最近点赞时间
    $listPraise     = new Praise_List_Praise();
    $filter         = "`create_time` >=".time() - $time*60 ." and `obj_id`=".$topic." and `type`=".Praise_Type_Type::TOPIC;
    $listPraise->setFilterString($filter);
    $listPraise->setOrder('`create_time` desc');
    $listPraise->setPage(1);
    $listPraise->setPagesize(1);
    $arrPraise = $listPraise->toArray();
    $praiseTime     = isset($arrPraise['list'][0])?$arrPraise['list'][0]['create_time']-FROM_TIME:$publishTime;

    $hot  = ($praiseNum + $collectTopicNum + $commentNum + 4*log10($topicUv+1) + 1.0)*(($publishTime+$collectTime+$commentTime+$praiseTime)/(3600*200.0)+1.0);
    return $hot;
}