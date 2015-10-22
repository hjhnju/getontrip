<?php
/**
 * 访问信息表
 * @author huwei
 */
class Tongji_Object_Visit extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'tongji_visit';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Tongji_Object_Visit';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'user', 'type', 'obj_id', 'create_time', 'update_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'user'        => 'user',
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
        'type'        => 1,
        'obj_id'      => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * @param array $data
     * @return Tongji_Object_Visit
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
     * 用户session_id
     * @var string
     */
    public $user;

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
