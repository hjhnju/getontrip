<?php
/**
 * 图文状态类型
 * @author huwei
 *
 */
class Imagetopic_Type_Status extends Base_Type {
    /**
     * 1 未发布
     * @var integer
     */
    const NOTPUBLISHED = 1;  

    /**
     * 审核中
     * @var integer
     */
    const AUDITING     = 2;
    
    /**
     * 审核通过
     * @var integer
     */
    const AUDITPASS    = 3;
    
    /**
     * 审核未通过
     * @var integer
     */
    const AUDITFAILED  = 4;
    
    /**
     * 已发布
     * @var integer
     */
    const PUBLISHED    = 5;
        
    /**
     * 已删除
     * @var integer
     */
    const DELETED      = 6;
    
    /**
     * 状态名
     * @var array
     */
    public static $names = array(
        self::NOTPUBLISHED     => '未发布',
        self::AUDITING         => '审核中',
        self::AUDITPASS        => '审核通过',
        self::AUDITFAILED      => '审核未通过',
        self::PUBLISHED        => '已发布',
        self::DELETED          => '已删除',
    );
}