<?php
/**
 * 图文信息表
 * @author huwei
 */
class Imagetopic_Object_Imagetopic extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'imagetopic';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Imagetopic_Object_Imagetopic';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'title', 'content', 'image', 'hot', 'status', 'owner', 'create_time', 'update_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'title'       => 'title',
        'content'     => 'content',
        'image'       => 'image',
        'hot'         => 'hot',
        'status'      => 'status',
        'owner'       => 'owner',
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
        'owner'       => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * @param array $data
     * @return Imagetopic_Object_Imagetopic
     */
    public static function init($data) {
        return parent::initObject(self::CLASSNAME, $data);
    }

    /**
     * 图文id
     * @var integer
     */
    public $id;

    /**
     * 图文标题
     * @var string
     */
    public $title;

    /**
     * 图文内容
     * @var string
     */
    public $content;

    /**
     * 图文图片
     * @var string
     */
    public $image;

    /**
     * 图文热度
     * @var 
     */
    public $hot;

    /**
     * 图文状态
     * @var integer
     */
    public $status;

    /**
     * 图文创建人ID
     * @var integer
     */
    public $owner;

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
