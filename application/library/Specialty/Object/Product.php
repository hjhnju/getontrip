<?php
/**
 * 特产商品表
 * @author huwei
 */
class Specialty_Object_Product extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'specialty_product';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Specialty_Object_Product';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'specialty_id', 'title', 'content', 'image', 'price', 'url', 'status', 'create_user', 'update_user', 'create_time', 'update_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'specialty_id'=> 'specialtyId',
        'title'       => 'title',
        'content'     => 'content',
        'image'       => 'image',
        'price'       => 'price',
        'url'         => 'url',
        'status'      => 'status',
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
        'specialty_id'=> 1,
        'status'      => 1,
        'create_user' => 1,
        'update_user' => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * @param array $data
     * @return Specialty_Object_Product
     */
    public static function init($data) {
        return parent::initObject(self::CLASSNAME, $data);
    }

    /**
     *  特产商品id
     * @var integer
     */
    public $id;

    /**
     * 特产ID
     * @var integer
     */
    public $specialtyId;

    /**
     * 特产的商品名
     * @var string
     */
    public $title;

    /**
     * 商品内容
     * @var string
     */
    public $content;

    /**
     * 商品图片
     * @var string
     */
    public $image;

    /**
     * 商品价格
     * @var 
     */
    public $price;

    /**
     * 商品购买链接
     * @var string
     */
    public $url;

    /**
     * 状态
     * @var integer
     */
    public $status;

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
