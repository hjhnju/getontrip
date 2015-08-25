<?php
class Sight_Keys {
    
    //景点的话题集合
    const REDIS_SIGHT_TOPIC_KEY   = 'sight_topic_%s';
    
    //景点的热门话题ID集合缓存,后面参数分别为景点id、页码，默认只缓存不带tags的
    const REDIS_SIGHT_HOT_KEY    = 'sight_hot_%s_%s';
    
    //景点最新话题ID集合缓存,后面参数分别为景点id、页码，默认只缓存不带tags的
    const REDIS_SIGHT_NEW_KEY    = 'sight_new_%s_%s';    

    public static function getSightTopicKey($id){
        return sprintf(self::REDIS_SIGHT_TOPIC_KEY, $id);
    }        
    
    public static function getHotTopicKey($sightId,$page){
        return sprintf(self::REDIS_SIGHT_HOT_KEY,$sightId,$page);
    }
    
    public static function getNewTopicKey($sightId,$page){
        return sprintf(self::REDIS_SIGHT_NEW_KEY,$sightId,$page);
    }
}
