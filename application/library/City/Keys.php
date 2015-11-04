<?php
class City_Keys {
    
    //城市热门话题ID集合缓存,后面参数分别为城市id、页码
    const REDIS_CITY_TOPIC_KEY    = 'city_topic_%s_%s';
    
    public static function getCityTopicKey($id, $page){
        return sprintf(self::REDIS_CITY_TOPIC_KEY, $id, $page);
    }
}
