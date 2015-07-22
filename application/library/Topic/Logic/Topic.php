<?php
class Topic_Logic_Topic{
    
    protected $sightId = '';
    protected $size    = 0;
    protected $strTags = '';
    protected $strDate = "1 month ago";
    
    public function __construct(){
        
    }
    
    /**
     * 获取最热门的话题，带景点ID、时间范围、大小、标签过滤，并加上答案等信息:热度=话题收藏数+评论数+话题浏览量
     * @param integer $sightId
     * @param integer $size
     * @return array
     */
    public function getHotTopic($sightId,$period='1 month ago',$size=2,$strTags=''){
        $arrHotDegree     = array();
        $arrTags          = array();
        $arrTet           = array();
        $collectTopicNum  = 0;
        $visitTopicUv     = 0;
        $commentNum       = 0;
        
        $redis = Base_Redis::getInstance();
        
        //获取景点的所有话题
        if(!empty($sightId)){        
            $ret = $redis->zRange(Sight_Keys::getSightTopicName($sightId),0,-1);
        }else{
            $arrKeys = $redis->keys(Sight_Keys::getSightTopicName("*"));
            foreach ($arrKeys as $key){
                $ret = array_merge($ret,$redis->zRange($key,0,-1));
            }            
        }
        foreach($ret as $key => $val){
            //根据标签过滤话题
            if(!empty($strTags)){
                $arrTags = explode(",",$strTags);
                $arrHasTags = $redis->sGetMembers(Topic_Keys::getTopicTagKey($val['id']));
                $temp = array_diff($arrHasTags,$arrTags);
                if(count($arrHasTags) == count($temp)){
                    continue;
                }
            }              
            
            $objTopic = new Topic_Object_Topic();
            $objTopic->fetch(array('id' => $val));
            $arrRet[] = $objTopic->toArray();
                       
            //话题最近收藏数
            $logicCollect      = new Collect_Logic_Collect();
            $collectTopicNum   = $logicCollect->getLateCollectNum(Collect_Keys::TOPIC, $val['id'],$period);
            
            //话题最近访问人数
            $visitTopicUv      = $this->getTopicVistUv($val, $period);
            
            //最近评论次数
            $logicComment      = new Comment_Logic_Comment();
            $commentNum        = $logicComment->getCommentNum($val, $period);
            
            $arrHotDegree[] = $collectTopicNum + $commentNum + $visitTopicUv;
            
            //话题访问次数            
            $arrRet[$key]['visit']   = $this->getTopicVistPv($val, $period);
            
            //话题收藏数
            $arrRet[$key]['collect'] = $collectTopicNum;
            
            //话题来源
            $logicSource = new Source_Logic_Source();
            $arrRet[$key]['from']    = $logicSource->getSourceName($objTopic->from);
        }        
        //根据权重排序
        array_multisort($arrHotDegree, SORT_DESC , $arrRet);
        return array_slice($arrRet,0,$size);
    }
    
    /**
     * 获取最新的话题，带景点、时间范围、大小、标签过滤，并加上答案等信息:话题的更新时间取话题时间与答案时间中的最新者
     * @param integer $sightId
     * @param integer $size
     * @return array
     */
    public function getNewTopic($sightId,$period='1 month ago',$page,$pageSize,$strTags=''){
        $arrHotDegree     = array();
        $arrTags          = array();
        $collectTopicNum  = 0;
        $visitTopicNum    = 0;
        $strFileter       = 'true';
        
        $redis = Base_Redis::getInstance();
        
        $listTopic = new Topic_List_Topic();
        
        if(!empty($sightId)){
            $ret = $redis->zRange(Sight_Keys::getSightTopicName($sightId),0,-1);
            $strTopicIds = implode(",",$ret);
            $strFileter = "`id` in($strTopicIds)";           
        }
        if(!empty($period)){
            $time      = strtotime($period);
            $strFileter .= " AND `update_time` > $time";
        }
        $listTopic->setFields(array('id','title','content','desc','image'));
        $listTopic->setFilterString($strFileter);
        $listTopic->setOrder("update_time desc");
        $listTopic->setPage($page);
        $listTopic->setPagesize($pageSize);
        $ret = $listTopic->toArray();
        foreach($ret['list'] as $key => $val){
            if(!empty($strTags)){
                $arrTags = explode(",",$strTags);
                $arrHasTags = $redis->sGetMembers(Topic_Keys::getTopicTagKey($val['id']));
                $temp = array_diff($arrHasTags,$arrTags);
                if(count($arrHasTags) == count($temp)){
                    continue;
                }
            }            

            //话题收藏数
            $logicCollect      = new Collect_Logic_Collect();
            $collectTopicNum   = $logicCollect->getLateCollectNum(Collect_Keys::TOPIC, $val['id']);             

            $ret['list'][$key]['visit']   = $this->getTopicVistPv($val, $period);
            $ret['list'][$key]['collect'] = $collectTopicNum;
            
            //话题来源
            $logicSource = new Source_Logic_Source();
            $arrRet[$key]['from']    = $logicSource->getSourceName($objTopic->from);
        }        
        return $ret;
    }
    
    
    /**
     * 根据景点ID获取一个月内的话题数
     * @param integer $sightId
     * @return integer
     */
    public function getHotTopicNum($sightId,$during){
        $redis = Base_Redis::getInstance();
        $start = 0;
        $end = time();
        if(!empty($during)){
            $start = strtotime($during);
        }
        $ret = $redis->zRangeByScore(Sight_Keys::getSightTopicName($sightId),$start,$end);
        return count($ret);
    }
    
    /**
     * 获取话题详细信息
     * @param integer $topicId
     * @return Topic_Object_Topic
     */
    public function getTopicDetail($topicId,$device_id,$page,$pageSize){
        $objTopic = new Topic_Object_Topic();
        $objTopic->fetch(array('id' => $topicId));
        $arrRet = $objTopic->toArray();
        
        //添加redis中话题访问次数统计
        $redis = Base_Redis::getInstance();
        
        $logicUser = new User_Logic_User();
        $userId    = $logicUser->getUserId($device_id);
        $redis->zAdd(Topic_Keys::getTopicVisitKey($topicId),time(),$userId);
               
        return $arrRet;
    }    
    
    /**
     * 获取某个用户的所有话题
     * @param integer $deviceId
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getUserTopic($deviceId,$page,$pageSize){
        $logicUser = new User_Logic_User();
        $userId    = $logicUser->getUserId($deviceId);
        $listTopic = new Topic_List_Topic();
        $listTopic->setFilter(array('user_id' => $userId));
        $listTopic->setPage($page);
        $listTopic->setPagesize($pageSize);
        return $listTopic->toArray();
    }
    
    /**
     * 根据景点ID获取话题信息
     * @param integer $sightId
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getTopicBySight($sightId,$page=1,$pageSize=PHP_INT_MAX){
        $arrRet = array();
        $listSightTopic = new Sight_List_Topic();
        $listSightTopic->setFields(array('topic_id'));
        $listSightTopic->setFilter(array('sight_id' => $sightId));
        $listSightTopic->setPage($page);
        $listSightTopic->setPagesize($pageSize);
        $ret = $listSightTopic->toArray();
        foreach ($ret['list'] as $key => $val){
            $arrRet[] = $val['topic_id'];
        }
        return $arrRet;
    }
    
    /**
     * 获取话题最近的访问人数
     * @param integer $topicId
     * @param string $during
     * @return integer
     */
    public function getTopicVistUv($topicId,$during){
        $redis   = Base_Redis::getInstance();
        $from    = strtotime($during);
        $arrUser = $redis->zRangeByScore(Topic_Keys::getTopicVisitKey($topicId),$from,time());
        return count(array_unique($arrUser));
    }
    
    /**
     * 获取话题最近的访问次数
     * @param integer $topicId
     * @param string $during
     * @return integer
     */
    public function getTopicVistPv($topicId,$during){
        $redis   = Base_Redis::getInstance();
        $from    = strtotime($during);
        $arrUser = $redis->zRangeByScore(Topic_Keys::getTopicVisitKey($topicId),$from,time());
        return count($arrUser);
    }
}