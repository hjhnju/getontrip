<?php
/**
 * 异常码与描述定义类
 * 各模块可以在library中自定义错误码，如User/RetCode.php
 */

class User_RetCode extends Base_RetCode{
    
    const LOGIN_FAIL       = 1025; //登录失败
    
    const SIGN_OUT_FIAL    = 1026; //登出失败    
    
    /* 消息函数
     * @var array
     */
    protected static $_arrErrMap = array(     
        self::LOGIN_FAIL          => '登录失败',
        self::SIGN_OUT_FIAL       => '登出失败', 
        self::NOT_LOGIN           => '未登录',      
    );

}
