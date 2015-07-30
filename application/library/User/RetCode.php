<?php
/**
 * 异常码与描述定义类
 * 各模块可以在library中自定义错误码，如User/RetCode.php
 */

class User_RetCode extends Base_RetCode{
    
    const GET_AUTHCODE_FAIL         = 1025; //第三方登录授权出错

    const GET_OPENID_FAIL           = 1026; //获取openid失败

    const SIGN_OUT_FIAL             = 1027; //登出失败    
    
    /* 消息函数
     * @var array
     */
    protected static $_arrErrMap = array(
       
        self::GET_AUTHCODE_FAIL        => '第三方登录授权出错',      
        self::GET_OPENID_FAIL          => '获取openid失败',
        self::SIGN_OUT_FIAL            => '登出失败',       
    );

}
