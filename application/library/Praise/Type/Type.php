<?php
/**
 * 点赞类型
 * @author huwei
 *
 */
class Praise_Type_Type extends Base_Type {
    /**
     * 1 话题点赞
     * @var integer
     */
    const TOPIC = 1;
    
    /**
     * 类型名
     * @var array
     */
    public static $names = array(
        self::TOPIC    => '话题点赞',
    );
}