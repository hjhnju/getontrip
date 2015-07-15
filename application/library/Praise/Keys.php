<?php
class Praise_Keys {

    const REDIS_ANSWER_INFO_KEY  = 'praise_answer_info';
    
    const ANSWER  = 1;

    public static function getAnswerInfoKey(){
    	return self::REDIS_ANSWER_INFO_KEY;
    }
    
    public static function getHashKeyByType($type){
        switch($type){
            case self::ANSWER:
                return self::REDIS_ANSWER_INFO_KEY;
            default:
                break;
        }
    }
}
