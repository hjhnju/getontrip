<?php
class TopicModel extends BaseModel{
    
    const MONTH = 30;
    
    const WEEK  = 7;
    
    const CACHE_PAGES = 3;
    
    const INDEX_PAGE_SIZE = 2;
    
    const REDIS_TIMEOUT = 3600;
    
    const CONTENT_LEN   = 75;
    
    public function __construct(){
        parent::__construct();
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
        $from = ($page-1)*$pageSize;        
        if($during == self::MONTH){
            $sql = "SELECT a.id FROM `topic`  a,`topic_tag`  b,`sight_topic` c WHERE a.status = ".Topic_Type_Status::PUBLISHED." and a.id=b.topic_id and b.topic_id=c.topic_id AND c.sight_id = $sightId AND b.tag_id in(".$strTags.") ORDER by a.hot2 desc, a.update_time desc limit $from,$pageSize";
        }elseif($during == self::WEEK){
            $sql = "SELECT a.id FROM `topic`  a,`topic_tag`  b WHERE a.status = ".Topic_Type_Status::PUBLISHED." and a.id=b.topic_id AND b.tag_id in(".$strTags.") ORDER by a.hot1 desc, a.update_time desc limit $from,$pageSize";
        }                   
        try {                 	
            $data = $this->db->fetchAll($sql);          
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());          
            return array();
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
        //$ret   =  '';
        //if(($page <= self::CACHE_PAGES) && empty($strTags)){
        //    $ret = $redis->get(Sight_Keys::getNewTopicKey($sightId, $page));
        //}
        //if(!empty($ret)){
        //    return array_slice(json_decode($ret,true),0,$pageSize);
        //}
        $from = ($page-1)*$pageSize;
        if(empty($strTags)){
            $sql = "SELECT a.id FROM `topic` a, `sight_topic` b   WHERE  a.id = b.topic_id and b.sight_id = $sightId and a.status = ".Topic_Type_Status::PUBLISHED." ORDER BY a.update_time desc limit $from,$pageSize";
        }else{
            $sql = "SELECT a.id FROM `topic` a,`topic_tag`  b, `sight_topic` c WHERE a.id=b.topic_id and b.topic_id=c.topic_id AND c.sight_id = $sightId AND b.tag_id in(".$strTags.") and a.status = ".Topic_Type_Status::PUBLISHED." ORDER by a.update_time desc limit $from,$pageSize";
        }
        try {
            $data = $this->db->fetchAll($sql);
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());
            return array();
        }
        //if(($page <= self::CACHE_PAGES) && empty($strTags)){
        //    $redis->setex(Sight_Keys::getNewTopicKey($sightId,$page),self::REDIS_TIMEOUT,json_encode($data));
        //}
        return $data;
    }
    
    /**
     * 获取热门话题
     * @param string $strTopicId
     * @param string $strTags
     * @param integer $size
     * @return array
     */
    public function getHotTopicIdsByCity($cityId,$page, $pageSize){
        $redis = Base_Redis::getInstance();
        $ret   = $redis->get(City_Keys::getCityTopicKey($cityId, $page, $pageSize));

        if(!empty($ret)){
            return json_decode($ret,true);
        }
    
        $from = ($page-1)*$pageSize;
        if(empty($strTags)){
            $sql = "SELECT a.id FROM `topic`  a, `sight_topic` b ,`sight` c WHERE  a.status = ".Topic_Type_Status::PUBLISHED." and a.id = b.topic_id and b.sight_id = c.id and c.city_id = $cityId ORDER BY a.hot2 desc, a.update_time desc limit $from,$pageSize";            
        }
        try {
            $data = $this->db->fetchAll($sql);
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());
            return array();
        }       
        $redis->setex(City_Keys::getCityTopicKey($cityId, $page),self::REDIS_TIMEOUT,json_encode($data));
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
            $objTopic->setFileds(array('id','title','subtitle','image'));
            $objTopic->fetch(array('id' => $topicId));
            $arrData  = $objTopic->toArray();
            return $arrData;
        }
        $redis = Base_Redis::getInstance();
        $ret   = $redis->get(Topic_Keys::getTopicContentKey($topicId));
        if(empty($ret)){
           $objTopic = new Topic_Object_Topic();
           $objTopic->setFileds(array('id','title','subtitle','image'));
           $objTopic->fetch(array('id' => $topicId));           
           $arrData  = $objTopic->toArray();
           $redis->setex(Topic_Keys::getTopicContentKey($topicId),self::REDIS_TIMEOUT,json_encode($arrData)); 
           return $arrData;
        }
        return json_decode($ret,true);
    }
    
    /**
     * 根据景点ID及标签ID获取话题数
     * @param integer $tagId
     * @param integer $sightId
     * @return number
     */
    public function getTopicNumByTag($tagId, $sightId){  
       // $redis  = Base_Redis::getInstance();  
       // $total  = $redis->hGet(Tag_Keys::getTagTopicNumKey(),$sightId); 
       // if(!empty($total)){
       //     return $total;
       // }
        $objTag = new Tag_Object_Tag();
        $objTag->fetch(array('id' => $tagId));
        if($objTag->type == Tag_Type_Tag::GENERAL){
            //通用标签
            $sql   = "select count(distinct(b.topic_id)) from  `topic_tag` b, `topic` c where  b.tag_id = $tagId and  b.topic_id = c.id and c.status = ".Topic_Type_Status::PUBLISHED;
            $total = $this->db->fetchOne($sql);
        }else{
            //分类标签或普通标签
            $sql   = "select count(distinct(a.topic_id)) from `sight_topic` a, `topic_tag` b, `topic` c where a.sight_id = $sightId and b.tag_id = $tagId and a.topic_id = b.topic_id and a.topic_id = c.id and c.status = ".Topic_Type_Status::PUBLISHED;
            $total = $this->db->fetchOne($sql);
        }
       // $redis->hSet(Tag_Keys::getTagTopicNumKey(),$sightId,$total);
        return $total;
    }
}
