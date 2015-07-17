<?php
/**
 * 话题接口
 * @author huwei
 */
class Topic_Api{
    
    /**
     * 接口1：Topic_Api::getTopicList($page, $pageSize)
     * 获取话题列表信息
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function getTopicList($page, $pageSize){
        $listTopic = new Topic_List_Topic();
        $listTopic->setPage($page);
        $listTopic->setPagesize($pageSize);
        $arrRet = $listTopic->toArray();
        foreach ($arrRet['list'] as $key => $val){
            $listTopictag = new Topictag_List_Topictag();
            $listTopictag->setFilter(array('topic_id' => $val['id']));
            $arrTag = $listTopictag->toArray();
            $arrRet['list'][$key]['tags'] = $arrTag['list'];
        }
        return $arrRet['list'];
    }
    
    /**
     * 接口2：Topic_Api::queryTopic($arrParam,$page,$pageSize)
     * 根据条件获取话题信息
     * @param array $arrParam:参数数组，如：array('sight_id'=>1);
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function queryTopic($arrParam,$page,$pageSize){
        $listTopic = new Topic_List_Topic();
        $listTopic->setFilter($arrParam);
        $listTopic->setPage($page);
        $listTopic->setPagesize($pageSize);
        return $listTopic->toArray();
    }
    
    /**
     * 接口3：Topic_Api::editTopic($topicId,$arrInfo)
     * 话题编辑接口
     * @param integer $topicId
     * @param array $arrInfo,话题信息,eg:array("content"=>"xxx");
     * @return boolean
     */
    public static function editTopic($topicId,$arrInfo){
        $objTopic = new Topic_Object_Topic();
        $objTopic->fetch(array('id' => $topicId));
        if(empty($objTopic->id)){
            return false;
        }
        foreach ($arrInfo as $key => $val){
            $objTopic->$key = $val;
        }
        return $objTopic->save();
    }
    
    /**
     * 接口4：Topic_Api::addTopic($arrInfo)
     * 添加话题接口
     * @param array $arrInfo,话题信息,eg:array('name'=>xxx,....)
     * @return boolean
     */
    public static function addTopic($arrInfo){
        $objTopic = new Topic_Object_Topic();
        foreach ($arrInfo as $key => $val){
            $objTopic->$key = $val;
        }
        return $objTopic->save();
    }
    
    /**
     * 接口5：Topic_Api::changeTopicStatus($topicId,$status)
     * 修改话题状态接口
     * @param integer $topicId
     * @param integer $status
     * @return boolean
     */
    public static function changeTopicStatus($topicId,$status){
        $objTopic = new Topic_Object_Topic();
        $objTopic->fetch(array('id' => $topicId));
        $objTopic->status = $status;
        return $objTopic->save();
    }
}