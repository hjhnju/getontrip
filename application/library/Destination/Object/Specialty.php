<?php
/**
 * 景点特产关系表
 * @author huwei
 */
class Destination_Object_Specialty extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'destination_specialty';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Destination_Object_Specialty';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'destination_id', 'destination_type', 'specialty_id', 'weight', 'create_time', 'update_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'              => 'id',
        'destination_id'  => 'destinationId',
        'destination_type'=> 'destinationType',
        'specialty_id'    => 'specialtyId',
        'weight'          => 'weight',
        'create_time'     => 'createTime',
        'update_time'     => 'updateTime',
    );

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'              => 1,
        'destination_id'  => 1,
        'destination_type'=> 1,
        'specialty_id'    => 1,
        'weight'          => 1,
        'create_time'     => 1,
        'update_time'     => 1,
    );

    /**
     * @param array $data
     * @return Destination_Object_Specialty
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
     * 目的地id
     * @var integer
     */
    public $destinationId;

    /**
     * 目的地类型
     * @var integer
     */
    public $destinationType;

    /**
     * 特产id
     * @var integer
     */
    public $specialtyId;

    /**
     * 权重值
     * @var integer
     */
    public $weight;

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
