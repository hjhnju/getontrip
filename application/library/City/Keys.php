<?php
class City_Keys {

    const REDIS_TAG_INFO_KEY   = 'cityinfo';
    
    //城市热门话题ID集合缓存,后面参数分别为城市id、页码
    const REDIS_CITY_TOPIC_KEY    = 'city_topic_%s_%s';
    
    public static function getCityTopicKey($id, $page){
        return sprintf(self::REDIS_CITY_TOPIC_KEY, $id, $page);
    }

    //标签ID
    public static function getTagInfoKey(){
        return self::REDIS_TAG_INFO_KEY;
    }
}
