<?php
class Sight_Keys {
    
    //景点的话题集合
    const REDIS_SIGHT_TOPIC_KEY   = 'sight_topic_%s';
    
    //景点的热门话题数据缓存
    const REDIS_SIGHT_HOT_KEY    = 'sight_hot_%s_%s';
    
    //景点最新话题数据缓存
    const REDIS_SIGHT_NEW_KEY    = 'sight_new_%s_%s';

    public static function getSightTopicKey($id){
        return sprintf(self::REDIS_SIGHT_TOPIC_KEY, $id);
    }        
    
    public static function getHotTopic($sightId,$strTags=''){
        return sprintf(self::REDIS_SIGHT_HOT_KEY,$sightId,$strTags);
    }
    
    public static function getNewTopic($sightId,$strTags=''){
        return sprintf(self::REDIS_SIGHT_NEW_KEY,$sightId,$strTags);
    }
}
