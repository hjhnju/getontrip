<?php
class Comment_Keys {
    
    const REDIS_COMMENT_KEY = 'comment_info';
    
    const REDIS_LATE_KEY    = '%s_late_%s';
    
    const REDIS_LATE_MINUTE = '%s_late_minute_%s';
    
    const REDIS_TOTAL_KEY   = '%s_total';
    
    public static function getLateKey($id,$during){
        return sprintf(self::REDIS_LATE_KEY,$id,$during);
    }
    
    public static function getLateMinuteKey($id,$during){
        return sprintf(self::REDIS_LATE_MINUTE,$id,$during);
    }
    
    public static function getTotalKey($id){
        return sprintf(self::REDIS_TOTAL_KEY,$id);
    }
    
    public static function getCommentKey(){
        return self::REDIS_COMMENT_KEY;
    }
}
