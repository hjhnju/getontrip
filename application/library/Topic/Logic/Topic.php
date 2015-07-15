<?php
class Topic_Logic_Topic{
    
    public function __construct(){
        
    }
    
    /**
     * 获取最热门的话题，并加上答案等信息:热度=话题收藏数+答案数+答案点赞数
     * @param integer $sightId
     * @param integer $size
     * @return array
     */
    public function getHotTopic($sightId,$size=2){
        $arrHotDegree     = array();
        $collectTopicNum  = 0;
        $upAnswerNum      = 0;
        $answerNum        = 0;
        $collectAnswerNum = 0;
        $redis = Base_Redis::getInstance();
        $listTopic = new Topic_List_Topic();
        $listTopic->setFilter(array('sight_id' => $sightId));
        $listTopic->setPagesize(PHP_INT_MAX);
        $ret = $listTopic->toArray();
        foreach($ret['list'] as $key => $val){
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
            
            $listAnswer = new Answers_List_Answers();
            $listAnswer->setFilter(array('topic_id' => $val['id']));
            $listAnswer->setOrder("create_time desc");
            $listAnswer->setPagesize(1);
            $arrAnswer = $listAnswer->toArray();
            if(empty($arrAnswer['list'])){
                $ret['list'][$key]['addinfo'] = $val['desc'];
            }else{
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
     * 获取最新的话题，并加上答案等信息
     * @param integer $sightId
     * @param integer $size
     * @return array
     */
    public function getNewTopic($sightId,$size=2){
        $listTopic = new Topic_List_Topic();
        $listTopic->setFilter(array('sight_id' => $sightId));
        $listTopic->setOrder("update_time desc");
        $listTopic->setPagesize($size);
        $ret = $listTopic->toArray();
        return $ret['list'];
    }
    
    public function getTopicById(){
        
    }
}