<?php
/**
 * 话题来源关系表
 * @author huwei
 */
class Source_Object_Source extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'source';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Source_Object_Source';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'name', 'url', 'type', 'create_user', 'update_user', 'create_time', 'update_time', 'group');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'name'        => 'name',
        'url'         => 'url',
        'type'        => 'type',
        'create_user' => 'createUser',
        'update_user' => 'updateUser',
        'create_time' => 'createTime',
        'update_time' => 'updateTime',
        'group'       => 'group',
    );

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'type'        => 1,
        'create_user' => 1,
        'update_user' => 1,
        'create_time' => 1,
        'update_time' => 1,
        'group'       => 1,
    );

    /**
     * @param array $data
     * @return Source_Object_Source
     */
    public static function init($data) {
        return parent::initObject(self::CLASSNAME, $data);
    }

    /**
     * id
     * @var integer
     */
    public $id;

    /**
     * 来源名称
     * @var string
     */
    public $name;

    /**
     * URL规则串
     * @var string
     */
    public $url;

    /**
     * 来源类型,1:微信公众号，2:其他
     * @var integer
     */
    public $type;

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
     * 来源的分组
     * @var integer
     */
    public $group;

}
