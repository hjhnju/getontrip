<?php
/**
 * 反馈意见处理类型
 * @author huwei
 *
 */
class Advise_Type extends Base_Type {
    /**
     * 0 未处理
     * @var integer
     */
    const UNTREATED  = 0; 
    
   /**
    * 1 已解决
    * @var integer
    */
    const SETTLED    = 1;
   
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::UNTREATED   => '未处理',
        self::SETTLED     => '已解决',
    );
}