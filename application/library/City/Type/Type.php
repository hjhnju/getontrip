<?php
/**
 * 城市状态类型
 * @author huwei
 *
 */
class City_Type_Type extends Base_Type {
    /**
     * 0 海外
     * @var integer
     */
    const OVERSEAS  = 0;  

    /**
     * 1 内地
     * @var integer
     */
    const INLAND     = 1;
        
    /**
     * 2 港澳台特区
     * @var integer
     */
    const SPECIALZONE = 2;
    
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::OVERSEAS     => '海外',
        self::INLAND       => '内地',
        self::SPECIALZONE  => '港澳台',
    );
}