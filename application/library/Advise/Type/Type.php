<?php
/**
 * 类型
 * @author huwei
 *
 */
class Advise_Type_Type extends Base_Type {
    /**
     * 1 提问
     * @var integer
     */
    const ADVISE  = 1; 
    
   /**
    * 1 回答
    * @var integer
    */
    const ANSWER    = 2;
   
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::ADVISE   => '提问',
        self::ANSWER   => '回答',
    );
}