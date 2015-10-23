<?php
/**
 * 书籍抓取来源
 * @author huwei
 *
 */
class Book_Type_Source extends Base_Type {
    /**
     * 1 京东
     * @var integer
     */
    const JD = 1;  

    /**
     * 2 豆瓣
     * @var integer
     */
    const DOUBAN    = 2;    
    
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::JD       => '京东',
        self::DOUBAN   => '豆瓣',
    );
}