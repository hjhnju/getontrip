<?php
class Sight_Keys {

    const REDIS_SIGHT_INFO_KEY   = 'sightid_%d';
    
    const REDIS_SIGHT_TOPIC_KEY  = 'sight_topic_%s';   
    
    const REDIS_SIGHT_NAME_KEY   = 'name';
    
    const REDIS_SIGHT_CITYID_KEY   = 'cityid';

    public static function getSightName($id){
        return sprintf(self::REDIS_SIGHT_INFO_KEY, $id);
    }
    
    public static function getSightTopicName($id){
        return sprintf(self::REDIS_SIGHT_TOPIC_KEY, $id);
    }
    
    public static function getSightNameKey(){
        return self::REDIS_SIGHT_NAME_KEY;
    }
    
    public static function getCityIdKey(){
        return self::REDIS_SIGHT_CITYID_KEY;
    }
    
    public static function getTopicNumKey(){
        return self::REDIS_SIGHT_TOPICNUM_KEY;
    }
}
