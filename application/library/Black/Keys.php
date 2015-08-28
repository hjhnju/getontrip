<?php
class Black_Keys {

    const REDIS_BLACK_KEY   = 'black_%s';

    //黑名单
    public static function getBlackKey($type){
        return sprintf(self::REDIS_BLACK_KEY,$type);
    }
}
