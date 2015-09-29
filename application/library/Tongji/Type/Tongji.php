<?php
/**
 * 访问类型
 * @author huwei
 *
 */
class Tongji_Type_Tongji extends Base_Type {
    /**
     * 1 话题
     * @var integer
     */
    const TOPIC = 1;
    /**
     * 2 景点
     * @var integer
     */
    const SIGHT = 2;  
    /**
     * 3 城市
     * @var integer
     */
    const CITY  = 3;
   
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::TOPIC      => '话题',
        self::SIGHT      => '景点',
        self::CITY       => '城市',
    );
}