<?php
class Topic_Keys {

    const REDIS_TOPIC_VISIT_KEY   = 'topic_visit_num';

    public static function getTopicVisitKey(){
        return self::REDIS_TOPIC_VISIT_KEY;
    }
}
