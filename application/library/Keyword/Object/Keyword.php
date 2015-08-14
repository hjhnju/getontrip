<?php
/**
 * 景点的相关词条信息表
 * @author huwei
 */
class Keyword_Object_Keyword extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'keyword';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Keyword_Object_Keyword';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'sight_id', 'name', 'url', 'status', 'create_user', 'update_user', 'create_time', 'update_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'sight_id'    => 'sightId',
        'name'        => 'name',
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
        'sight_id'    => 1,
        'status'      => 1,
        'create_user' => 1,
        'update_user' => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * @param array $data
     * @return Keyword_Object_Keyword
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
     * @var integer
     */
    public $sightId;

    /**
     * 词条名
     * @var string
     */
    public $name;

    /**
     * 词条的网址
     * @var string
     */
    public $url;

    /**
     * 状态 1未确认 2已确认
     * @var integer
     */
    public $status;

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

}
