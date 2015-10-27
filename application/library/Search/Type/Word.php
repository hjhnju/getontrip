<?php
/**
 * 搜索热词状态
 * @author huwei
 *
 */
class Search_Type_Word extends Base_Type {
    
    /**
     * 审核中
     * @var integer
     */
    const AUDITING     = 1;
    
    /**
     * 审核通过
     * @var integer
     */
    const AUDITPASS    = 2;
    
    /**
     * 审核未通过
     * @var integer
     */
    const AUDITFAILED  = 3;
   
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::AUDITING      => '审核中',
        self::AUDITPASS     => '审核通过',
        self::AUDITFAILED   => '审核未通过',
    );
}