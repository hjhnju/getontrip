<?php
class Topic_Keys {

    const REDIS_TOPIC_VISIT_KEY   = 'topic_visit_info';
    
    const REDIS_TOPIC_TAG_KEY     = 'topic_tag_%s';
    
    const REDIS_LATE_KEY  = '%s_late_%s';
    
    const REDIS_TOTAL_KEY    = '%s_total';
    
    const REDIS_TOPIC_HOT_KEY = 'topic_hot_info';

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
    
    public static function getTopicHotKey(){
        return self::REDIS_TOPIC_HOT_KEY;
    }
}
