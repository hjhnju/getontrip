<?php
/**
 * 话题接口
 * @author huwei
 */
class Topic_Api{
    
    /**
     * 接口1：Topic_Api::getTopicList($page, $pageSize)
     * 获取话题列表
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
    
    public static function editTopic(){
        
    }
    
    public static function addTopic(){
        
    }
    
    public static function changeTopicStatus(){
        
    }
}