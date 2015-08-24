<?php
class TopicModel{
    
    const MONTH = 30;
    
    const WEEK  = 7;
    
    const CACHE_PAGES = 10;
    
    const REDIS_TIMEOUT = 3600;
    
    const CONTENT_LEN   = 75;
    
    protected $db;
    
    public function __construct(){
        $this->db = Base_Db::getInstance('getontrip');
    }
    
    /**
     * 获取热门话题
     * @param string $strTopicId
     * @param string $strTags
     * @param integer $size
     * @return array
     */
    public function getHotTopicIds($sightId,$strTags,$page,$pageSize,$during){
        $redis = Base_Redis::getInstance();
        $ret   =  '';
        if(empty($strTags) && ($page <= self::CACHE_PAGES)){
            switch ($during){
                case self::MONTH:
                    $ret = $redis->get(Sight_Keys::getHotTopicKey($sightId, $page));
                    break;
                case self::WEEK:
                    $ret = $redis->get(Find_Keys::getFindKey($page));
                    break;
                default :
                    break;
            }
        }
        if(!empty($ret)){
           return json_decode($ret,true); 
        }
                
        $from = ($page-1)*$pageSize;
        if(empty($strTags)){
            if($during == self::MONTH){
                $sql = "SELECT a.id FROM `topic`  a, `sight_topic` b WHERE  a.status = ".Topic_Type_Status::PUBLISHED." and a.id = b.topic_id and b.sight_id = $sightId ORDER BY a.hot2 desc, a.update_time desc limit $from,$pageSize";
            }elseif($during == self::WEEK){
                $sql = "SELECT `id` FROM `topic`  WHERE  `status` = ".Topic_Type_Status::PUBLISHED." ORDER BY `hot1` desc,`update_time` desc limit $from,$pageSize";
            }
        }else{
            if($during == self::MONTH){
                $sql = "SELECT a.id FROM `topic`  a,`topic_tag`  b,`sight_topic` c WHERE a.status = ".Topic_Type_Status::PUBLISHED." a.id=b.topic_id=c.topic_id AND c.sight_id = $sightId AND b.tag_id in(".$strTags.") ORDER by a.hot2 desc, a.update_time desc limit $from,$pageSize";
            }elseif($during == self::WEEK){
                $sql = "SELECT a.id FROM `topic`  a,`topic_tag`  b WHERE a.status = ".Topic_Type_Status::PUBLISHED." a.id=b.topic_id AND b.tag_id in(".$strTags.") ORDER by a.hot1 desc, a.update_time desc limit $from,$pageSize";
            }
        }       
        try {                 	
            $data = $this->db->fetchAll($sql);          
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());          
            return array();
        }
        if(empty($strTags) &&($page <= self::CACHE_PAGES)){
            if($during == self::MONTH){
                $redis->setex(Sight_Keys::getHotTopicKey($sightId,$page),self::REDIS_TIMEOUT,json_encode($data)); 
            }else{
                $redis->setex(Find_Keys::getFindKey($page),self::REDIS_TIMEOUT,json_encode($data));
            }
        }
        return $data;
    }
    
    /**
     * 获取最新话题
     * @param string $strTopicId
     * @param string $strTags
     * @param integer $size
     * @return array
     */
    public function getNewTopicIds($sightId,$strTags,$page,$pageSize){
        $redis = Base_Redis::getInstance();
        $ret   =  '';
        if(($page <= self::CACHE_PAGES)){
            $ret = $redis->get(Sight_Keys::getNewTopicKey($sightId, $page));
        }
        if(!empty($ret)){
            return json_decode($ret,true);
        }
        $from = ($page-1)*$pageSize;
        if(empty($strTags)){
            $sql = "SELECT a.id FROM `topic` a, `sight_topic` b   WHERE  a.id = b.topic_id and b.sight_id = $sightId and a.status = ".Topic_Type_Status::PUBLISHED." ORDER BY a.update_time desc limit $from,$pageSize";
        }else{
            $sql = "SELECT a.id FROM `topic`  a,`topic_tag`  b, `sight_topic` c WHERE a.id=b.topic_id=c.topic_id AND c.sight_id = $sightId AND b.tag_id in(".$strTags.") and a.status = ".Topic_Type_Status::PUBLISHED." ORDER by a.update_time desc limit $from,$pageSize";
        }
        try {
            $data = $this->db->fetchAll($sql);
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());
            return array();
        }
        if(($page <= self::CACHE_PAGES)){
            $redis->setex(Sight_Keys::getNewTopicKey($sightId,$page),self::REDIS_TIMEOUT,json_encode($data));
        }
        return $data;
    }
    
    /**
     * 供需要进行缓存的话题详情接口使用
     * @param integer $topicId
     * @return array
     */
    public function getTopicDetail($topicId,$page){
        if ( $page > self::CACHE_PAGES ){
            $objTopic = new Topic_Object_Topic();
            $objTopic->fetch(array('id' => $topicId));
            $arrData  = $objTopic->toArray();
            return $arrData;
        }
        $redis = Base_Redis::getInstance();
        $ret   = $redis->get(Topic_Keys::getTopicContentKey($topicId));
        if(empty($ret)){
           $objTopic = new Topic_Object_Topic();
           $objTopic->fetch(array('id' => $topicId));           
           $arrData  = $objTopic->toArray();
           $arrData['desc'] = Base_Util_String::getSubString($arrData['content'],self::CONTENT_LEN);
           unset($arrData['content']);
           $redis->setex(Topic_Keys::getTopicContentKey($topicId),self::REDIS_TIMEOUT,json_encode($arrData)); 
           return $arrData;
        }
        return json_decode($ret,true);
    }
}
