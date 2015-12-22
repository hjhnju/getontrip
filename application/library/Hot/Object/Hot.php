<?php
/**
 * 热度信息表
 * @author huwei
 */
class Hot_Object_Hot extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'hot';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Hot_Object_Hot';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'obj_id', 'obj_type', 'type', 'hot', 'create_time', 'update_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'obj_id'      => 'objId',
        'obj_type'    => 'objType',
        'type'        => 'type',
        'hot'         => 'hot',
        'create_time' => 'createTime',
        'update_time' => 'updateTime',
    );

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'obj_id'      => 1,
        'obj_type'    => 1,
        'type'        => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * @param array $data
     * @return Hot_Object_Hot
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
     * 对象id
     * @var integer
     */
    public $objId;

    /**
     * 对象类型
     * @var integer
     */
    public $objType;

    /**
     * 热度类型
     * @var integer
     */
    public $type;

    /**
     * 热度值
     * @var 
     */
    public $hot;

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
