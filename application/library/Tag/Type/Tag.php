<?php
/**
 * 标签类型
 * @author huwei
 *
 */
class Tag_Type_Tag extends Base_Type {
    /**
     * 1 普通标签
     * @var integer
     */
    const NORMAL  = 1;  
    /**
     * 2 通用标签
     * @var integer
     */
    const GENERAL = 2;
    
    /**
     * 3 二级分类标签
     * @var integer
     */
    const CLASSIFY = 3;
   
    /**
     * 4 搜索标签
     * @var integer
     */
    const SEARCH = 4;
    
    /**
     * 5 一级分类标签
     * @var integer
     */
    const TOP_CLASS = 5;
    
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::NORMAL      => '普通标签',
        self::GENERAL     => '通用标签',
        self::CLASSIFY    => '二级分类标签',
        self::SEARCH      => '搜索标签',
        self::TOP_CLASS   => '一级分类标签',
    );
}