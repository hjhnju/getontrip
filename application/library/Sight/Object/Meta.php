<?php
/**
 * 景点库 数据表
 * @author huwei
 */
class Sight_Object_Meta extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'sight_meta';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Sight_Object_Meta';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'name', 'level', 'image', 'describe', 'impression', 'address', 'type', 'continent', 'country', 'province', 'city', 'region', 'is_china', 'x', 'y', 'url', 'status', 'weight', 'city_id', 'create_time', 'update_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'name'        => 'name',
        'level'       => 'level',
        'image'       => 'image',
        'describe'    => 'describe',
        'impression'  => 'impression',
        'address'     => 'address',
        'type'        => 'type',
        'continent'   => 'continent',
        'country'     => 'country',
        'province'    => 'province',
        'city'        => 'city',
        'region'      => 'region',
        'is_china'    => 'isChina',
        'x'           => 'x',
        'y'           => 'y',
        'url'         => 'url',
        'status'      => 'status',
        'weight'      => 'weight',
        'city_id'     => 'cityId',
        'create_time' => 'createTime',
        'update_time' => 'updateTime',
    );

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'is_china'    => 1,
        'status'      => 1,
        'weight'      => 1,
        'city_id'     => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * @param array $data
     * @return Sight_Object_Meta
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
     * 景点级别
     * @var string
     */
    public $level;

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
     * 大家印象
     * @var string
     */
    public $impression;

    /**
     * 景点地址
     * @var string
     */
    public $address;

    /**
     * 景点类型
     * @var string
     */
    public $type;

    /**
     * 大洲
     * @var string
     */
    public $continent;

    /**
     * 所属国家
     * @var string
     */
    public $country;

    /**
     * 所属省份
     * @var string
     */
    public $province;

    /**
     * 所属城市
     * @var string
     */
    public $city;

    /**
     * 所属区县
     * @var string
     */
    public $region;

    /**
     * 是否国内
     * @var integer
     */
    public $isChina;

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
     * 百度旅游链接url
     * @var string
     */
    public $url;

    /**
     * 状态：是否已经添加至景点
     * @var integer
     */
    public $status;

    /**
     * 权重值
     * @var integer
     */
    public $weight;

    /**
     * city_meta表对应的城市ID
     * @var integer
     */
    public $cityId;

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
