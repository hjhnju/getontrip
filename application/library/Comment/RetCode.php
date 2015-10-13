<?php
/**
 * 异常码与描述定义类
* 各模块可以在library中自定义错误码，如Collect/RetCode.php
*/

class Comment_RetCode extends Base_RetCode{

    //定义错误码：
    
    const USER_NOT_INT      = 2; //用户未登录

    /* 消息函数
     * @var array
    */
    protected static $_arrErrMap = array(
        self::USER_NOT_INT  => '用户未登录',
    );
}
