<?php
/**
 * 热度类型
 * @author huwei
 *
 */
class Hot_Type_Hot extends Base_Type {
    
    /**
     * 城市的热度类型
     * 1 热门
     * @var integer
     */
    const HOT = 1; 

    /**
     * 城市的热度类型
     * 2 非热门
     * @var integer
     */
    const NOTHOT = 2;

    
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::HOT        => '热门',
        self::NOTHOT     => '非热门',
    );
}