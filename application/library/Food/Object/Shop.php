<?php
/**
 * 美食商店关系表
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
    protected $fields = array('id', 'food_id', 'shop_id', 'create_time', 'update_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'food_id'     => 'foodId',
        'shop_id'     => 'shopId',
        'create_time' => 'createTime',
        'update_time' => 'updateTime',
    );

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'food_id'     => 1,
        'shop_id'     => 1,
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
     *  id
     * @var integer
     */
    public $id;

    /**
     * 美食id
     * @var integer
     */
    public $foodId;

    /**
     * 店铺id
     * @var integer
     */
    public $shopId;

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
