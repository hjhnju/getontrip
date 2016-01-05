<?php
/**
 * 话题接口
 * @author huwei
 */
class Topic_Api{
        
    /**
     * 接口1：Topic_Api::editTopic($topicId,$arrInfo)
     * 话题编辑接口
     * @param integer $topicId
     * @param array $arrInfo,话题信息，注意标签、景点是数组,eg:array("content"=>"xxx",'tags'=>array(1,2),'sights'=>array(1));
     * @return boolean
     */
    public static function editTopic($topicId,$arrInfo){
        $logic = new Topic_Logic_Topic();
        return $logic->editTopic($topicId, $arrInfo);
    }
    
    /**
     * 接口2：Topic_Api::addTopic($arrInfo)
     * 添加话题接口
     * @param array $arrInfo,话题信息，注意标签及景点是数组,eg:array('title'=>xxx,'tags'=>array(1,2))
     * @return boolean
     */
    public static function addTopic($arrInfo){
        $logic = new Topic_Logic_Topic();
        return $logic->addTopic($arrInfo);
    }
    
    /**
     * 接口3：Topic_Api::changeTopicStatus($topicId,$status)
     * 修改话题状态接口
     * @param integer $topicId
     * @param integer $status
     * @return boolean
     */
    /*public static function changeTopicStatus($topicId,$status){
        $logic = new Topic_Logic_Topic();
        return $logic->changeTopicStatus($topicId, $status);
    }*/
    
    /**
     * 接口3：Topic_Api::getTopicById($id)
     * 根据话题ID获取话题详情
     * @param integer $id
     * @return array
     */
    public static function getTopicById($id){
        $logic = new Topic_Logic_Topic();
        return $logic->getTopicById($id);
    }
    

    /**
     * 接口4：Topic_Api::search($arrParam,$page,$pageSize)
     * 对话题中的标题内容进行模糊查询
     * @param array $arrParam，条件数组,$arrParam['title']中包含模糊匹配词
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function search($arrParam,$page,$pageSize){
        $logic = new Topic_Logic_Topic();
        return $logic->search($arrParam, $page, $pageSize);
    }
    
    /**
     * 接口5：Topic_Api::delTopic($id)
     * 删除话题接口
     * @param integer $id
     * @return boolean
     */
    public static function delTopic($id){
        $logic = new Topic_Logic_Topic();
        return $logic->delTopic($id);
    }
    
    /**
     * 接口6：Topic_Api::getTopicNum($arrInfo)
     * 根据条件获取话题数量
     * @param array $arrInfo,eg:array('sightId'=>1,'status'=>xxx);
     * @param integer
     */
    public static function getTopicNum($arrInfo){
        $logic = new Topic_Logic_Topic();
        return $logic->getTopicNum($arrInfo);
    }
    
    /**
     * 接口7：Topic_Api::getHotTopic($page,$pageSize)
     * 获取热门话题
     * @param integer $page
     * @param integer $pageSize
     */
    public static function getHotTopic($page,$pageSize){
        $logic = new Topic_Logic_Topic();
        return $logic->getAllHotTopic($page,$pageSize);
    }
}