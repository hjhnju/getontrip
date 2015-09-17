<?php
/**
 * 评论类型
 * @author huwei
 *
 */
class Comment_Type_Type extends Base_Type {
    /**
     * 1 话题评论
     * @var integer
     */
    const TOPIC = 1;
    /**
     * 2 书籍评论
     * @var integer
     */
    const BOOK = 2;  
    /**
     * 3 视频评论
     * @var integer
     */
    const VIDEO = 3;
   
    /**
     * 4 百科评论
     * @var integer
     */
    const WIKI = 4;
    
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::TOPIC    => '话题评论',
        self::BOOK     => '书籍评论',
        self::VIDEO    => '视频评论',
        self::WIKI     => '百科评论',
    );
}