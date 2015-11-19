<?php
#!/home/work/local/php/bin -q
require_once("env.inc.php");
$redis = Base_Redis::getInstance();
//热度更新后，要删除掉一些缓存 
//热度计算公式:一个月内或一周内(收藏数+回复数+访问数)
$type     = trim(strtoupper($argv[1]));
$arrTopic = array();
if(isset($argv[2])){
    $arrTopic = array($argv[2]);
}
switch ($type){
    case 'HOUR':
        //$time = time() - 3600;
        $listCollect = new Collect_List_Collect();
        $filter      = '';
        //$listCollect->setFilterString("`type` =".Collect_Type::TOPIC." and create_time >=".$time); 
        $listCollect->setFilterString("`type` =".Collect_Type::TOPIC);
        $listCollect->setPagesize(PHP_INT_MAX);
        $arrCollect  = $listCollect->toArray();
        foreach ($arrCollect['list'] as $val){
            $arrTopic[] = $val['obj_id'];
        }
        
        $listComment = new Comment_List_Comment();
        $filter      = '';
        //$listComment->setFilterString("`type` =".Comment_Type_Type::TOPIC." and create_time >=".$time);
        $listComment->setFilterString("`type` =".Comment_Type_Type::TOPIC);
        $listComment->setPagesize(PHP_INT_MAX);
        $arrComment  = $listComment->toArray();
        foreach ($arrComment['list'] as $val){
            $arrTopic[] = $val['obj_id'];
        }
        break;
    case 'DAY':
        //$time        = time() - 7*24*3600;
        $listCollect = new Collect_List_Collect();
        $filter      = '';
        //$listCollect->setFilterString("`type` =".Collect_Type::TOPIC." and create_time >=".$time);
        $listCollect->setFilterString("`type` =".Collect_Type::TOPIC);
        $listCollect->setPagesize(PHP_INT_MAX);
        $arrCollect  = $listCollect->toArray();
        foreach ($arrCollect['list'] as $val){
            $arrTopic[] = $val['obj_id'];
        }
        
        $listComment = new Comment_List_Comment();
        $filter      = '';
        //$listComment->setFilterString("`type` =".Comment_Type_Type::TOPIC." and create_time >=".$time);
        $listComment->setFilterString("`type` =".Comment_Type_Type::TOPIC);
        $listComment->setPagesize(PHP_INT_MAX);
        $arrComment  = $listComment->toArray();
        foreach ($arrComment['list'] as $val){
            $arrTopic[] = $val['obj_id'];
        }
        
        $listVisit = new Tongji_List_Visit();
        //$filter    = "`type` =".Tongji_Type_Visit::TOPIC." and create_time >=".$time;
        $filter    = "`type` =".Tongji_Type_Visit::TOPIC;
        $listVisit->setFilterString($filter);
        $listVisit->setPagesize(PHP_INT_MAX);
        $arrVisit  = $listVisit->toArray();
        foreach ($arrVisit['list'] as $val){
            $arrTopic[] = $val['obj_id'];
        }
        break;
     default:
            break;
}
$arrTopic = array_unique($arrTopic);
foreach ($arrTopic as $topic){       
    $hot1  = getHot($topic,7*24*60);   
    $hot2  = getHot($topic,time(),'TOTAL');
    $hot3  = getHot($topic,60);
    
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
    
    if($type == 'LATE'){
        if($time == 60){
            $collectTopicNum = $logicCollect->getLateCollectNum(Collect_Type::TOPIC, $topic,$time,'MINUTE');            
            $commentNum      = $logicComment->getLateCommentNum($topic,$time,Comment_Type_Type::TOPIC,'MINUTE');            
            $topicUv         = $logicTopic->getLateTopicVistUv($topic,$time,'MINUTE');
        }else{
            $collectTopicNum = $logicCollect->getLateCollectNum(Collect_Type::TOPIC, $topic,$time);            
            $commentNum      = $logicComment->getLateCommentNum($topic,$time/(24*60));           
            $topicUv         = $logicTopic->getLateTopicVistUv($topic,$time/(24*60));
        }    
    }else{
        $collectTopicNum = $logicCollect->getTotalCollectNum(Collect_Type::TOPIC, $topic);        
        $commentNum      = $logicComment->getTotalCommentNum($topic);       
        $topicUv         = $logicTopic->getTotalTopicVistUv($topic);
    }
    
    $objTopic        = new Topic_Object_Topic();
    $objTopic->fetch(array('id' => $topic));
    $publishTime     = $objTopic->updateTime;
    
    //最近收藏时间
    $listCollect     = new Collect_List_Collect();
    $filter          = "`create_time` >=".time() - $time*60 ." and `obj_id`=".$topic;
    $listCollect->setFilterString($filter);
    $listCollect->setOrder('`create_time` desc');
    $listCollect->setPage(1);
    $listCollect->setPagesize(1);
    $arrCollect = $listCollect->toArray();
    $collectTime     = isset($arrCollect['list'][0])?$arrCollect['list'][0]['create_time']:$publishTime;
    
    //最近评论时间
    $listComment     = new Comment_List_Comment();
    $filter          = "`create_time` >=".time() - $time*60 ." and `obj_id`=".$topic;
    $listComment->setFilterString($filter);
    $listComment->setOrder('`create_time` desc');
    $listComment->setPage(1);
    $listComment->setPagesize(1);
    $arrComment = $listComment->toArray();
    $commentTime     = isset($arrComment['list'][0])?$arrComment['list'][0]['create_time']:$publishTime;    

    $hot  = ($collectTopicNum + $commentNum + 4*log10($topicUv+1) + 1)/((time()-$publishTime)/(3600*100)+(time()-$collectTime)/(3600*100)+(time()-$commentTime)/(3600*100)+1);
    return $hot;
}