<?php
/**
 * 话题逻辑层
 * @author huwei
 *
 */
class Topic_Logic_Topic extends Base_Logic{
    
    const DEFAULT_SIZE  = 2;
    
    const CONTENT_LEN   = 75;
    
    const DEFAULT_DAYS  = 30;    
    
    public function __construct(){
        
    }
    
    /**
     * 根据景点Id获取话题ID数组，如果景点ID为空，则返回所有话题ID
     * @param string $sightId
     * @return string
     */
    public function getTopicIdBySight($sightId = ''){
        $ret       = array();
        $redis = Base_Redis::getInstance();
        $ret   = $redis->sMembers(Sight_Keys::getSightTopicKey($sightId));
        if(!empty($ret)){
            $strTopicId = implode(",", $ret);
            return $strTopicId;
        }
        //获取景点的所有话题
        $redis->delete(Sight_Keys::getSightTopicKey($sightId));
        $listTopic = new Sight_List_Topic();
        $listTopic->setPagesize(PHP_INT_MAX);
        if(!empty($sightId)){
            $listTopic->setFilter(array('sight_id' => $sightId));
        }
        $arrRet = $listTopic->toArray();
        foreach ($arrRet['list'] as $val){
            $objTopic = new Topic_Object_Topic();
            $objTopic->fetch(array('id' => $val['topic_id']));
            if($objTopic->status == Topic_Type_Status::PUBLISHED){
                $ret[] = $val['topic_id'];
                $redis->sAdd(Sight_Keys::getSightTopicKey($sightId),$val['topic_id']);
            }
        }
        $strTopicId = implode(",", $ret);       
        return $strTopicId;
    }
    
    /**
     * 获取最热门的话题，带景点ID、时间范围、大小、标签过滤，并加上答案等信息
     * @param integer $sightId
     * @param integer $size
     * @return array
     */
    public function getHotTopic($sightId,$period=self::DEFAULT_DAYS,$size=self::DEFAULT_SIZE,$strTags=''){
        $strTopicId = $this->getTopicIdBySight($sightId);
        $model      = new TopicModel();
        $arrRet     = $model->getHotTopics($strTopicId,$strTags,$size,$period);

        foreach($arrRet as $key => $val){
            $arrRet[$key]['desc'] = Base_Util_String::getSubString($arrRet[$key]['content'],self::CONTENT_LEN);
            unset($arrRet[$key]['content']);
            
            //话题访问人数            
            $arrRet[$key]['visit']   = strval($this->getTotalTopicVistUv($val['id']));
            
            //话题收藏数
            $logicCollect            = new Collect_Logic_Collect();
            $arrRet[$key]['collect'] = strval($logicCollect->getTotalCollectNum(Collect_Type::TOPIC, $val['id']));
            
            //话题来源
            $logicSource = new Source_Logic_Source();         
            $arrRet[$key]['from']    = $logicSource->getSourceName($val['from']);
            
            if(!empty($val['image'])){
                $arrRet[$key]['image']  = Base_Image::getUrlByHash($val['image']);
            }                       
        }
        return $arrRet;
    }
    
    /**
     * 获取最新的话题，带景点、时间范围、大小、标签过滤，并加上答案等信息:话题的更新时间取话题时间与答案时间中的最新者
     * @param integer $sightId
     * @param integer $size
     * @return array
     */
    public function getNewTopic($sightId,$period=self::DEFAULT_DAYS,$page,$pageSize,$strTags=''){     
        $strTopicId = $this->getTopicIdBySight($sightId);
        $model      = new TopicModel();
        $arrRet     = $model->getNewTopics($strTopicId, $strTags, $page, $pageSize);

        foreach($arrRet as $key => $val){         
            $arrRet[$key]['desc'] = Base_Util_String::getSubString($val['content'],self::CONTENT_LEN);
            unset($arrRet[$key]['content']);

            //话题收藏数
            $logicCollect      = new Collect_Logic_Collect();        

            $arrRet[$key]['visit']   = $this->getTotalTopicVistPv($val['id'], $period);
            $arrRet[$key]['collect'] = strval($logicCollect->getTotalCollectNum(Collect_Keys::TOPIC, $val['id']));
            
            //话题来源
            $logicSource             = new Source_Logic_Source();
            $arrRet[$key]['from']    = $logicSource->getSourceName($val['from']);
            
            if(!empty($val['image'])){
                $arrRet[$key]['image']  = Base_Image::getUrlByHash($val['image']);
            }
        }        
        return $arrRet;
    }
    
    
    /**
     * 获取话题详细信息
     * @param integer $topicId
     * @return Topic_Object_Topic
     */
    public function getTopicDetail($topicId,$device_id){
        $objTopic = new Topic_Object_Topic();
        $objTopic->fetch(array('id' => $topicId));
        $arrRet = $objTopic->toArray();
        $logicComment          = new Comment_Logic_Comment();
        $arrRet['commentNum']  = $logicComment->getTotalCommentNum($topicId);
               
        //话题来源
        $logicSource = new Source_Logic_Source();
        $arrRet['from']    = $logicSource->getSourceName($objTopic->from);
               
        //访问数
        $logicTopic            = new Topic_Logic_Topic();
        $arrRet['visitNum']  = strval($this->getTotalTopicVistPv($topicId)+1);
        
        //添加redis中话题访问次数统计，直接让其失效，下次从数据库中获取
        $redis = Base_Redis::getInstance();
        $redis->hDel(Topic_Keys::getTopicVisitKey(),Topic_Keys::getTotalKey($topicId));
        $redis->hDel(Topic_Keys::getTopicVisitKey(),Topic_Keys::getLateKey($topicId,'*'));
        
        //这里需要更新一下热度
        return $arrRet;
    }    
    
    /**
     * 获取某个用户的所有话题
     * @param integer $deviceId
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getUserTopic($deviceId,$page,$pageSize){
        $logicUser = new User_Logic_User();
        $userId    = $logicUser->getUserId($deviceId);
        $listTopic = new Topic_List_Topic();
        $listTopic->setFilter(array('user_id' => $userId));
        $listTopic->setPage($page);
        $listTopic->setPagesize($pageSize);
        return $listTopic->toArray();
    }
    
    /**
     * 根据景点ID获取话题信息
     * @param integer $sightId
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getTopicBySight($sightId,$page=1,$pageSize=PHP_INT_MAX){
        $arrRet = array();
        $listSightTopic = new Sight_List_Topic();
        $listSightTopic->setFields(array('topic_id'));
        $listSightTopic->setFilter(array('sight_id' => $sightId));
        $listSightTopic->setPage($page);
        $listSightTopic->setPagesize($pageSize);
        $ret = $listSightTopic->toArray();
        foreach ($ret['list'] as $key => $val){
            $arrRet[] = $val['topic_id'];
        }
        return $arrRet;
    }
    
    /**
     * 获取话题最近的访问人数
     * @param integer $topicId
     * @param string $during
     * @return integer
     */
    public function getLateTopicVistUv($topicId,$during){
        $redis   = Base_Redis::getInstance();
        $from    = strtotime($during." days ago");
        $ret = $redis->hGet(Topic_Keys::getTopicVisitKey($topicId),Topic_Keys::getLateKey($topicId,$during));
        if(!empty($ret)){
            return $ret;
        }
        $list   = new Visit_List_Visit();
        $list->setFields(array('device_id'));
        $filter = "'obj_id' = $topicId and 'create_time' >= $from and type = ".Visit_Type::TOPIC; 
        $list->setPagesize(PHP_INT_MAX);
        $list->setFilterString($filter);
        $arrRet = $list->toArray();
        $arrTotal = array();
        foreach($arrRet['list'] as $val){
            if(!in_array($val,$arrTotal)){
                $arrTotal[] = $val;
            }
        }
        $redis->hSet(Topic_Keys::getTopicVisitKey($topicId),Topic_Keys::getLateKey($topicId,$during),count($arrTotal));
        return count($arrTotal);
    }
    
    /**
     * 获取话题最近的访问次数
     * @param integer $topicId
     * @param string $during
     * @return integer
     */
    public function getLateTopicVistPv($topicId,$during){
        $redis   = Base_Redis::getInstance();
        $from    = strtotime($during);
        $ret = $redis->hGet(Topic_Keys::getTopicVisitKey($topicId),Topic_Keys::getLateKey($topicId,$during));
        if(!empty($ret)){
            return $ret;
        }
        $list   = new Visit_List_Visit();
        $filter = "'obj_id' = $topicId and 'create_time' >= $from and type = ".Visit_Type::TOPIC;
        $list->setPagesize(PHP_INT_MAX);
        $list->setFilterString($filter);
        $arrRet = $list->toArray();
        $redis->hSet(Topic_Keys::getTopicVisitKey($topicId),Topic_Keys::getLateKey($topicId,$during),$arrRet['total']);
        return $arrRet['total'];
    }
    
    /**
     * 获取话题总的访问人数
     * @param integer $topicId
     * @param string $during
     * @return integer
     */
    public function getTotalTopicVistUv($topicId){
        $redis   = Base_Redis::getInstance();
        $ret = $redis->hGet(Topic_Keys::getTopicVisitKey($topicId),Topic_Keys::getTotalKey($topicId));
        if(!empty($ret)){
            return $ret;
        }
        $list   = new Visit_List_Visit();
        $list->setFields(array('device_id'));
        $list->setFilter(array('obj_id' => $topicId,'type' => Visit_Type::TOPIC));
        $list->setPagesize(PHP_INT_MAX);
        $arrRet = $list->toArray();
        $arrTotal = array();
        foreach($arrRet['list'] as $val){
            if(!in_array($val,$arrTotal)){
                $arrTotal[] = $val;
            }
        }
        $redis->hSet(Topic_Keys::getTopicVisitKey($topicId),Topic_Keys::getTotalKey($topicId),count($arrTotal));
        return count($arrTotal);
    }
    
    /**
     * 获取话题总的访问次数
     * @param integer $topicId
     * @param string $during
     * @return integer
     */
    public function getTotalTopicVistPv($topicId){
        $redis   = Base_Redis::getInstance();
        $ret = $redis->hGet(Topic_Keys::getTopicVisitKey($topicId),Topic_Keys::getTotalKey($topicId));
        if(!empty($ret)){
            return $ret;
        }
        $list   = new Visit_List_Visit();
        $list->setFilter(array('obj_id' => $topicId,'type' => Visit_Type::TOPIC));
        $list->setPagesize(PHP_INT_MAX);
        $arrRet = $list->toArray();
        $redis->hSet(Topic_Keys::getTopicVisitKey($topicId),Topic_Keys::getTotalKey($topicId),$arrRet['total']);
        return $arrRet['total'];
    }
    
    /**
     * 获取话题的热度:热度=话题收藏数+评论数+话题浏览量
     * @param integer $sightId，景点ID
     * @param string $period,时间段
     * @return integer，话题热度
     */
    public function getTopicHotDegree($topicId,$period){
        $redis = Base_Redis::getInstance();
        $ret   = $redis->hGet(Topic_Keys::getTopicHot(),Topic_Keys::getLateKey($topicId));
        if(!empty($ret)){
            return $ret;
        }
        //话题最近收藏数
        $logicCollect      = new Collect_Logic_Collect();
        $collectTopicNum   = $logicCollect->getLateCollectNum(Collect_Keys::TOPIC, $topicId,$period);
    
        //话题最近访问人数
        $visitTopicUv      = $this->getLateTopicVistUv($topicId, $period);
    
        //最近评论次数
        $logicComment      = new Comment_Logic_Comment();
        $commentNum        = $logicComment->getLateCommentNum($topicId, $period);
        
        $hotDegree         = $collectTopicNum + $commentNum + $visitTopicUv;
        $redis->hSet(Topic_Keys::getTopicHot(),Topic_Keys::getLateKey($topicId),$hotDegree);
        return $hotDegree;
    }
    

    /**
     * 对话题中的标题内容进行模糊查询
     * @param array $arrParam，条件数组,$arrParam['title']中包含模糊匹配词
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function search($arrParam,$page,$pageSize){
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
        if(isset($arrParam['title'])){
            $filter = "`title` like '%".$arrParam['title']."%' and ";
            unset($arrParam['title']);
        }
    
        foreach ($arrParam as $key => $val){
            $filter .= "`".$key."` = $val and ";
        }
        if(!empty($sight_id)){
            $strTopics = implode(",",$arrTopics);
            if(empty($strTopics)){
                $strTopics = -1;
            }
            $filter .= "`id` in ($strTopics)";
        }else{
            if(!empty($filter)){
                $filter  = substr($filter,0,-4);
            }
        }
        if(!empty($filter)){
            $listTopic->setFilterString($filter);
        }
        if(isset($arrParam['id'])){
            $listTopic->setFields(array('id','title','from','content','url','image','status','create_time','update_time'));
        }else{
            $listTopic->setFields(array('id','title','from','url','image','status','create_time','update_time'));
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
            if(!empty($sight_id)){
                $listSighttopic->setFilter(array('topic_id' =>$val['id'],'sight_id' =>$sight_id));
            }else{
                $listSighttopic->setFilter(array('topic_id' =>$val['id']));
            }
            $listSighttopic->setPagesize(PHP_INT_MAX);
            $arrSighttopic = $listSighttopic->toArray();
            $arrRet['list'][$key]['sights'] = $arrSighttopic['list'];
             
            $logicCollect      = new Collect_Logic_Collect();           
            $arrRet['list'][$key]['collect'] = $logicCollect->getTotalCollectNum(Collect_Keys::TOPIC, $val['id']);
            
            $logicComment      = new Comment_Logic_Comment();
            $arrRet['list'][$key]['comment'] = $logicComment->getTotalCommentNum($val['id']);
        }
        return $arrRet;
    }
    
    
    public function getTopicById($id){
        $objTopic = new Topic_Object_Topic();
        $objTopic->fetch(array('id' => $id));
        $ret = $objTopic->toArray();
        
        $listTopictag = new Topic_List_Tag();
        $listTopictag->setFilter(array('topic_id' => $id));
        $listTopictag->setPagesize(PHP_INT_MAX);
        $arrTag = $listTopictag->toArray();
        $ret['tags'] = $arrTag['list'];
        
        $listSighttopic = new Sight_List_Topic();
        $listSighttopic->setFilter(array('topic_id' =>$id));
        $listSighttopic->setPagesize(PHP_INT_MAX);
        $arrSighttopic = $listSighttopic->toArray();
        $ret['sights'] = $arrSighttopic['list'];
         
        $logicCollect      = new Collect_Logic_Collect();
        $ret['collect'] = $logicCollect->getTotalCollectNum(Collect_Keys::TOPIC, $id);
        
        $logicComment      = new Comment_Logic_Comment();
        $ret['comment'] = $logicComment->getTotalCommentNum($id);
        return $ret;
    }
    
    public function delTopic($id){
        $redis = Base_Redis::getInstance();
        //删除话题
        $objTopic = new Topic_Object_Topic();
        $objTopic->fetch(array('id' => $id));
        
        //删除图片
        if(!empty($objTopic->image)){
            $oss      = Oss_Adapter::getInstance();
            $filename = $objTopic->image . '.jpg';
            $oss->remove($filename);
        }        
        $ret = $objTopic->remove();
 
        //删除评论
        $listComment = new Comment_List_Comment();
        $listComment->setFilter(array('topic_id' => $id));
        $listComment->setPagesize(PHP_INT_MAX);
        $arrComment = $listComment->toArray();
        foreach ($arrComment['list'] as $index => $val){
            $objComment = new Comment_Object_Comment();
            $objComment->fetch(array('id' => $val['id']));
            $objComment->remove();
        }
    
        //删除话题标签关系
        $listTopicTag = new Topic_List_Tag();
        $listTopicTag->setFilter(array('topic_id' => $id));
        $listTopicTag->setPagesize(PHP_INT_MAX);
        $arrTopicTag = $listTopicTag->toArray();
        foreach ($arrTopicTag['list'] as  $val){
            $objTopicTag = new Topic_Object_Tag();
            $objTopicTag->fetch(array('id' => $val['id']));
            $redis->sRemove(Topic_Keys::getTopicTagKey($id),$objTopicTag->tagId);
            $redis->hIncrBy(Tag_Keys::getTagInfoKey($objTopicTag->tagId),'num',-1);
            $objTopicTag->remove();
        }
        //删除话题景点关系
        $listSightTopic = new Sight_List_Topic();
        $listSightTopic->setFilter(array('topic_id' => $id));
        $listSightTopic->setPagesize(PHP_INT_MAX);
        $arrSightTopic = $listSightTopic->toArray();
        foreach ($arrSightTopic['list'] as $data){
            $objSightTopic = new Sight_Object_Topic();
            $objSightTopic->fetch(array('id' => $data['id']));
            $redis->sRemove(Sight_Keys::getSightTopicKey($objSightTopic->sightId),$id);
            $num = $redis->sCard(Sight_Keys::getSightTopicKey($objSightTopic->sightId));
            if($num == 0){
                $model = new SightModel();
                $model->eddSight($objSightTopic->sightId, array('hastopic' => 0));
            }
            $objSightTopic->remove();
        }
    
        //更新redis统计数据
        $redis->hDel(Topic_Keys::getTopicVisitKey(),Topic_Keys::getLateKey($id, '*'));
        $redis->hDel(Topic_Keys::getTopicVisitKey(),Topic_Keys::getTotalKey($id));
        $redis->delete(Topic_Keys::getTopicTagKey(),$id);
        $redis->hDel(Topic_Keys::getTopicHotKey(),Topic_Keys::getLateKey($id, '*'));
        $redis->hDel(Topic_Keys::getTopicHotKey(),Topic_Keys::getTotalKey($id));
        return $ret;
    }
    
    /**
     * 获取话题列表信息
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function getTopicList($page, $pageSize){
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
     * 根据条件获取话题信息
     * @param array $arrParam:参数数组，如：array('sight_id'=>1);
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public  function queryTopic($arrParam,$page,$pageSize){
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
     * 修改话题状态接口
     * @param integer $topicId
     * @param integer $status
     * @return boolean
     */
    /*public function changeTopicStatus($topicId,$status){
        $objTopic = new Topic_Object_Topic();
        $objTopic->fetch(array('id' => $topicId));
        $objTopic->status = $status;
        return $objTopic->save();
    }*/
    
    public function addTopic($arrInfo){
        $objTopic = new Topic_Object_Topic();
        $redis = Base_Redis::getInstance();
        foreach ($arrInfo as $key => $val){
            $key  = $this->getprop($key);
            $objTopic->$key = $val;
        }
        $logicUser = new User_Logic_Login();
        $userId = $logicUser->checkLogin();
        if(!empty($userId)){
            $objTopic->userId = $userId;
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
                if($objTopic->status == Topic_Type_Status::PUBLISHED){
                    $redis->sAdd(Sight_Keys::getSightTopicKey($val),$objTopic->id);
                    $num = $redis->sCard(Sight_Keys::getSightTopicKey($val));
                    if($num == 1){
                        $model = new SightModel();
                        $model->eddSight($val, array('hastopic' => 1));
                    }
                }                
            }
        }
        return $objTopic->id;        
    }
    
    public function editTopic($topicId,$arrInfo){
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
    
        //话题状态修改，对应改变缓存中信息
        if(isset($arrInfo['status'])){
            $listSightTopic = new Sight_List_Topic();
            $listSightTopic->setFilter(array('topic_id' => $topicId));
            $listSightTopic->setPagesize(PHP_INT_MAX);
            $arrList = $listSightTopic->toArray();
            foreach($arrList['list'] as $key => $val){
                if($arrInfo['status'] == Topic_Type_Status::PUBLISHED){
                    $redis->sAdd(Sight_Keys::getSightTopicKey($val['sight_id']),$topicId);
                }else{
                    $redis->sRemove(Sight_Keys::getSightTopicKey($val['sight_id']),$topicId);
                }
            }
        }
                 
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
                    $redis->sRemove(Sight_Keys::getSightTopicKey($objSightTopic->sightId),$topicId);
                    $num = $redis->sCard(Sight_Keys::getSightTopicKey($objSightTopic->sightId));
                    if($num == 0){
                        $model = new SightModel();
                        $model->eddSight($objSightTopic->sightId, array('hastopic' => 0));
                    }
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
                    if($objTopic->status == Topic_Type_Status::PUBLISHED){
                       $redis->sAdd(Sight_Keys::getSightTopicKey($objSightTopic->sightId),$topicId);
                       $num = $redis->sCard(Sight_Keys::getSightTopicKey($objSightTopic->sightId));
                       if($num == 1){
                           $model = new SightModel();
                           $model->eddSight($objSightTopic->sightId, array('hastopic' => 1));
                       }
                    }                    
                }
            }
        }
        return $ret;
    }
    
    /**
     * 根据话题名搜索话题，并且结果不包含标签、景点信息
     * @param string $title
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public function searchTopic($title,$page,$pageSize){
        $listTopic     = new Topic_List_Topic();
        $logicCollect  = new Collect_Logic_Collect();
        $filter = "`title` like '%".$title."%'";
        $listTopic->setFields(array('id','title','image'));
        $listTopic->setFilterString($filter);
        $listTopic->setPage($page);
        $listTopic->setPagesize($pageSize);
        $arrRet = $listTopic->toArray();
        
        foreach ($arrRet['list'] as $key => $val){
            $arrRet['list'][$key]['name'] = $val['title'];
            unset($arrRet['list'][$key]['title']);
        }
        return $arrRet;
    }
}