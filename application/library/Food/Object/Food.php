<?php
/**
 * 美食信息表
 * @author huwei
 */
class Food_Object_Food extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'food';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Food_Object_Food';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'title', 'content', 'image', 'status', 'create_user', 'update_user', 'create_time', 'update_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'title'       => 'title',
        'content'     => 'content',
        'image'       => 'image',
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
        'status'      => 1,
        'create_user' => 1,
        'update_user' => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * @param array $data
     * @return Food_Object_Food
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
     * 标题
     * @var string
     */
    public $title;

    /**
     * 内容
     * @var string
     */
    public $content;

    /**
     * 图片
     * @var string
     */
    public $image;

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
