<?php
/**
 * 异常码与描述定义类
* 各模块可以在library中自定义错误码，如Collect/RetCode.php
*/

class Advise_RetCode extends Base_RetCode{
    //定义错误码：
    
    const REPORT_EXSIT       = 1025; //已经举报过
    
    /* 消息函数
     * @var array
     */
    protected static $_arrErrMap = array(
        self::REPORT_EXSIT  => '已经举报过',
    );
}
