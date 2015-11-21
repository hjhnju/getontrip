<?php
/**
 * 举报信息表
 * @author huwei
 */
class Report_Object_Report extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'report';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Report_Object_Report';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'type', 'objid', 'userid', 'status', 'create_time', 'update_time', 'update_user');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'type'        => 'type',
        'objid'       => 'objid',
        'userid'      => 'userid',
        'status'      => 'status',
        'create_time' => 'createTime',
        'update_time' => 'updateTime',
        'update_user' => 'updateUser',
    );

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'type'        => 1,
        'objid'       => 1,
        'userid'      => 1,
        'status'      => 1,
        'create_time' => 1,
        'update_time' => 1,
        'update_user' => 1,
    );

    /**
     * @param array $data
     * @return Report_Object_Report
     */
    public static function init($data) {
        return parent::initObject(self::CLASSNAME, $data);
    }

    /**
     * 自增id
     * @var integer
     */
    public $id;

    /**
     * 类型 1:评论举报
     * @var integer
     */
    public $type;

    /**
     * 对象id
     * @var integer
     */
    public $objid;

    /**
     * 举报人的id
     * @var integer
     */
    public $userid;

    /**
     * 状态:
     * @var integer
     */
    public $status;

    /**
     * 创建时间
     * @var integer
     */
    public $createTime;

    /**
     * 更新时间
     * @var integer
     */
    public $updateTime;

    /**
     * 更新人
     * @var integer
     */
    public $updateUser;

}
