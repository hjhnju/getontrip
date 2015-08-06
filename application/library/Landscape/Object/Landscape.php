<?php
/**
 * 景观信息表
 * @author huwei
 */
class Landscape_Object_Landscape extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'landscape';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Landscape_Object_Landscape';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'name', 'title', 'image', 'content', 'author', 'x', 'y', 'status', 'create_time', 'update_time');

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
        'x'           => 'x',
        'y'           => 'y',
        'status'      => 'status',
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
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * @param array $data
     * @return Landscape_Object_Landscape
     */
    public static function init($data) {
        return parent::initObject(self::CLASSNAME, $data);
    }

    /**
     * 景观id
     * @var integer
     */
    public $id;

    /**
     * 景观名称
     * @var string
     */
    public $name;

    /**
     * 景观副标题
     * @var string
     */
    public $title;

    /**
     * 景观背景图片
     * @var string
     */
    public $image;

    /**
     * 景观内容
     * @var string
     */
    public $content;

    /**
     * 景观作者
     * @var string
     */
    public $author;

    /**
     * 经度
     * @var 
     */
    public $x;

    /**
     * 纬度
     * @var 
     */
    public $y;

    /**
     * 景观状态
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

}
