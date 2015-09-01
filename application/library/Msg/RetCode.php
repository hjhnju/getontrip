<?php
/**
 * 异常码与描述定义类
* 各模块可以在library中自定义错误码，如Msg/RetCode.php
*/

class Msg_RetCode extends Base_RetCode{

    //定义错误码：


    /* 消息函数
     * @var array
    */
    protected static $_arrErrMap = array(
    );

    /**
     * 获取信息描述
     * @param  int    $exceptionCode 错误码
     * @return string            描述
    */
    public static function getMsg($exceptionCode) {

        if (isset(self::$_arrErrMap[$exceptionCode])) {
            return self::$_arrErrMap[$exceptionCode];
        } else {
            return self::$_arrErrMap[self::UNKNOWN_ERROR];
        }
    }
}
