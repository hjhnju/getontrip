<?php
class Answers_Logic_Answers{
    
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
    public function  addAnswer($id,$info){
        
    }
}