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
    protected $fields = array('id', 'sight_id', 'title', 'content', 'desc', 'image', 'user_id', 'status', 'x', 'y', 'create_time', 'update_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'sight_id'    => 'sightId',
        'title'       => 'title',
        'content'     => 'content',
        'desc'        => 'desc',
        'image'       => 'image',
        'user_id'     => 'userId',
        'status'      => 'status',
        'x'           => 'x',
        'y'           => 'y',
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
     * 景点id
     * @var integer
     */
    public $sightId;

    /**
     * 副标题
     * @var string
     */
    public $title;

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
     * @var string
     */
    public $userId;

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
