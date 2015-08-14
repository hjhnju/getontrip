<?php
/**
 * 主题信息表
 * @author huwei
 */
class Theme_Object_Theme extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'theme';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Theme_Object_Theme';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'name', 'title', 'image', 'content', 'author', 'status', 'create_user', 'update_user', 'create_time', 'update_time', 'period');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'name'        => 'name',
        'title'       => 'title',
        'image'       => 'image',
        'content'     => 'content',
        'author'      => 'author',
        'status'      => 'status',
        'create_user' => 'createUser',
        'update_user' => 'updateUser',
        'create_time' => 'createTime',
        'update_time' => 'updateTime',
        'period'      => 'period',
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
        'period'      => 1,
    );

    /**
     * @param array $data
     * @return Theme_Object_Theme
     */
    public static function init($data) {
        return parent::initObject(self::CLASSNAME, $data);
    }

    /**
     * 主题id
     * @var integer
     */
    public $id;

    /**
     * 主题名称
     * @var string
     */
    public $name;

    /**
     * 主题副标题
     * @var string
     */
    public $title;

    /**
     * 主题背景图片
     * @var string
     */
    public $image;

    /**
     * 主题内容
     * @var string
     */
    public $content;

    /**
     * 主题作者
     * @var string
     */
    public $author;

    /**
     * 主题状态
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

    /**
     * 主题期数
     * @var integer
     */
    public $period;

}
