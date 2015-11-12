<?php
class Collect_Keys {

    const REDIS_TOPIC_INFO_KEY   = 'collect_topic_info';

    const REDIS_SIGHT_INFO_KEY   = 'collect_sight_info';

    const REDIS_CITY_INFO_KEY  = 'collect_city_info';
    
    const REDIS_BOOK_INFO_KEY   = 'collect_book_info';
    
    const REDIS_LATE_KEY         = '%s_late_%s';
    
    const REDIS_TOTAL_KEY        = '%s_total';
    
    const REDIS_LATE_MINUTE_KEY  = '%s_late_minute_%s';
    
    const TOPIC     = 4;
    
    const SIGHT     = 2;
    
    const CITY    = 3;
     
    const BOOK    = 5;

    public static function getTopicInfoKey(){
        return self::REDIS_TOPIC_INFO_KEY;
    }

    public static function getSightInfoKey(){
    	return self::REDIS_SIGHT_INFO_KEY;
    }

    public static function getCityInfoKey(){
    	return self::REDIS_CITY_INFO_KEY;
    }
   
    public static function getBookInfoKey(){
        return self::REDIS_BOOK_INFO_KEY;
    }
    
    public static function getLateKeyName($id,$during){
        return sprintf(self::REDIS_LATE_KEY,$id,$during);
    }
    
    public static function getLateMinuteKeyName($id,$during){
        return sprintf(self::REDIS_LATE_MINUTE_KEY,$id,$during);
    }
    
    public static function getTotalKeyName($id){
        return sprintf(self::REDIS_TOTAL_KEY,$id);
    }
    
    public static function getHashKeyByType($type){
        switch($type){
            case self::TOPIC:
                return self::REDIS_TOPIC_INFO_KEY;
            case self::SIGHT:
                return self::REDIS_SIGHT_INFO_KEY;
            case self::CITY:
                return self::REDIS_CITY_INFO_KEY;
            case self::BOOK:
                return self::REDIS_BOOK_INFO_KEY;
            default:
                break;
        }
    }
}
