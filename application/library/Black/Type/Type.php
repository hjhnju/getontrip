<?php
/**
 * 黑名单类型
 * @author huwei
 *
 */
class Black_Type_Type extends Base_Type {
    /**
     * 1 视频
     * @var integer
     */
    const VIDEO = 1;  

    /**
     * 2 书籍
     * @var integer
     */
    const BOOK    = 2;
    
    /**
     * 3 所有类型
     * @var integer
     */  
    const ALL    = 3;
    
    /**
     * 黑名单名称
     * @var array
     */
    public static $names = array(
        self::VIDEO    => '视频',
        self::BOOK     => '书籍',
        self::ALL      => '所有类型',
    );
}