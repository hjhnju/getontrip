<?php
/**
 * 点赞类型
 * @author huwei
 *
 */
class Praise_Type_Type extends Base_Type {
    /**
     * 1 话题点赞
     * @var integer
     */
    const TOPIC = 1;
    
    /**
     * 2 书籍点赞
     * @var integer
     */
    const BOOK  = 2;
    
    /**
     * 3 视频点赞
     * @var integer
     */
    const VIDEO = 3;
    
    /**
     * 4 图文点赞
     * @var integer
     */
    const IMAGETOPIC = 4;
    
    /**
     * 类型名
     * @var array
     */
    public static $names = array(
        self::TOPIC      => '话题点赞',
        self::BOOK       => '书籍点赞',
        self::VIDEO      => '视频点赞',
        self::IMAGETOPIC => '图文点赞',
    );
}