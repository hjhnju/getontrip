<?php
/**
 * 特产信息表
 * @author huwei
 */
class Specialty_Object_Specialty extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'specialty';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Specialty_Object_Specialty';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'title', 'content', 'image', 'status', 'create_user', 'update_user', 'create_time', 'update_time', 'type');

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
        'type'        => 'type',
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
        'type'        => 1,
    );

    /**
     * @param array $data
     * @return Specialty_Object_Specialty
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
     * 特产名
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

    /**
     * 特产类型,1:必买
     * @var integer
     */
    public $type;

}
