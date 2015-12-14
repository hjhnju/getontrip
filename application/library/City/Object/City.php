<?php
/**
 * 城市信息
 * @author huwei
 */
class City_Object_City extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'city';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'City_Object_City';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'status', 'x', 'y', 'create_time', 'update_time', 'image', 'create_user', 'update_user', 'is_china', 'continent');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'status'      => 'status',
        'x'           => 'x',
        'y'           => 'y',
        'create_time' => 'createTime',
        'update_time' => 'updateTime',
        'image'       => 'image',
        'create_user' => 'createUser',
        'update_user' => 'updateUser',
        'is_china'    => 'isChina',
        'continent'   => 'continent',
    );

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'status'      => 1,
        'create_time' => 1,
        'update_time' => 1,
        'create_user' => 1,
        'update_user' => 1,
        'is_china'    => 1,
    );

    /**
     * @param array $data
     * @return City_Object_City
     */
    public static function init($data) {
        return parent::initObject(self::CLASSNAME, $data);
    }

    /**
     * 
     * @var integer
     */
    public $id;

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
     * 城市图像
     * @var string
     */
    public $image;

    /**
     * 城市创建人ID
     * @var integer
     */
    public $createUser;

    /**
     * 城市修改人ID
     * @var integer
     */
    public $updateUser;

    /**
     * 是否国内
     * @var integer
     */
    public $isChina;

    /**
     * 大洲
     * @var string
     */
    public $continent;

}
