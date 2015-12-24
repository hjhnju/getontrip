<?php
/**
 * 推荐类型
 * @author huwei
 *
 */
class Recommend_Type_Label extends Base_Type {
    /**
     * 1 景点
     * @var integer
     */
    const SIGHT  = 1; 
     
    /**
     * 2 通用标签
     * @var integer
     */
    const GENERAL = 2;
    
    /**
     * 3 分类标签
     * @var integer
     */
    const TAG     = 3;
    
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::SIGHT      => '景点',
        self::GENERAL    => '通用标签',
        self::TAG        => '分类标签',
    );
}