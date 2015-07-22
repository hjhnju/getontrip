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
    public static function queryTopic($arrParam,$page,$pageSize){
        $listTopic = new Topic_List_Topic();
        $arrTopics = array();
        $filter    = '';
        $sight_id  = '';
        if(isset($arrParam['sight_id'])){
            $logic = new Topic_Logic_Topic();
            $arrTopics = $logic->getTopicBySight($arrParam['sight_id']);
            $sight_id  = $arrParam['sight_id'];
            unset($arrParam['sight_id']);
        }
        foreach ($arrParam as $key => $val){
            $filter = "`".$key."`=$val and ";
        }
        if(!empty($arrTopics)){
            $strTopics = implode(",",$arrTopics);
            $filter .= "`id` in ($strTopics)";
        }else{
            $filter = substr($filter,0,-4);
        }
        
        $listTopic->setFilterString($filter);
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
            if(!empty($sight_id)){
                $listSighttopic->setFilter(array('topic_id' =>$val['id'],'sight_id' =>$sight_id));
            }else{
                $listSighttopic->setFilter(array('topic_id' =>$val['id']));
            }           
            $listSighttopic->setPagesize(PHP_INT_MAX);
            $arrSighttopic = $listSighttopic->toArray();
            $arrRet['list'][$key]['sights'] = $arrSighttopic['list'];
        }
        return $arrRet;
    }
    
    /**
     * 接口3：Topic_Api::editTopic($topicId,$arrInfo)
     * 话题编辑接口
     * @param integer $topicId
     * @param array $arrInfo,话题信息，注意标签、景点是数组,eg:array("content"=>"xxx",'tags'=>array(1,2),'sights'=>array(1));
     * @return boolean
     */
    public static function editTopic($topicId,$arrInfo){
        $objTopic = new Topic_Object_Topic();
        $redis    = Base_Redis::getInstance();
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
                    $redis->sRemove(Topic_Keys::getTopicTagKey($topicId),$objTopicTag->tagId);
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
                    $redis->sAdd(Topic_Keys::getTopicTagKey($topicId),$objTopicTag->tagId);
                }
            }
        }
        
        if(isset($arrInfo['sights'])){
            $listSightTopic = new Sight_List_Topic();
            $listSightTopic->setFilter(array('topic_id' => $topicId));
            $listSightTopic->setPagesize(PHP_INT_MAX);
            $arrList = $listSightTopic->toArray();
            foreach($arrList['list'] as $key => $val){
                $objSightTopic = new Sight_Object_Topic();
                $objSightTopic->fetch(array('id' => $val['id']));
                if(!in_array($objSightTopic->sightId,$arrInfo['sights'])){
                    $redis->zDelete(Sight_Keys::getSightTopicName($objSightTopic->sightId),$topicId);
                    $objSightTopic->remove();
                }
            }
        
            foreach($arrInfo['sights'] as $sight){
                $objSightTopic = new Sight_Object_Topic();
                $objSightTopic->fetch(array('topic_id' => $topicId,'sight_id' =>$sight));
                if(empty($objSightTopic->id)){
                    $objSightTopic->sightId = $sight;
                    $objSightTopic->topicId = $topicId;
                    $objSightTopic->save();
                    $redis->zAdd(Sight_Keys::getSightTopicName($objSightTopic->sightId),time(),$topicId);
                }
            }
        }
        return $ret;
    }
    
    /**
     * 接口4：Topic_Api::addTopic($arrInfo)
     * 添加话题接口
     * @param array $arrInfo,话题信息，注意标签及景点是数组,eg:array('name'=>xxx,'tags'=>array(1,2))
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
        if(isset($arrInfo['sights'])){
            foreach($arrInfo['sights'] as $val){
                $objSightTopic = new Sight_Object_Topic();
                $objSightTopic->topicId = $objTopic->id;
                $objSightTopic->sightId = $val;
                $objSightTopic->save();
        
                $redis->zAdd(Sight_Keys::getSightTopicName($val),time(),$objTopic->id);
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
     * 接口6：Topic_Api::search($arrParam,$page,$pageSize)
     * 对话题中的标题内容进行模糊查询
     * @param array $arrParam，条件数组,$arrParam['title']中包含模糊匹配词
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function search($arrParam,$page,$pageSize){
        $arrTopics = array();
        $filter    = '';
        $sight_id  = '';
        if(isset($arrParam['sight_id'])){
            $logic = new Topic_Logic_Topic();
            $arrTopics = $logic->getTopicBySight($arrParam['sight_id']);
            $sight_id  = $arrParam['sight_id'];
            unset($arrParam['sight_id']);
        }        
        
        $listTopic = new Topic_List_Topic();
        if(isset($arrParam['content'])){
            $filter = "`content` like '%".$arrParam['content']."%' and ";
            unset($arrParam['content']);
        }        
        
        foreach ($arrParam as $key => $val){
            $filter .= "`".$key."` = $val and ";
        }
        if(!empty($arrTopics)){
            $strTopics = implode(",",$arrTopics);
            $filter .= "`id` in ($strTopics)";
        }else{
            if(!empty($filter)){
                $filter  = substr($filter,0,-4);
            }
        }
        if(!empty($filter)){
            $listTopic->setFilterString($filter);
        }
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
        $listTopic->setFilterString("");
    }
    
    /**
     * 接口7：Topic_Api::delTopic($id)
     * 删除话题接口
     * @param integer $id
     * @return boolean
     */
    public static function delTopic($id){
        $redis = Base_Redis::getInstance();
        //删除话题
        $objTopic = new Topic_Object_Topic();
        $objTopic->fetch(array('id' => $id));
        $objTopic->status = Topic_Type_Status::DELETED;
        $ret =  $objTopic->save();
        
        //删除答案
        $listAnswers = new Answers_List_Answers();
        $listAnswers->setFilter(array('topic_id' => $id));
        $listAnswers->setPagesize(PHP_INT_MAX);
        $arrAnswers = $listAnswers->toArray();
        foreach ($arrAnswers['list'] as $index => $val){
            $objAnswer = new Answers_Object_Answers();
            $objAnswer->fetch(array('id' => $val['id']));
            $objAnswer->status = Answers_Type_Status::DELETED;
            $objAnswer->save();
        }
        
        //删除话题标签关系
        $listTopicTag = new Topic_List_Tag();
        $listTopicTag->setFilter(array('topic_id' => $id));
        $listTopicTag->setPagesize(PHP_INT_MAX);
        $arrTopicTag = $listTopicTag->toArray();
        foreach ($arrTopicTag['list'] as $index => $val){
            $objTopicTag = new Topic_Object_Tag();
            $objTopicTag->fetch(array('id' => $val['id']));
            $objTopicTag->remove();
            $redis->sRemove(Topic_Keys::getTopicTagKey($id),$objTopicTag->tagId);
            $redis->hIncrBy(Tag_Keys::getTagInfoKey($objTopicTag->tagId),'num',-1);
        }
        //删除话题景点关系
        $listSigtTopic = new Sight_List_Topic();
        $listSigtTopic->setFilter(array('topic_id' => $id));
        $listSigtTopic->setPagesize(PHP_INT_MAX);
        $arrSigtTopic = $listSigtTopic->toArray();
        foreach ($arrSigtTopic['list'] as $index => $val){
            $objSightTopic = new Sight_Object_Topic();
            $objSightTopic->fetch(array('id' => $val['id']));
            $objAnswer->remove();
            $redis->zDelete(Sight_Keys::getSightTopicName($objSightTopic->sightId),$id);
        }
        
        //更新redis统计数据
        $redis->hDel(Topic_Keys::getTopicVisitKey(),$id);
        $redis->delete(Topic_Keys::getTopicTagKey($id));
        return $ret;
    }
}