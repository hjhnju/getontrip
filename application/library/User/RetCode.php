<?php
/**
 * 异常码与描述定义类
 * 各模块可以在library中自定义错误码，如User/RetCode.php
 */

class User_RetCode extends Base_RetCode{
    
    const LOGIN_FAIL       = 1025; //登录失败
    
    const SIGN_OUT_FIAL    = 1026; //登出失败    
    
    const EMAIL_FORMAT_WRONG  = 1027; //邮箱格式错误
    
    const PASSWD_FORMAT_WRONG = 1028; //密码格式错误
    
    const EMAIL_WRONG         = 1029; //邮箱不存在
    
    const PASSWD_WORNG        = 1030; //密码错误
    
    const EMAIL_EXSIT         = 1031; //邮箱已被注册
    
    /* 消息函数
     * @var array
     */
    protected static $_arrErrMap = array(    
        self::LOGIN_FAIL          => '登录失败',
        self::SIGN_OUT_FIAL       => '登出失败',   
        self::EMAIL_WRONG         => '邮箱不存在', 
        self::EMAIL_FORMAT_WRONG  => '邮箱格式错误',
        self::PASSWD_WORNG        => '密码错误',
        self::PASSWD_FORMAT_WRONG => '密码格式错误',  
        self::EMAIL_EXSIT         => '邮箱已被注册',
    );

}
