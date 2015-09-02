<?php
/**
 * 消息状态类型
 * @author huwei
 *
 */
class Msg_Type_Status extends Base_Type {
    /**
     * 1 已读
     * @var integer
     */
    const READ     = 1;  
 
    /**
     * 2 未读
     * @var integer
     */
    const UNREAD    = 2;
    
    /**
     * 3 删除
     * @var unknown
     */
    const DEL       = 3;
    
    /**
     * 4 所有
     * @var unknown
     */
    const ALL       = 4;
    
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::READ     => '未读',
        self::UNREAD   => '已读',
        self::DEL      => '删除',
        self::ALL      => '所有的',
    );
}