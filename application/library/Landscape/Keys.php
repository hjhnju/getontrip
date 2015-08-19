<?php
class Landscape_Keys {
    
    //景观访问信息
    const REDIS_LANDSCAPE_VISIT_KEY   = 'landscape_visit_info';
    
    const REDIS_LATE_KEY  = '%s_late_%s';
    
    const REDIS_TOTAL_KEY    = '%s_total';
    
    public static function getLandscapeVisitKey(){
        return self::REDIS_LANDSCAPE_VISIT_KEY;
    }
    
    public static function getLateKey($id,$during){
        return sprintf(self::REDIS_LATE_KEY,$id,$during);
    }
    
    public static function getTotalKey($id){
        return sprintf(self::REDIS_TOTAL_KEY,$id);
    }
}
