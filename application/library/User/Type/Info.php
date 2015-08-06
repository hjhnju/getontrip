<?php
/**
 * 用户配置类型
 * @author huwei
 *
 */
class User_Type_Info extends Base_Type {
    /**
     * 1 接收消息或有图
     * @var integer
     */
    const ACCEPT = 1;
   
    /**
     * 2 不接受消息或无图
     * @var string
     */
    const NOTACCEPT = 2;    
    
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::ACCEPT           => '接收消息或有图',  
        self::NOTACCEPT        => '不接受消息或无图',
    );
}