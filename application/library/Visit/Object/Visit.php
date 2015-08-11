<?php
/**
 * 访问信息表
 * @author huwei
 */
class Visit_Object_Visit extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'visit';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Visit_Object_Visit';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'device_id', 'type', 'obj_id', 'create_time', 'update_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'device_id'   => 'deviceId',
        'type'        => 'type',
        'obj_id'      => 'objId',
        'create_time' => 'createTime',
        'update_time' => 'updateTime',
    );

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'device_id'   => 1,
        'type'        => 1,
        'obj_id'      => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * @param array $data
     * @return Visit_Object_Visit
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
     * 设备ID
     * @var integer
     */
    public $deviceId;

    /**
     * 访问类型类型，1:话题详情
     * @var integer
     */
    public $type;

    /**
     * 访问对象ID
     * @var integer
     */
    public $objId;

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

}
