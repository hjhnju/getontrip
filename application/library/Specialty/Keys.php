<?php
class Specialty_Keys {
    
    //美食详情
    const REDIS_SPECIALTY_INFO_KEY   = 'specialty_%s_%s';
    
    //美食推荐的话题ID数据
    const REDIS_SPECIALTY_RECOMMEND  = 'specialty_recommend_%s';
    
    //美食黑名单
    const REDIS_BLACK_SPECIALTY_KEY  = 'black_specialty_%s';
    
    const REDIS_SPECIALTY_TOPIC_NUM  = 'specialty_topic_num';
    
    const REDIS_SPECIALTY_TOPIC_IDS  = 'specialty_topic_id_%s';

    public static function getSpecialtyInfoName($sightId,$index){
        return sprintf(self::REDIS_Specialty_INFO_KEY, $sightId,$index);
    }
    
    public static function getBlackSpecialtyName($id){
        return sprintf(self::REDIS_BLACK_SPECIALTY_KEY, $id);
    }
    
    public static function getSpecialtyRecommend($id){
        return sprintf(self::REDIS_SPECIALTY_RECOMMEND, $id);
    }
    
    public static function getSpecialtyTopicNum(){
        return self::REDIS_SPECIALTY_TOPIC_NUM;
    }
    
    public static function getSpecialtyTopicIds($id){
        return sprintf(self::REDIS_SPECIALTY_TOPIC_IDS, $id);
    }
}
