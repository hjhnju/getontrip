<?php
class Tag_Keys {

    const REDIS_TAG_INFO_KEY   = 'taginfo';

    //标签ID
    public static function getTagInfoKey(){
        return self::REDIS_TAG_INFO_KEY;
    }
}
