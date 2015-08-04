<?php
/**
 * 词条状态类型
 * @author huwei
 *
 */
class Keyword_Type_Status extends Base_Type {
    /**
     * 1 未确认
     * @var integer
     */
    const NOTPUBLISHED = 1;  

    /**
     * 2 已确认
     * @var integer
     */
    const PUBLISHED    = 2;
        
    /**
     * 3 所有的
     * @var integer
     */
    const ALL    = 3;
    
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::NOTPUBLISHED     => '未确认',
        self::PUBLISHED        => '已确认',
        self::ALL              => '全部状态',
    );
}