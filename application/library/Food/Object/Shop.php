<?php
/**
 * 商店信息表
 * @author huwei
 */
class Food_Object_Shop extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'food_shop';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Food_Object_Shop';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'food_id', 'title', 'image', 'addr', 'phone', 'url', 'status', 'score', 'type', 'x', 'y', 'create_user', 'update_user', 'create_time', 'update_time', 'price');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'food_id'     => 'foodId',
        'title'       => 'title',
        'image'       => 'image',
        'addr'        => 'addr',
        'phone'       => 'phone',
        'url'         => 'url',
        'status'      => 'status',
        'score'       => 'score',
        'type'        => 'type',
        'x'           => 'x',
        'y'           => 'y',
        'create_user' => 'createUser',
        'update_user' => 'updateUser',
        'create_time' => 'createTime',
        'update_time' => 'updateTime',
        'price'       => 'price',
    );

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'food_id'     => 1,
        'status'      => 1,
        'type'        => 1,
        'create_user' => 1,
        'update_user' => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * @param array $data
     * @return Food_Object_Shop
     */
    public static function init($data) {
        return parent::initObject(self::CLASSNAME, $data);
    }

    /**
     * 商店id
     * @var integer
     */
    public $id;

    /**
     * 美食ID
     * @var integer
     */
    public $foodId;

    /**
     * 名称
     * @var string
     */
    public $title;

    /**
     * 图片
     * @var string
     */
    public $image;

    /**
     * 地址
     * @var string
     */
    public $addr;

    /**
     * 电话
     * @var string
     */
    public $phone;

    /**
     * 店铺URL
     * @var string
     */
    public $url;

    /**
     * 状态
     * @var integer
     */
    public $status;

    /**
     * 评分
     * @var 
     */
    public $score;

    /**
     * 店铺类型
     * @var integer
     */
    public $type;

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
     * 创建人ID
     * @var integer
     */
    public $createUser;

    /**
     * 修改人ID
     * @var integer
     */
    public $updateUser;

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
     * 人均价格
     * @var 
     */
    public $price;

}
