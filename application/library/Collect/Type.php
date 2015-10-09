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
     * 3 收藏城市
     * @var integer
     */
    const CITY = 3;
    
    /**
     * 4 收藏书籍
     * @var integer
     */
    const BOOK = 4;
   
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::TOPIC      => '收藏内容',
        self::SIGHT      => '收藏景点',
        self::CITY       => '收藏主题',
        self::BOOK       => '收藏书籍',
    );
}