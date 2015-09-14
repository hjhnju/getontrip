<?php 
/**
* 
*/
class Admin_Type_Role  extends Base_Type
{
     /**
     * 普通
     * @var int
     */
    const COMMON  = 1;
    /**
     * 超级
     * @var int
     */
    const SUPER  = 2; 
    
    public static $names  = array(
        self::COMMON => '普通用户',
        self::SUPER => '超级用户' 
    );
}