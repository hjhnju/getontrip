<?php
/**
 * 城市元数据信息
 * @author huwei
 */
class City_Object_Meta extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'city_meta';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'City_Object_Meta';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'name', 'pinyin', 'pid', 'provinceid', 'cityid', 'create_user', 'update_user', 'create_time', 'update_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'name'        => 'name',
        'pinyin'      => 'pinyin',
        'pid'         => 'pid',
        'provinceid'  => 'provinceid',
        'cityid'      => 'cityid',
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
        'pid'         => 1,
        'provinceid'  => 1,
        'cityid'      => 1,
        'create_user' => 1,
        'update_user' => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * @param array $data
     * @return City_Object_Meta
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
     * 名称
     * @var string
     */
    public $name;

    /**
     * 拼音
     * @var string
     */
    public $pinyin;

    /**
     * 父ID 0为省
     * @var integer
     */
    public $pid;

    /**
     * 所属省
     * @var integer
     */
    public $provinceid;

    /**
     * 所属市
     * @var integer
     */
    public $cityid;

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
