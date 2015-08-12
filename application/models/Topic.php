<?php
class TopicModel{
    
    const MONTH = 30;
    
    const WEEK  = 7;
    
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
    public function getHotTopics($strTopicId,$strTags,$size,$during){
        if(empty($strTags)){
            if($during == self::MONTH){
                $sql = "SELECT a.id,a.title,a.subtitle,a.image,a.from,a.desc,a.content FROM `topic`  a,`topic_tag`  b WHERE a.status = 5 and a.id=b.topic_id AND a.id in(".$strTopicId.") ORDER by a.hot2 desc, a.update_time desc limit 0,$size";
            }elseif($during == self::WEEK){
                $sql = "SELECT a.id,a.title,a.subtitle,a.image,a.from,a.desc,a.content FROM `topic`  a,`topic_tag`  b WHERE a.status = 5 and a.id=b.topic_id AND a.id in(".$strTopicId.") ORDER by a.hot1 desc, a.update_time desc limit 0,$size";
            }
        }else{
            if($during == self::MONTH){
                $sql = "SELECT a.id,a.title,a.subtitle,a.image,a.from,a.desc,a.content FROM `topic`  a,`topic_tag`  b WHERE a.status = 5 a.id=b.topic_id AND a.id in(".$strTopicId.") AND b.tag_id in(".$strTags.") ORDER by a.hot2 desc, a.update_time desc limit 0,$size";
            }elseif($during == self::WEEK){
                $sql = "SELECT a.id,a.title,a.subtitle,a.image,a.from,a.desc,a.content FROM `topic`  a,`topic_tag`  b WHERE a.status = 5 a.id=b.topic_id AND a.id in(".$strTopicId.") AND b.tag_id in(".$strTags.") ORDER by a.ho1 desc, a.update_time desc limit 0,$size";
            }
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
    public function getNewTopics($strTopicId,$strTags,$page,$pageSize){
        $from = ($page-1)*$pageSize;
        if(empty($strTags)){
            $sql = "SELECT a.id,a.title,a.subtitle,a.image,a.from,a.desc,a.content FROM `topic`  a,`topic_tag`  b WHERE a.status = 5 and a.id=b.topic_id AND a.id in(".$strTopicId.") ORDER BY a.update_time desc limit $from,$pageSize";
        }else{
            $sql = "SELECT a.id,a.title,a.subtitle,a.image,a.from,a.desc,a.content FROM `topic`  a,`topic_tag`  b WHERE a.status = 5 and a.id=b.topic_id AND a.id in(".$strTopicId.") AND b.tag_id in(".$strTags.") ORDER by a.update_time desc limit $from,$pageSize";
        }
        try {
            $data = $this->db->fetchAll($sql);
        } catch (Exception $ex) {
            Base_Log::error($ex->getMessage());
            return array();
        }
        return $data;
    }
}
