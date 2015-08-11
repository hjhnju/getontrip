<?php
class Collect_Keys {

    const REDIS_TOPIC_INFO_KEY   = 'collect_topic_info';

    const REDIS_SIGHT_INFO_KEY   = 'collect_sight_info';

    const REDIS_ANSWER_INFO_KEY  = 'collect_answer_info';
    
    const REDIS_THEME_INFO_KEY   = 'collect_theme_info';
    
    const REDIS_LATE_KEY         = '%s_late_%s';
    
    const REDIS_TOTAL_KEY        = '%s_total';
    
    const TOPIC     = 1;
    
    const SIGHT     = 2;
    
    const ANSWER    = 3;
     
    const THEME     = 4;

    public static function getTopicInfoKey(){
        return self::REDIS_TOPIC_INFO_KEY;
    }

    public static function getSightInfoKey(){
    	return self::REDIS_SIGHT_INFO_KEY;
    }

    public static function getAnswerInfoKey(){
    	return self::REDIS_ANSWER_INFO_KEY;
    }
   
    public static function getThemeInfoKey(){
        return self::REDIS_THEME_INFO_KEY;
    }
    
    public static function getLateKeyName($id,$during){
        return sprintf(self::REDIS_LATE_KEY,$id,$during);
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
            case self::ANSWER:
                return self::REDIS_ANSWER_INFO_KEY;
            case self::THEME:
                return self::REDIS_THEME_INFO_KEY;
            default:
                break;
        }
    }
}
