<?php
/**
 * 消息类型
 * @author huwei
 *
 */
class Msg_Type extends Base_Type {
    /**
     * 1 系统消息
     * @var integer
     */
    const SYSTEM = 1;
   
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
    );
}