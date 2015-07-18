<?php
class City_Keys {

    const REDIS_TAG_INFO_KEY   = 'cityinfo';

    //标签ID
    public static function getTagInfoKey(){
        return self::REDIS_TAG_INFO_KEY;
    }
}
