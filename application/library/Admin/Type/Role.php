<?php 
/**
* 管理员类型
*/
class Admin_Type_Role  extends Base_Type{
    
     /**
     * 1 普通管理员
     * @var integer
     */
    const GENERAL = 1;
    
    /**
     * 2 超级用户
     * @var integer
     */
    const SUPER   = 2;
 
    protected static $names  = array(
        self::GENERAL => '普通管理员',
        self::SUPER   => '超级用户',
    );
}