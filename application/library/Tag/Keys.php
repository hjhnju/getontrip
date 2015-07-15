<?php
class Tag_Keys {

    const REDIS_TAG_SIGHT_KEY   = 'tag_sight_%s';

    public static function getSightName($id){
        return sprintf(self::REDIS_TAG_SIGHT_KEY, $id);
    }
}
