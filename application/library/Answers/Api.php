<?php
class Answers_Api{
    
    /**
     * 接口1：Answers_Api::getAnswersByTopic($topicId,$page,$pageSize)
     * 根据话题ID获取所有答案信息
     * @param integer $topicId
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function getAnswersByTopic($topicId,$page,$pageSize){
        $listAnswer = new Answers_List_Answers();
        $listAnswer->setFilter(array('topic_id' => $topicId));
        $listAnswer->setPage($page);
        $listAnswer->setPagesize($pageSize);
        return $listAnswer->toArray();
    }
    
    /**
     * 接口2：Answers_Api::getAnswerById($id)
     * 根据ID获取答案信息
     * @param integer $id
     * @return array
     */
    public static function getAnswerById($id){
        $objAnswer = new Answers_Object_Answers();
        $objAnswer->fetch(array('id' => $id));
        return $objAnswer->toArray();
    }
    
    /**
     * 接口3：Answers_Api::getAnswerList($page,$pageSize)
     * 获取所有答案的列表
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function getAnswerList($page,$pageSize){
        $listAnswer = new Answers_List_Answers();
        $listAnswer->setPage($page);
        $listAnswer->setPagesize($pageSize);
        return $listAnswer->toArray();
    }
    
    /**
     * 接口4：Answers_Api::setAnswerState($id,$state)
     * 设置答案状态
     * @param integer $id
     * @param integer $state
     * @return boolean
     */
    public static function setAnswerState($id,$state){
        $objAnswer = new Answers_Object_Answers();
        $objAnswer->fetch(array('id' => $id));
        $objAnswer->status = $state;
        return $objAnswer->save();
    }
    
    /**
     * 接口5：Answers_Api::delAnswer($id)
     * 删除答案
     * @param integer $id
     * @return boolean
     */
    public static function delAnswer($id){
        $objAnswer = new Answers_Object_Answers();
        $objAnswer->fetch(array('id' => $id));
        return $objAnswer->remove();
    }
}