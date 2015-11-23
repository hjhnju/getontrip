<?php
/**
 * 访问类型
 * @author huwei
 *
 */
class Tongji_Type_Visit extends Base_Type {
    /**
     * 1 访问话题
     * @var integer
     */
    const TOPIC = 1;
    /**
     * 2 访问景点
     * @var integer
     */
    const SIGHT = 2;  
    
    /**
     * 3 书籍中间页的访问
     * @var integer
     */
    const BOOK  = 3;
    
    /**
     * 4 视频中间页的访问
     * @var unknown
     */
    const VIDEO = 4;
   
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::TOPIC      => '访问话题',
        self::SIGHT      => '访问景点',
        self::BOOK       => '访问书籍',
        self::VIDEO      => '访问视频',
    );
}