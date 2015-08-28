<?php
/**
 * 黑名单信息表
 * @author huwei
 */
class Black_Object_Black extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'black';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Black_Object_Black';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'type', 'obj_id', 'create_time', 'update_time', 'create_user', 'update_user');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'type'        => 'type',
        'obj_id'      => 'objId',
        'create_time' => 'createTime',
        'update_time' => 'updateTime',
        'create_user' => 'createUser',
        'update_user' => 'updateUser',
    );

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'type'        => 1,
        'create_time' => 1,
        'update_time' => 1,
        'create_user' => 1,
        'update_user' => 1,
    );

    /**
     * @param array $data
     * @return Black_Object_Black
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
     * 类型，1:视频,2:书籍
     * @var integer
     */
    public $type;

    /**
     * 访问对象ID
     * @var string
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

    /**
     * 创建人
     * @var integer
     */
    public $createUser;

    /**
     * 更新人
     * @var integer
     */
    public $updateUser;

}
