<?php
/**
 * 点赞类型
 * @author huwei
 *
 */
class Praise_Type extends Base_Type {
    /**
     * 1 答案点赞
     * @var integer
     */
    const ANSWER = 1;    
   
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::ANSWER     => '答案点赞',
    );
}