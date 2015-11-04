<?php
class Sight_Keys {
    
    //景点的话题集合
    const REDIS_SIGHT_TOPIC_KEY   = 'sight_topic_%s';     

    //景点的统计信息KEY，包括：话题数、书籍数、视频数、景观数
    const REDIS_SIGHT_TONGJI_KEY   = 'sight_tongji_%s'; 
    
    //景点的显示标签的KEY
    const REDIS_SIGHT_SHOW_TAG_KEY = 'sight_tag_%s';

    const BOOK      = 'book';
    
    const TOPIC     = 'topic';
    
    const VIDEO     = 'video';
    
    const LANDSCAPE = 'landscape';
    
    public static function getSightTopicKey($id){
        return sprintf(self::REDIS_SIGHT_TOPIC_KEY, $id);
    }        
    
    public static function getSightTongjiKey($sightId){
        return sprintf(self::REDIS_SIGHT_TONGJI_KEY,$sightId);
    }
    
    public static function getSightShowTagIds($sightId){
        return sprintf(self::REDIS_SIGHT_SHOW_TAG_KEY,$sightId);
    }
}
