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
    const NOTACCEPT  = 2;    
    
    /**
     * 0 男性
     * @var integer
     */
    const SEX_MALE   = 0;
    
    /**
     * 1 女性
     * @var integer
     */
    const SEX_FEMAIL = 1;
    
    /**
     * 2 性别待定
     * @var integer
     */
    const SEX_UNKNOW = 2;
    
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::ACCEPT           => '接收消息或有图',  
        self::NOTACCEPT        => '不接受消息或无图',
        self::SEX_MALE         => '男',
        self::SEX_FEMAIL       => '女',
        self::SEX_UNKNOW       => '未知',
    );
}