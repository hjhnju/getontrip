<?php
/**
 * 标签信息表
 * @author huwei
 */
class Tag_Object_Tag extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'tag';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Tag_Object_Tag';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'name', 'create_user', 'update_user', 'create_time', 'update_time', 'type');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'name'        => 'name',
        'create_user' => 'createUser',
        'update_user' => 'updateUser',
        'create_time' => 'createTime',
        'update_time' => 'updateTime',
        'type'        => 'type',
    );

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'create_user' => 1,
        'update_user' => 1,
        'create_time' => 1,
        'update_time' => 1,
        'type'        => 1,
    );

    /**
     * @param array $data
     * @return Tag_Object_Tag
     */
    public static function init($data) {
        return parent::initObject(self::CLASSNAME, $data);
    }

    /**
     * 标签id
     * @var integer
     */
    public $id;

    /**
     * 标签名称
     * @var string
     */
    public $name;

    /**
     * 
     * @var integer
     */
    public $createUser;

    /**
     * 
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
     * 状态 1变通标签 2通用标签
     * @var integer
     */
    public $type;

}
