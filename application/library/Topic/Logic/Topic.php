<?php
class Topic_Logic_Topic{
    
    protected $sightId = '';
    protected $size    = 0;
    protected $strTags = '';
    protected $strDate = "1 month ago";
    
    public function __construct(){
        
    }
    
    /**
     * 获取最热门的话题，带景点ID、时间范围、大小、标签过滤，并加上答案等信息:热度=话题收藏数+答案数+答案点赞数
     * @param integer $sightId
     * @param integer $size
     * @return array
     */
    public function getHotTopic($sightId,$period='1 month ago',$size=2,$strTags=''){
        $arrHotDegree     = array();
        $arrTags          = array();
        $arrTet           = array();
        $collectTopicNum  = 0;
        $visitTopicNum    = 0;
        $answerNum        = 0;
        $collectAnswerNum = 0;
        
        $redis = Base_Redis::getInstance();
        

        if(!empty($sightId)){        
            $ret = $redis->zRange(Sight_Keys::getSightTopicName($sightId),0,-1);
        }else{
            $arrKeys = $redis->keys(Sight_Keys::getSightTopicName("*"));
            foreach ($arrKeys as $key){
                $ret = array_merge($ret,$redis->zRange($key,0,-1));
            }            
        }
        foreach($ret as $key => $val){
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
                       
            $logicCollect      = new Collect_Logic_Collect();
            $collectTopicNum   = $logicCollect->getLateCollectNum(Collect_Keys::TOPIC, $val['id']); //话题收藏数
            $listAnswer        = new Answers_List_Answers();
            $time   = strtotime($period);
            $filter = "`topic_id` = ".$val['id']." and `update_time` > $time";
            $listAnswer->setFilterString($filter);
            $arrAnswer = $listAnswer->toArray();
            $answerNum    = $arrAnswer['total'];  //答案数                       
            $logicPraise  = new Praise_Logic_Praise();
            foreach ($arrAnswer['list'] as $data){
                //答案点赞数
                $upAnswerNum       += $logicPraise->getLatePraiseNum($data['id']);
                //答案收藏数
                $collectAnswerNum  += $logicCollect->getLateCollectNum(Collect_Keys::ANSWER, $data['id']);
            }
            
            $arrHotDegree[] = $collectTopicNum + $answerNum + $collectAnswerNum;
            
            //有描述用描述，没描述用答案
            if(isset($val['desc']) &&(!empty($val['desc']))){
                $arrRet[$key]['addinfo'] = $val['desc'];
            }else{
                $listAnswer = new Answers_List_Answers();
                $listAnswer->setFilter(array('topic_id' => $val['id']));
                $listAnswer->setFields(array('id','from','content'));
                $listAnswer->setOrder("create_time desc");
                $listAnswer->setPagesize(1);
                $arrAnswer = $listAnswer->toArray();
                $arrRet[$key]['addinfo'] = $arrAnswer['list'];
            }            
            $visitTopicNum = $redis->hGet(Topic_Keys::REDIS_TOPIC_VISIT_KEY,$val['id']);
            $arrRet[$key]['visit']   = empty($visitTopicNum)?0:$visitTopicNum;
            $arrRet[$key]['collect'] = $collectTopicNum;
        }        
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
                         
            $logicCollect      = new Collect_Logic_Collect();
            $collectTopicNum   = $logicCollect->getLateCollectNum(Collect_Keys::TOPIC, $val['id']); //话题收藏数
            
            //有描述用描述，没描述用答案
            if(isset($val['desc']) &&(!empty($val['desc']))){
                $ret['list'][$key]['addinfo'] = $val['desc'];
            }else{
                $listAnswer        = new Answers_List_Answers();
                $listAnswer->setFilter(array('topic_id' => $val['id']));
                $listAnswer->setFields(array('id','from','content'));
                $listAnswer->setOrder("create_time desc");
                $listAnswer->setPagesize(1);
                $arrAnswer = $listAnswer->toArray();
                $ret['list'][$key]['addinfo'] = $arrAnswer['list'];
            }            
            $visitTopicNum = $redis->hGet(Topic_Keys::REDIS_TOPIC_VISIT_KEY,$val['id']);
            $ret['list'][$key]['visit']   = empty($visitTopicNum)?0:$visitTopicNum;
            $ret['list'][$key]['collect'] = $collectTopicNum;
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
    public function getTopicDetail($topicId,$page,$pageSize){
        $objTopic = new Topic_Object_Topic();
        $objTopic->fetch(array('id' => $topicId));
        $arrRet = $objTopic->toArray();
        $listAnswers = new Answers_List_Answers();
        $listAnswers->setFilter(array('topic_id' => $topicId));
        $listAnswers->setPage($page);
        $listAnswers->setPagesize($pageSize);
        $arrAnswers = $listAnswers->toArray();
        $arrRet['answers'] = $arrAnswers;
        
        //添加redis中话题访问次数统计
        $redis = Base_Redis::getInstance();
        $visitTopicNum = $redis->hGet(Topic_Keys::REDIS_TOPIC_VISIT_KEY,$topicId);
        if(empty($visitTopicNum)){
            $redis->hSet(Topic_Keys::REDIS_TOPIC_VISIT_KEY,$topicId,1);
        }else{
            $redis->hSet(Topic_Keys::REDIS_TOPIC_VISIT_KEY,$topicId,$visitTopicNum+1);
        }
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
}