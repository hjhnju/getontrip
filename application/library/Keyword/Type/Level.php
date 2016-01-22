<?php
/**
 * 词条层级类型
 * @author huwei
 *
 */
class Keyword_Type_Level extends Base_Type {
    /**
     * 1 城市级,sight_id为城市id
     * @var integer
     */
    const CITY = 1;  

    /**
     * 2 景点级,sight_id为景点id
     * @var integer
     */
    const SIGHT        = 2;
        
    /**
     * 3 景观级,sight_id为景观id
     * @var integer
     */
    const LANDSCAPE    = 3;
    
    /**
     * 4 二级景观,sight_id为景观id
     * @var integer
     */
    const SECOND_LANDSCAPE    = 4;
    
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::SIGHT        => '景点级',
        self::CITY         => '城市级',
        self::LANDSCAPE    => '景观级',
    );
}