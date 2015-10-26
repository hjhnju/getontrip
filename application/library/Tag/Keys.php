<?php
class Tag_Keys {

    const REDIS_TAG_INFO_KEY   = 'taginfo_%s';
    
    const REDIS_TAG_TOPIC_KEY  = 'tag_topic_num';

    //标签ID
    public static function getTagInfoKey($id){
        return sprintf(self::REDIS_TAG_INFO_KEY, $id);
    }
    
    public static function getTagTopicNumKey(){
        return self::REDIS_TAG_TOPIC_KEY;
    }
}
