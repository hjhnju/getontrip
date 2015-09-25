<?php
/**
 * 搜索类型
 * @author huwei
 *
 */
class Search_Type_Search extends Base_Type {
    
    /**
     * 1 景点搜索
     * @var integer
     */
    
    const SIGHT = 1;  
    /**
     * 2 城市搜索
     * @var integer
     */
    const CITY = 2;
    
    /**
     * 3 内容搜索
     * @var integer
     */
    const CONTENT = 3;
   
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::SIGHT      => '景点搜索标签',
        self::CITY       => '城市搜索标签',
        self::CONTENT    => '内容搜索',
    );
}