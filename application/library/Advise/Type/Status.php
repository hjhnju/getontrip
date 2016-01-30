<?php
/**
 * 反馈意见处理类型
 * @author huwei
 *
 */
class Advise_Type_Status extends Base_Type {
    /**
     * 1 未处理
     * @var integer
     */
    const UNTREATED   = 1; 
    
   /**
    * 2 已解决
    * @var integer
    */
    const SETTLED     = 2;
    
    /**
     * 3 待解决
     * @var unknown
     */
    const NEED_HANDLE = 3;
    
    /**
     * 4 无需处理
     * @var unknown
     */
    const DROP = 4;
   
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::UNTREATED   => '未处理',
        self::SETTLED     => '已解决',
        self::NEED_HANDLE => '待解决',
        self::DROP        => '无需处理',
    );
}