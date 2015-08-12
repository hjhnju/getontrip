<?php
class Find_Keys {
        
    //发现数据缓存id，参数为页码数
    const REDIS_FIND_KEY   = 'find_%s';

    public static function getFindKey($page){
        return sprintf(self::REDIS_FIND_KEY, $page);
    }        
}
