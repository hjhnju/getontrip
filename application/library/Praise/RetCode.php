<?php
/**
 * 异常码与描述定义类
* 各模块可以在library中自定义错误码，如Collect/RetCode.php
*/

class Praise_RetCode extends Base_RetCode{

    //定义错误码：
    
    const HAS_PRAISED      = 1025; //已经点赞过

    /* 消息函数
     * @var array
    */
    protected static $_arrErrMap = array(
        self::HAS_PRAISED  => '已经点赞过',
    );
}
