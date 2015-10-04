<?php
/**
 * 收藏类型
 * @author huwei
 *
 */
class Collect_Type extends Base_Type {
    /**
     * 1 收藏内容
     * @var integer
     */
    const TOPIC = 1;
    /**
     * 2 收藏景点
     * @var integer
     */
    const SIGHT = 2;  
    /**
     * 3 收藏城市
     * @var integer
     */
    const CITY = 3;
   
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::TOPIC      => '收藏内容',
        self::SIGHT      => '收藏景点',
        self::CITY       => '收藏主题',
    );
}