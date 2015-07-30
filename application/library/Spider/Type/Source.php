<?php
/**
 * 爬虫抓取数据源类型
 * @author huwei
 *
 */
class Spider_Type_Source extends Base_Type {
    /**
     * 1 网址
     * @var integer
     */
    const URL = 1;  

    /**
     * 2 字符串
     * @var integer
     */
    const STRING    = 2;
 
    /**
     * 类型名
     * @var array
     */
    public static $names = array(
        self::URL     => '网址',
        self::STRING  => '字符串',
    );
}