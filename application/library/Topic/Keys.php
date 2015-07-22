<?php
class Topic_Keys {

    const REDIS_TOPIC_VISIT_KEY   = 'topic_visit_%s';
    
    const REDIS_TOPIC_TAG_KEY     = 'topic_tag_%s';
    
    const REDIS_TOPIC_COMMENT_KEY = 'topic_comment_%s';

    public static function getTopicVisitKey($id){
        return sprintf(self::REDIS_TOPIC_VISIT_KEY, $id);
    }
    
    public static function getTopicTagKey($id){
        return sprintf(self::REDIS_TOPIC_TAG_KEY, $id);
    }
    
    public static function getTopicCommentKey($id){
        return sprintf(self::REDIS_TOPIC_COMMENT_KEY, $id);
    }
}
