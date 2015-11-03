<?php
/**
 * 景点数据表
 * @author huwei
 */
class Sight_Object_Sight extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'sight';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Sight_Object_Sight';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'name', 'image', 'describe', 'level', 'city_id', 'status', 'x', 'y', 'create_user', 'update_user', 'create_time', 'update_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'name'        => 'name',
        'image'       => 'image',
        'describe'    => 'describe',
        'level'       => 'level',
        'city_id'     => 'cityId',
        'status'      => 'status',
        'x'           => 'x',
        'y'           => 'y',
        'create_user' => 'createUser',
        'update_user' => 'updateUser',
        'create_time' => 'createTime',
        'update_time' => 'updateTime',
    );

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'city_id'     => 1,
        'status'      => 1,
        'create_user' => 1,
        'update_user' => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * @param array $data
     * @return Sight_Object_Sight
     */
    public static function init($data) {
        return parent::initObject(self::CLASSNAME, $data);
    }

    /**
     * 景点id
     * @var integer
     */
    public $id;

    /**
     * 景点名称
     * @var string
     */
    public $name;

    /**
     * 景点图像
     * @var string
     */
    public $image;

    /**
     * 景点描述
     * @var string
     */
    public $describe;

    /**
     * 景点级别
     * @var string
     */
    public $level;

    /**
     * 城市ID
     * @var integer
     */
    public $cityId;

    /**
     * 状态
     * @var integer
     */
    public $status;

    /**
     * 经度
     * @var 
     */
    public $x;

    /**
     * 纬度
     * @var 
     */
    public $y;

    /**
     * 景点创建人ID
     * @var integer
     */
    public $createUser;

    /**
     * 景点修改人ID
     * @var integer
     */
    public $updateUser;

    /**
     * 景点创建时间
     * @var integer
     */
    public $createTime;

    /**
     * 景点更新时间
     * @var integer
     */
    public $updateTime;

}
