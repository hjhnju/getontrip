<?php
/**
 * 推荐结果类型
 * @author huwei
 *
 */
class Recommend_Type_Status extends Base_Type {
    /**
     * 1 未处理
     * @var integer
     */
    const NOT_DEAL  = 1; 
     
    /**
     * 2 已接受
     * @var integer
     */
    const ACCEPT = 2;
    
    /**
     * 3 已拒绝
     * @var integer
     */
    const REJECT = 3;
    
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::NOT_DEAL      => '未处理',
        self::ACCEPT        => '已接受',
        self::REJECT        => '已拒绝',
    );
}