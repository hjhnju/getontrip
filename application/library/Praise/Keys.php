<?php
class Praise_Keys {

    const REDIS_TOPIC_INFO_KEY   = 'praise_topic_info';
    
    const REDIS_LATE_KEY         = '%s_late_%s';
    
    const REDIS_TOTAL_KEY        = '%s_total';
    
    const REDIS_LATE_MINUTE_KEY  = '%s_late_minute_%s';

    public static function getTopicInfoKey(){
        return self::REDIS_TOPIC_INFO_KEY;
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
}
