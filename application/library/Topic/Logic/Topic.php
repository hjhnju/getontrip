<?php
class Topic_Logic_Topic{
    
    public function __construct(){
        
    }
    
    /**
     * 获取最热门的话题，带标签过滤，并加上答案等信息:热度=话题收藏数+答案数+答案点赞数
     * @param integer $sightId
     * @param integer $size
     * @return array
     */
    public function getHotTopic($sightId,$size=2,$strTags=''){
        $arrHotDegree     = array();
        $arrTopics        = array();
        $collectTopicNum  = 0;
        $upAnswerNum      = 0;
        $answerNum        = 0;
        $collectAnswerNum = 0;
        
        $redis = Base_Redis::getInstance();
        if(!empty($strTags)){
            $arrTags = explode(",",$strTags);
            foreach ($arrTags as $tag){
                $arrTopics = array_merge($arrTopics,$redis->sGetMembers(Tag_Keys::getTagTopic($tag)));
            }
        }
        
        $listTopic = new Topic_List_Topic();
        $listTopic->setFilter(array('sight_id' => $sightId));
        $listTopic->setPagesize(PHP_INT_MAX);
        $ret = $listTopic->toArray();
        foreach($ret['list'] as $key => $val){
            if((!empty($arrTopics)) && (!in_array($val['id'],$arrTopics))){
                unset($ret['list'][$key]);
                continue;
            }            
            
            $logicCollect      = new Collect_Logic_Collect();
            $collectTopicNum   = $logicCollect->getLateCollectNum(Collect_Keys::TOPIC, $val['id']); //话题收藏数
            $listAnswer        = new Answers_List_Answers();
            $time   = strtotime("-1 month");
            $filter = "`topic_id` = ".$val['id']." and `update_time` > $time";
            $listAnswer->setFilterString($filter);
            $arrAnswer = $listAnswer->toArray();
            $answerNum    = count($arrAnswer['list']);  //答案数                       
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
                $ret['list'][$key]['addinfo'] = $val['desc'];
            }else{
                $listAnswer = new Answers_List_Answers();
                $listAnswer->setFilter(array('topic_id' => $val['id']));
                $listAnswer->setOrder("create_time desc");
                $listAnswer->setPagesize(1);
                $arrAnswer = $listAnswer->toArray();
                $ret['list'][$key]['addinfo'] = $arrAnswer['list'][0];
            }            
            $ret['list'][$key]['upNum']   = $upAnswerNum;
            $ret['list'][$key]['collect'] = $collectTopicNum;
        }        
        array_multisort($arrHotDegree, SORT_DESC , $ret['list']);
        $arrRet = array_slice($ret['list'],0,$size);
        return $arrRet;
    }
    
    /**
     * 获取最新的话题，带标签过滤，并加上答案等信息:话题的更新时间取话题时间与答案时间中的最新者
     * @param integer $sightId
     * @param integer $size
     * @return array
     */
    public function getNewTopic($sightId,$size=2,$strTags=''){
        $arrHotDegree     = array();
        $arrTopics        = array();
        $collectTopicNum  = 0;
        $visitTopicNum    = 0;
        
        $redis = Base_Redis::getInstance();
        if(!empty($strTags)){
            $arrTags = explode(",",$strTags);
            foreach ($arrTags as $tag){
                $arrTopics = array_merge($arrTopics,$redis->sGetMembers(Tag_Keys::getTagTopic($tag)));
            }
        }
        
        $listTopic = new Topic_List_Topic();
        $time      = strtotime("-1 month");
        //$listTopic->setFilter(array('sight_id' => $sightId));
        $listTopic->setFilterString("`sight_id`=$sightId and `update_time` > $time");
        $listTopic->setOrder("update_time desc");
        $listTopic->setPagesize($size);
        $ret = $listTopic->toArray();
        foreach($ret['list'] as $key => $val){
            if((!empty($arrTopics)) && (!in_array($val['id'],$arrTopics))){
                unset($ret['list'][$key]);
                continue;
            }                        
            $logicCollect      = new Collect_Logic_Collect();
            $collectTopicNum   = $logicCollect->getLateCollectNum(Collect_Keys::TOPIC, $val['id']); //话题收藏数
            
            //有描述用描述，没描述用答案
            if(isset($val['desc']) &&(!empty($val['desc']))){
                $ret['list'][$key]['addinfo'] = $val['desc'];
            }else{
                $listAnswer        = new Answers_List_Answers();
                $listAnswer->setFilter(array('topic_id' => $val['id']));
                $listAnswer->setOrder("create_time desc");
                $listAnswer->setPagesize(1);
                $arrAnswer = $listAnswer->toArray();
                $ret['list'][$key]['addinfo'] = $arrAnswer['list'][0];
            }            
            $visitTopicNum = $redis->hGet(Topic_Keys::REDIS_TOPIC_VISIT_KEY,$val['id']);
            $ret['list'][$key]['visit']   = $visitTopicNum;
            $ret['list'][$key]['collect'] = $collectTopicNum;
        }        
        return $ret;
    }
    
    
    /**
     * 根据景点ID获取一个月内的话题数
     * @param integer $sightId
     * @return integer
     */
    public function getHotTopicNum($sightId){
        $redis = Base_Redis::getInstance();
        $count = 0;
        $end = time();
        $start = strtotime("-1 month");
        $ret = $redis->zRangeByScore(Sight_Keys::getSightTopicName($sightId),$start,$end);
        return count($count);
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
        $arrRet['answers'] = $arrAnswers['list'];
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