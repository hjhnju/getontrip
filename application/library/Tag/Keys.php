<?php
class Tag_Keys {

    const REDIS_TAG_INFO_KEY   = 'taginfo_%s';

    //标签ID
    public static function getTagInfoKey($id){
        return sprintf(self::REDIS_TAG_INFO_KEY, $id);
    }
}
