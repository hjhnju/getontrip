<?php
/**
 * 景点类型
 * @author huwei
 *
 */
class Sight_Type_Meta extends Base_Type {
    /**
     * 0 无需处理
     * @var integer
     */
    const NOTNEED    = 0;  
 
    /**
     * 1 待处理
     * @var integer
     */
    const NEEDCONFIRM  = 1;
    
    /**
     * 2 已确认
     * @var unknown
     */
    const CONFIRMED    = 2;
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::NOTNEED       => '未采纳',
        self::NEEDCONFIRM   => '推荐采纳',
        self::CONFIRMED     => '已采纳',
    );
}