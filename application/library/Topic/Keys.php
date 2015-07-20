<?php
class Topic_Keys {

    const REDIS_TOPIC_VISIT_KEY   = 'topic_visit_num';
    
    const REDIS_TOPIC_TAG_KEY     = 'topic_tag_%d';

    public static function getTopicVisitKey(){
        return self::REDIS_TOPIC_VISIT_KEY;
    }
    
    public static function getTopicTagKey($id){
        return sprintf(self::REDIS_TOPIC_TAG_KEY, $id);
    }
}
