<?php
class Answers_Logic_Answers{
    
    const ANONYMOUS = "匿名用户";
    
    public function __construct(){
        
    }
    
    /**
     * 获取答案的详细信息
     * @param integer $answerId
     * @return array
     */
    public function getAnswerDetail($answerId){
        $objAnswer = new Answers_Object_Answers();
        $objAnswer->fetch(array('id' => $answerId));
        if(empty($objAnswer->id)){
            return array();
        }
        $arrRet = $objAnswer->toArray();  
        $arrRet['praise'] = 0;
        $redis = Base_Redis::getInstance();
        $count = $redis->zSize(Praise_Keys::getAnswerInfoKey());
        if(!empty($count)){
            $arrRet['praise'] = $count;
        }     
        return $arrRet;
    }
    
    /**
     * 根据$answerId获取最近的一个答案
     * @param integer $answerId
     * @return array
     */
    public function getNextAnswer($topicId,$answerId){
        $objAnswer = new Answers_Object_Answers();
        $objAnswer->fetch(array('id' => $answerId));
        $create_time = $objAnswer->createTime;
        
        $listAnswer = new Answers_List_Answers();
        $listAnswer->setFilterString("`topic_id` = $topicId and `create_time` < $create_time");
        $listAnswer->setOrder("create_time desc");
        $listAnswer->setPagesize(1);
        $arrRet = $listAnswer->toArray();
        $arrRet['praise'] = 0;
        $redis = Base_Redis::getInstance();
        $count = $redis->zSize(Praise_Keys::getAnswerInfoKey());
        if(!empty($count)){
            $arrRet['praise'] = $count;
        }
        return $arrRet;
    }
    
    /**
     * 获取我的答案数据
     * @param integer $deviceId
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getUserAnswers($deviceId,$page,$pageSize){
        $logicUser   = new User_Logic_User();
        $userId      = $logicUser->getUserId($deviceId);
        $listAnswers = new Answers_Logic_Answers();
        $listAnswers->setFilter(array('user_id' => $userId));
        $listAnswers->setPage($page);
        $listAnswers->setPagesize($pageSize);
        return $listAnswers->toArray();
    }
    
    /**
     * 添加答案信息
     * @param integer $id
     * @param array $info
     */
    public function  addAnswer($deviceId,$info,$showname){
        $objAnswer = new Answers_Object_Answers();
        
        $logicUser = new User_Logic_User();
        $objAnswer->userId = $logicUser->getUserId($deviceId);
        $objAnswer->anonymous = $showname;
        return $objAnswer->save();        
    }
}