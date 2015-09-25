<?php
/**
 * 百科目录信息表
 * @author huwei
 */
class Keyword_Object_Catalog extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'keyword_catalog';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Keyword_Object_Catalog';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'keyword_id', 'name', 'url', 'status', 'create_time', 'update_time', 'create_user', 'update_user');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'keyword_id'  => 'keywordId',
        'name'        => 'name',
        'url'         => 'url',
        'status'      => 'status',
        'create_time' => 'createTime',
        'update_time' => 'updateTime',
        'create_user' => 'createUser',
        'update_user' => 'updateUser',
    );

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'keyword_id'  => 1,
        'status'      => 1,
        'create_time' => 1,
        'update_time' => 1,
        'create_user' => 1,
        'update_user' => 1,
    );

    /**
     * @param array $data
     * @return Keyword_Object_Catalog
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
     * 词条id
     * @var integer
     */
    public $keywordId;

    /**
     * 目录名
     * @var string
     */
    public $name;

    /**
     * 链接
     * @var string
     */
    public $url;

    /**
     * 状态
     * @var integer
     */
    public $status;

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
     * 创建人
     * @var integer
     */
    public $createUser;

    /**
     * 更新人
     * @var integer
     */
    public $updateUser;

}
