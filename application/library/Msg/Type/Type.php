<?php
/**
 * 消息类型
 * @author huwei
 *
 */
class Msg_Type_Type extends Base_Type {
    /**
     * 1 系统消息
     * @var integer
     */
    const SYSTEM = 1;
    
    /**
     * 2 回复消息
     * @var integer
     */
    const REPLY = 2;
   
    /**
     * 默认key名
     * @var string
     */
    const DEFAULT_KEYNAME = 'type';
    
    /**
     * 默认类型属性名
     * @var string
     */
    const DEFAULT_FIELD = 'msg_type';
    
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::SYSTEM           => '系统消息',  
        self::REPLY            => '回复消息',
    );
    
    /**
     * 消息映射
     * 1 系统消息
     * 2 回复消息
     */
    public static $_arrMsgMap = array(
        self::SYSTEM => array(
            'title'    => '%s',
            'content'  => '%s',
            'link'     => '/guide',
        ),
        self::REPLY => array(
            'title'    => '',
            'content'  => '%s回复了您的评论',
            'link'     => '/comment',
        ),
    );
}