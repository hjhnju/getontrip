<?php
/**
 * 访问类型
 * @author huwei
 *
 */
class Visit_Type extends Base_Type {
    /**
     * 1 访问话题
     * @var integer
     */
    const TOPIC = 1;
    /**
     * 2 访问景点
     * @var integer
     */
    const SIGHT = 2;  
    /**
     * 3 访问主题
     * @var integer
     */
    const THEME = 3;
    /**
     * 4 访问景观
     * @var integer
     */
    const LANDSCAPE = 4;
   
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::TOPIC      => '访问话题',
        self::SIGHT      => '访问景点',
        self::THEME      => '访问主题',
        self::LANDSCAPE  => '访问景观',
    );
}