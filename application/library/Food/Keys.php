<?php
class Food_Keys {
    
    //视频详情
    const REDIS_FOOD_INFO_KEY   = 'food_%s_%s';
    
    //视频黑名单
    const REDIS_BLACK_FOOD_KEY  = 'black_food_%s';

    public static function getFoodInfoName($sightId,$index){
        return sprintf(self::REDIS_Food_INFO_KEY, $sightId,$index);
    }
    
    public static function getBlackFoodName($id){
        return sprintf(self::REDIS_BLACK_FOOD_KEY, $id);
    }
}
