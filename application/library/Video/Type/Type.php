<?php
/**
 * 视频类型
 * @author huwei
 *
 */
class Video_Type_Type extends Base_Type {
    /**
     * 1 专辑
     * @var integer
     */
    const ALBUM  = 1;  

    /**
     * 2 单视频
     * @var integer
     */
    const VIDEO  = 2;
   
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::ALBUM     => '专辑',
        self::VIDEO     => '单视频',
    );
}