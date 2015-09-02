<?php
class Theme_Keys {
    
    //主题访问信息
    const REDIS_THEME_VISIT_KEY   = 'theme_visit_info';
    
    const REDIS_LATE_KEY  = '%s_late_%s';
    
    const REDIS_TOTAL_KEY    = '%s_total';
    
    public static function getThemeVisitKey(){
        return self::REDIS_THEME_VISIT_KEY;
    }
    
    public static function getLateKey($id,$during){
        return sprintf(self::REDIS_LATE_KEY,$id,$during);
    }
    
    public static function getTotalKey($id){
        return sprintf(self::REDIS_TOTAL_KEY,$id);
    }
}
