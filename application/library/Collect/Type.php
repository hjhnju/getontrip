<?php
/**
 * 收藏类型
 * @author huwei
 *
 */
class Collect_Type extends Base_Type {
    /**
     * 1 收藏话题
     * @var integer
     */
    const TOPIC = 1;
    /**
     * 2 收藏景点
     * @var integer
     */
    const SIGHT = 2;
    /**
     * 3 收藏答案
     * @var integer
     */
    const ANSWER = 3;    
    /**
     * 4 收藏主题
     * @var integer
     */
    const THEME = 4;
   
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::TOPIC      => '收藏话题',
        self::SIGHT      => '收藏景点',
        self::ANSWER     => '收藏答案',
        self::THEME      => '收藏主题',
    );
}