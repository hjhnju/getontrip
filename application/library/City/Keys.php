<?php
class City_Keys {
    
    //城市热门话题ID集合缓存,后面参数分别为城市id、页码
    const REDIS_CITY_TOPIC_KEY    = 'city_topic_%s_%s';
    
    const REDIS_CITY_TOPIC_NUM    = 'city_topic_num';
    
    const REDIS_CITY_WIKI_NUM     = 'city_wiki_num';
    
    const REDIS_CITY_VIDEO_NUM    = 'city_video_num';
    
    const REDIS_CITY_BOOK_NUM     = 'city_book_num';
    
    public static function getCityTopicKey($id, $page){
        return sprintf(self::REDIS_CITY_TOPIC_KEY, $id, $page);
    }
    
    public static function getCityTopicNumKey(){
        return self::REDIS_CITY_TOPIC_NUM;
    }
    
    public static function getCityWikiNumKey(){
        return self::REDIS_CITY_WIKI_NUM;
    }
    
    public static function getCityVideoNumKey(){
        return self::REDIS_CITY_VIDEO_NUM;
    }
    
    public static function getCityBookNumKey(){
        return self::REDIS_CITY_BOOK_NUM;
    }
}
