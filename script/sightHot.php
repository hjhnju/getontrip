<?php
#!/home/work/local/php/bin -q
require_once("env.inc.php");
$redis = Base_Redis::getInstance();
//热度更新后，要删除掉一些缓存 
//热度计算公式:一个月内或一周内(收藏数+回复数+访问数)

$listSight = new Sight_List_Sight();
$listSight->setPagesize(PHP_INT_MAX);
$arrSight  = $listSight->toArray();
foreach ($arrSight['list'] as $sight){       
    $hot1  = getHot($sight['id'],7*24*60); 
    $hot2  = getHot($sight['id'],time(),'TOTAL');
    $hot3  = getHot($sight['id'],60);
    
    $objSight        = new Sight_Object_Sight();
    $objSight->fetch(array('id' => $sight['id']));
    if(!empty($objSight->id)){
        $time            = $objSight->updateTime;
        $objSight->hot1  = $hot1;
        $objSight->hot2  = $hot2;
        $objSight->hot3  = $hot3;
        $objSight->updateTime = $time;
        $objSight->save();
    }
}
function getHot($sight,$time,$type='LATE'){
    $logicCollect = new Collect_Logic_Collect();    
    $logicSight   = new Sight_Logic_Sight();
    $collectSightNum = 0;
    
    if($type == 'LATE'){
        if($time == 60){
            $collectSightNum = $logicCollect->getLateCollectNum(Collect_Type::SIGHT, $sight,$time,'MINUTE');  
            $listVisit        = new Tongji_List_Visit();
            $listVisit->setFilterString("`type` = ".Collect_Type::SIGHT." and create_time >=".$time*60);
            $visitNum        = $listVisit->getTotal();
        }else{
            $collectSightNum = $logicCollect->getLateCollectNum(Collect_Type::SIGHT, $sight,$time);         
            $listVisit        = new Tongji_List_Visit();
            $listVisit->setFilterString("`type` = ".Collect_Type::SIGHT." and create_time >=".$time*60);
            $visitNum        = $listVisit->getTotal();
        }    
    }else{
        $collectSightNum = $logicCollect->getTotalCollectNum(Collect_Type::SIGHT, $sight); 
        $listVisit       = new Tongji_List_Visit();
        $listVisit->setFilter(array('type' => Collect_Type::SIGHT));
        $visitNum        = $listVisit->getTotal();
    }
    
    //发布时间
    $objSight        = new Sight_Object_Sight();
    $objSight->fetch(array('id' => $sight));
    $publishTime     = $objTopic->updateTime;
    
    //最近收藏时间
    $listCollect     = new Collect_List_Collect();
    $filter          = "`create_time` >=".time() - $time*60 ." and `obj_id`=".$sight;
    $listCollect->setFilterString($filter);
    $listCollect->setOrder('`create_time` desc');
    $listCollect->setPage(1);
    $listCollect->setPagesize(1);
    $arrCollect = $listCollect->toArray();
    $collectTime     = isset($arrCollect['list'][0])?$arrCollect['list'][0]['create_time']:$publishTime;    

    
    //话题数
    $topicNum       = $logicSight->getTopicNum($sight,array('status' => Sight_Type_Status::PUBLISHED));
    
    //标签数
    $logicTag       = new Tag_Logic_Tag();
    $tagNum         = count($logicTag->getTagIdsBySight($sight));
    
    $hot  = (log10($topicNum+1)+ $tagNum + $collectSightNum + 4*log10($visitNum+1) + 1)/((time()-$publishTime)/(3600*100)+(time()-$collectTime)/(3600*100)+1);
    return $hot;
}