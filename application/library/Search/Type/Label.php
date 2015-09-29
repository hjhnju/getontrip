<?php
/**
 * 搜索标签类型
 * @author huwei
 *
 */
class Search_Type_Label extends Base_Type {
    /**
     * 1 景点搜索标签
     * @var integer
     */
    const SIGHT = 1;  
    /**
     * 2 城市搜索标签
     * @var integer
     */
    const CITY = 2;
   
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::SIGHT      => '景点',
        self::CITY       => '城市',
    );
}