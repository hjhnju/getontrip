<?php
class Sight_Keys {
    
    //景点的话题集合
    const REDIS_SIGHT_TOPIC_KEY   = 'sight_topic_%s';
    
    //景点的热门话题ID集合缓存,后面参数分别为景点id、页码，默认只缓存不带tags的
    const REDIS_SIGHT_HOT_KEY    = 'sight_hot_%s_%s';
    
    //景点最新话题ID集合缓存,后面参数分别为景点id、页码，默认只缓存不带tags的
    const REDIS_SIGHT_NEW_KEY    = 'sight_new_%s_%s';   

    //景点首页话题ID集合缓存,后面参数分别为景点id、页码，默认只缓存不带tags的
    const REDIS_SIGHT_INDEX_KEY    = 'sight_index_%s_%s';
    
    //景点的统计信息KEY，包括：话题数、书籍数、视频数、景观数
    const REDIS_SIGHT_TONGJI_KEY   = 'sight_tongji_%s'; 
    
    //景点的显示标签的KEY
    const REDIS_SIGHT_SHOW_TAG_KEY = 'sight_tag_%s';

    const BOOK      = 'book';
    
    const TOPIC     = 'topic';
    
    const VIDEO     = 'video';
    
    const LANDSCAPE = 'landscape';
    
    public static function getSightTopicKey($id){
        return sprintf(self::REDIS_SIGHT_TOPIC_KEY, $id);
    }        
    
    public static function getHotTopicKey($sightId,$page){
        return sprintf(self::REDIS_SIGHT_HOT_KEY,$sightId,$page);
    }
    
    public static function getNewTopicKey($sightId,$page){
        return sprintf(self::REDIS_SIGHT_NEW_KEY,$sightId,$page);
    }
    
    public static function getIndexTopicKey($sightId,$page){
        return sprintf(self::REDIS_SIGHT_INDEX_KEY,$sightId,$page);
    }
    
    public static function getSightTongjiKey($sightId){
        return sprintf(self::REDIS_SIGHT_TONGJI_KEY,$sightId);
    }
    
    public static function getSightShowTagIds($sightId){
        return sprintf(self::REDIS_SIGHT_SHOW_TAG_KEY,$sightId);
    }
}
