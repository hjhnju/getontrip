<?php
class Tag_Keys {

    const REDIS_TAG_TOPIC_KEY   = 'tag_topic_%s';

    //标签ID
    public static function getTagTopic($id){
        return sprintf(self::REDIS_TAG_TOPIC_KEY, $id);
    }
}
