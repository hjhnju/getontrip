<?php
class Video_Keys {

    const REDIS_VIDEO_INFO_KEY   = 'video_%s_%s';

    public static function getVideoInfoName($sightId,$index){
        return sprintf(self::REDIS_VIDEO_INFO_KEY, $sightId,$index);
    }
}
