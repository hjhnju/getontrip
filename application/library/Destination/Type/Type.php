<?php
/**
 * 目的地类型
 * @author huwei
 *
 */
class Destination_Type_Type extends Base_Type {
    
    /**
     * 1 景观
     * @var integer
     */
    const LANDSCAPE  = 1; 

    /**
     * 2 景点
     * @var integer
     */
    const SIGHT  = 2;

    /**
     * 3 城市
     * @var integer
     */
    const CITY  = 3;
   
    /**
     * 类型名
     * @var array
     */
    public static $names = array(
        self::LANDSCAPE   => '景观',
        self::SIGHT       => '景点',
        self::CITY        => '城市',
    );
}