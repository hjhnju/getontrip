<?php
/**
 * 商店信息表
 * @author huwei
 */
class Shop_Object_Shop extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'shop';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Shop_Object_Shop';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'title', 'image', 'addr', 'phone', 'status', 'score', 'type', 'create_user', 'update_user', 'create_time', 'update_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'title'       => 'title',
        'image'       => 'image',
        'addr'        => 'addr',
        'phone'       => 'phone',
        'status'      => 'status',
        'score'       => 'score',
        'type'        => 'type',
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
        'status'      => 1,
        'type'        => 1,
        'create_user' => 1,
        'update_user' => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * @param array $data
     * @return Shop_Object_Shop
     */
    public static function init($data) {
        return parent::initObject(self::CLASSNAME, $data);
    }

    /**
     *  id
     * @var integer
     */
    public $id;

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

}
