<?php
class Topic_Keys {
    
    //话题访问信息
    const REDIS_TOPIC_VISIT_KEY   = 'topic_visit_info';
    
    //话题标签ID集合，参数为话题ID
    const REDIS_TOPIC_TAG_KEY     = 'topic_tag_%s';
    
    const REDIS_LATE_KEY  = '%s_late_%s';
    
    const REDIS_TOTAL_KEY    = '%s_total';

    //根据话题ID，缓存话题内容，不包含统计数据
    const REDIS_TOPIC_CONTENT = 'topic_%s';
    
    public static function getTopicVisitKey(){
        return self::REDIS_TOPIC_VISIT_KEY;
    }
    
    public static function getTopicTagKey($id){
        return sprintf(self::REDIS_TOPIC_TAG_KEY, $id);
    }
    
    public static function getLateKey($id,$during){
        return sprintf(self::REDIS_LATE_KEY,$id,$during);
    }
    
    public static function getTotalKey($id){
        return sprintf(self::REDIS_TOTAL_KEY,$id);
    }
    
    public static function getTopicContentKey($topicId){
        return sprintf(self::REDIS_TOPIC_CONTENT,$topicId);
    }
}
