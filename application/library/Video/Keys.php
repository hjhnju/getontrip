<?php
class Video_Keys {
    
    //视频详情
    const REDIS_VIDEO_INFO_KEY   = 'video_%s_%s';
    
    //视频黑名单
    const REDIS_BLACK_VIDEO_KEY  = 'black_video_%s';

    public static function getVideoInfoName($sightId,$index){
        return sprintf(self::REDIS_VIDEO_INFO_KEY, $sightId,$index);
    }
    
    public static function getBlackVideoName($id){
        return sprintf(self::REDIS_BLACK_VIDEO_KEY, $id);
    }
}
