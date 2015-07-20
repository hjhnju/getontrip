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
            $listTopictag = new Topic_List_Tag();
            $listTopictag->setFilter(array('topic_id' => $val['id']));
            $listTopictag->setPagesize(PHP_INT_MAX);
            $arrTag = $listTopictag->toArray();
            $arrRet['list'][$key]['tags'] = $arrTag['list'];
            
            $listSighttopic = new Sight_List_Topic();
            $listSighttopic->setFilter(array('topic_id' =>$val['id']));
            $listSighttopic->setPagesize(PHP_INT_MAX);
            $arrSighttopic = $listSighttopic->toArray();
            $arrRet['list'][$key]['sights'] = $arrSighttopic['list'];
        }
        return $arrRet;
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
        $arrRet = $listTopic->toArray();
        foreach ($arrRet['list'] as $key => $val){
            $listTopictag = new Topic_List_Tag();
            $listTopictag->setFilter(array('topic_id' => $val['id']));
            $arrTag = $listTopictag->toArray();
            $arrRet['list'][$key]['tags'] = $arrTag['list'];
        }
    }
    
    /**
     * 接口3：Topic_Api::editTopic($topicId,$arrInfo)
     * 话题编辑接口
     * @param integer $topicId
     * @param array $arrInfo,话题信息，注意标签是个数组,eg:array("content"=>"xxx",'tags'=>array(1,2));
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
        $ret = $objTopic->save();
        
        if(isset($arrInfo['tags'])){
            $listTopicTag = new Topic_List_Tag();
            $listTopicTag->setFilter(array('topic_id' => $topicId));
            $listTopicTag->setPagesize(PHP_INT_MAX);
            $arrList = $listTopicTag->toArray();
            foreach($arrList['list'] as $key => $val){
                $objTopicTag = new Topic_Object_Tag();
                $objTopicTag->fetch(array('id' => $val['id']));
                if(!in_array($objTopicTag->tagId,$arrInfo['tags'])){
                    $objTopicTag->remove();
                }
            }
            
            foreach($arrInfo['tags'] as $tag){
                $objTopicTag = new Topic_Object_Tag();
                $objTopicTag->fetch(array('topic_id' => $topicId,'tag_id' =>$tag));
                if(empty($objTopicTag->id)){
                    $objTopicTag->tagId   = $tag;
                    $objTopicTag->topicId = $topicId;
                    $objTopicTag->save();
                }
            }
        }
        return $ret;
    }
    
    /**
     * 接口4：Topic_Api::addTopic($arrInfo,$arrTags)
     * 添加话题接口
     * @param array $arrInfo,话题信息,eg:array('name'=>xxx,'tags'=>array(1,2))
     * @return boolean
     */
    public static function addTopic($arrInfo){
        $objTopic = new Topic_Object_Topic();
        $redis = Base_Redis::getInstance();
        foreach ($arrInfo as $key => $val){
            $objTopic->$key = $val;
        }
        $ret = $objTopic->save();
        if(isset($arrInfo['tags'])){
            foreach($arrInfo['tags'] as $val){
                $objTopictag = new Topic_Object_Tag();
                $objTopictag->topicId = $objTopic->id;
                $objTopictag->tagId   = $val;
                $objTopictag->save();
    
                $redis->sAdd(Topic_Keys::getTopicTagKey($objTopic->id),$val);
            }
        }
        return $ret;
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
    

    /**
     * 接口6：Topic_Api::search($query,$page,$pageSize)
     * 对话题中的标题内容进行模糊查询
     * @param string $query
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function search($query,$page,$pageSize){
        $listTopic = new Topic_List_Topic();
        $listTopic->setFilterString("`title` like '%".$query."%'");
        $listTopic->setPage($page);
        $listTopic->setPagesize($pageSize);
        return $listTopic->toArray();
    }
    
    public static function delTopic($id){
        $objTopic = new Topic_Object_Topic();
        $objTopic->fetch(array('id' => $id));
        $objTopic->status = Topic_Type_Status::DELETED;
        return $objTopic->save();
    }
}