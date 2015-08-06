<?php
/**
 * 景观状态类型
 * @author huwei
 *
 */
class Landscape_Type_Status extends Base_Type {
    /**
     * 1 未发布
     * @var integer
     */
    const NOTPUBLISHED = 1;  
 
    /**
     * 2 已发布
     * @var integer
     */
    const PUBLISHED    = 2;
    
    /**
     * 3 全部状态
     * @var unknown
     */
    const ALL          = 3;
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::NOTPUBLISHED     => '未发布',
        self::PUBLISHED        => '已发布',
        self::ALL              => '全部状态',
    );
}