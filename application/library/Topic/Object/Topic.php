<?php
/**
 * 话题信息表
 * @author huwei
 */
class Topic_Object_Topic extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'topic';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Topic_Object_Topic';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'title', 'subtitle', 'content', 'desc', 'image', 'create_user', 'update_user', 'from', 'url', 'status', 'x', 'y', 'hot1', 'hot2', 'hot3', 'create_time', 'update_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'title'       => 'title',
        'subtitle'    => 'subtitle',
        'content'     => 'content',
        'desc'        => 'desc',
        'image'       => 'image',
        'create_user' => 'createUser',
        'update_user' => 'updateUser',
        'from'        => 'from',
        'url'         => 'url',
        'status'      => 'status',
        'x'           => 'x',
        'y'           => 'y',
        'hot1'        => 'hot1',
        'hot2'        => 'hot2',
        'hot3'        => 'hot3',
        'create_time' => 'createTime',
        'update_time' => 'updateTime',
    );

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'create_user' => 1,
        'update_user' => 1,
        'from'        => 1,
        'status'      => 1,
        'hot1'        => 1,
        'hot2'        => 1,
        'hot3'        => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * @param array $data
     * @return Topic_Object_Topic
     */
    public static function init($data) {
        return parent::initObject(self::CLASSNAME, $data);
    }

    /**
     * 话题id
     * @var integer
     */
    public $id;

    /**
     * 标题
     * @var string
     */
    public $title;

    /**
     * 副标题
     * @var string
     */
    public $subtitle;

    /**
     * 话题内容
     * @var string
     */
    public $content;

    /**
     * 补充描述
     * @var string
     */
    public $desc;

    /**
     * 话题背景图片
     * @var string
     */
    public $image;

    /**
     * 话题作者ID
     * @var integer
     */
    public $createUser;

    /**
     * 
     * @var integer
     */
    public $updateUser;

    /**
     * 话题来源
     * @var integer
     */
    public $from;

    /**
     * 原链接
     * @var string
     */
    public $url;

    /**
     * 话题状态
     * @var integer
     */
    public $status;

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
     * 话题热度:7天
     * @var integer
     */
    public $hot1;

    /**
     * 话题热度:30天
     * @var integer
     */
    public $hot2;

    /**
     * 话题热度:xxx天，供扩展
     * @var integer
     */
    public $hot3;

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
