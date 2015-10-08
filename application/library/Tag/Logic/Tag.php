<?php
class Tag_Logic_Tag extends Base_Logic{
    
    protected $_fileds;
    
    public function __construct(){
        $this->_fileds = array('id', 'name', 'create_user', 'update_user', 'create_time', 'update_time', 'type');
    }
    
    /**
     * 获取标签信息列表
     * @param integer $page
     * @param integer $pageSize
     * @param array   $arrParam
     * @return array
     */
    public function getTagList($page, $pageSize, $arrParam = array()){
        $listTag = new Tag_List_Tag();
        $listTag->setPage($page);
        $listTag->setPagesize($pageSize);
        if(!empty($arrParam)){
            $listTag->setFilter($arrParam);
        }
        return $listTag->toArray();
    }
    
    /**
     * 获取热门标签列表，根据话题所有的标签数量排序作为热度
     * @param integer $size
     * @return array
     */
    public function getHotTags($size){
        $redis    = Base_Redis::getInstance();
        $arrCount = array();
        $listTag  = new Tag_List_Tag();
        $listTag->setPagesize(PHP_INT_MAX);
        $arrTag = $listTag->toArray();
        foreach ($arrTag['list'] as $val){
            $arrCount[] = $redis->hGet(Tag_Keys::getTagInfoKey($val['id']),'num');
        }
        array_multisort($arrCount, SORT_DESC , $arrTag['list']);
        $arrRet = array_slice($arrTag['list'],0,$size);
        return $arrRet;
    }
    
    /**
     * 编辑标签信息
     * @param integer $id
     * @param array $arrInfo
     * @return boolean
     */
    public function editTag($id, $arrInfo){
        $objTag = new Tag_Object_Tag();
        $objTag->fetch(array('id' => $id));
        foreach ($arrInfo as $key => $val){
            $key = $this->getprop($key);
            if(in_array($key,$this->_fileds)){
                $objTag->$key = $val;
            } 
        }
        $ret = $objTag->save();        
        if(isset($arrInfo['name'])){
            $redis = Base_Redis::getInstance();
            $redis->hSet(Tag_Keys::getTagInfoKey($id),'name',$arrInfo['name']);
        }      
        return $ret;
    }
    
    /**
     * 添加标签信息
     * @param array $arrInfo
     * @return boolean
     */
    public function saveTag($arrInfo){
        $objTag       = new Tag_Object_Tag();
        foreach ($arrInfo as $key => $val){
            $key = $this->getprop($key);
            if(in_array($key,$this->_fileds)){
                $objTag->$key = $val;
            } 
        }
        $ret = $objTag->save();
        
        if(isset($arrInfo['name'])){
            $redis = Base_Redis::getInstance();
            $redis->hSet(Tag_Keys::getTagInfoKey($objTag->id),'name',$arrInfo['name']);
        }
        return $ret;
    }
    
    /**
     * 标签删除接口
     * @param integer $id
     * @return boolean
     */
    public function delTag($id){
        $objTag = new Tag_Object_Tag();
        $objTag->fetch(array('id' => $id));
        $ret1 = $objTag->remove();
    
        $listTopictag = new Topic_List_Tag();
        $listTopictag->setFilter(array('tag_id' => $id));
        $listTopictag->setPagesize(PHP_INT_MAX);
        $arrList = $listTopictag->toArray();
        foreach ($arrList['list'] as $val){
            $objTag = new Topic_Object_Tag();
            $objTag->fetch(array('id'=>$val['id']));            
            $objTag->remove();
        }    
        $redis = Base_Redis::getInstance();
        $ret2 = $redis->delete(Tag_Keys::getTagInfoKey($id));
        return $ret1&&$ret2;
    }
    
    /**
     * 根据名称获取标签信息
     * @param string $name
     * @return array
     */
    public function getTagByName($name){
        $objTag = new Tag_Object_Tag();
        $objTag->fetch(array('name' => $name));
        return $objTag->toArray();
    }
    
    /**
     * 根据ID获取标签信息
     * @param string $id
     * @return array
     */
    public function getTagById($id){
        $objTag = new Tag_Object_Tag();
        $objTag->fetch(array('id' => $id));
        return $objTag->toArray();
    }
    
    /**
     * 根据话题ID获取话题标签名
     * @param integer $topicId
     */
    public function getTopicTags($topicId){
        $redis   = Base_Redis::getInstance();
        $arrTags = array();
        $arrTemp = $redis->sGetMembers(Topic_Keys::getTopicTagKey($topicId));
        foreach ($arrTemp as $id){
            $ret = $redis->hGet(Tag_Keys::getTagInfoKey($id),'name');
            if(!$ret){
                $redis->sRem(Topic_Keys::getTopicTagKey($topicId),$id);
            }else{
                $arrTags[] = $ret;
            }
        }
        if(empty($arrTags)){
            $listTopicTag = new Topic_List_Tag();
            $listTopicTag->setFields(array('tag_id'));
            $listTopicTag->setFilter(array('topic_id' => $topicId));
            $listTopicTag->setPagesize(PHP_INT_MAX);
            $ret = $listTopicTag->toArray();
            foreach ($ret['list'] as $val){
                $redis->sAdd(Topic_Keys::getTopicTagKey($topicId),$val['tag_id']);
                $objTag = new Tag_Object_Tag();
                $objTag->fetch(array('id' => $val['tag_id']));
                $arrTags[] = $objTag->name;
                $ret = $redis->hSet(Tag_Keys::getTagInfoKey($val['tag_id']),'name',$objTag->name);
            }
        }
        return $arrTags;
    }
    
    /**
     * 根据景点ID获取标签信息
     * @param integer $sightId
     * @return array
     */
    public function getTagBySight($sightId){
        $arrClassfiyTag = array();
        $arrGeneralTag  = array();
        $arrNormal      = array();
        //通用标签
        $listSightTag = new Sight_List_Tag();
        $listSightTag->setFilter(array('sight_id' => $sightId));
        $listSightTag->setPagesize(PHP_INT_MAX);
        $arrSightTag = $listSightTag->toArray();
        foreach ($arrSightTag['list'] as $val){
            $objTag = new Tag_Object_Tag();
            $objTag->fetch(array('id' => $val['tag_id']));
            $arrGeneralTag[] = $objTag->name;
        }
        //分类标签
        $logicTopic = new Topic_Logic_Topic();
        $strTopics  = $logicTopic->getTopicIdBySight($sightId);
        $arrTopics  = explode(",",$strTopics);
        foreach ($arrTopics as $id){
            $listTopicTag = new Topic_List_Tag();
            $listTopicTag->setFilter(array('topic_id' => $id));
            $listTopicTag->setPagesize(PHP_INT_MAX);
            $arrTag = $listTopicTag->toArray();
            foreach ($arrTag['list'] as $val){
                $objTag = new Tag_Object_Tag();
                $objTag->fetch(array('id' => $val['tag_id']));
                if($objTag->type == Tag_Type_Tag::CLASSIFY){
                    $arrClassfiyTag[$val['tag_id']]['name'] = $objTag->name;
                    $arrClassfiyTag[$val['tag_id']]['num']  = isset($arrClassfiyTag[$val['tag_id']]['num'])?$arrClassfiyTag[$val['tag_id']]['num']+1:1;
                }elseif($objTag->type == Tag_Type_Tag::NORMAL){
                    $arrNormal[$val['tag_id']]['name'] = $objTag->name;
                    $arrNormal[$val['tag_id']]['num']  = isset($arrNormal[$val['tag_id']]['num'])?$arrNormal[$val['tag_id']]['num']+1:1;
                }                
            }  
        }        
        return array(
            'classify' => $arrClassfiyTag,
            'general'  => $arrGeneralTag,
            'normal'   => $arrNormal,
        );
    }
}