<?php
/**
 * 异常码与描述定义类
* 各模块可以在library中自定义错误码，如Collect/RetCode.php
*/

class Collect_RetCode extends Base_RetCode{

    //定义错误码：
    const OBJID_ERROR       = 1025; //收藏对象的ID错误

    /* 消息函数
     * @var array
    */
    protected static $_arrErrMap = array(
        self::OBJID_ERROR   => '收藏对象的ID错误',
    );
}
