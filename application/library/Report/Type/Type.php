<?php
/**
 * 举报类型
 * @author huwei
 *
 */
class Report_Type_Type extends Base_Type {
    /**
     * 1 评论举报
     * @var integer
     */
    const COMMENT = 1;
    
    /**
     * 类型名
     * @var array
     */
    public static $names = array(
        self::COMMENT    => '评论举报',
    );
}