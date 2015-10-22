<?php
/**
 * 视频信息表
 * @author huwei
 */
class Video_Object_Video extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'video';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Video_Object_Video';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'sight_id', 'title', 'url', 'image', 'from', 'len', 'type', 'status', 'create_time', 'update_time', 'create_user', 'update_user', 'guid');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'sight_id'    => 'sightId',
        'title'       => 'title',
        'url'         => 'url',
        'image'       => 'image',
        'from'        => 'from',
        'len'         => 'len',
        'type'        => 'type',
        'status'      => 'status',
        'create_time' => 'createTime',
        'update_time' => 'updateTime',
        'create_user' => 'createUser',
        'update_user' => 'updateUser',
        'guid'        => 'guid',
    );

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'sight_id'    => 1,
        'type'        => 1,
        'status'      => 1,
        'create_time' => 1,
        'update_time' => 1,
        'create_user' => 1,
        'update_user' => 1,
    );

    /**
     * @param array $data
     * @return Video_Object_Video
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
     * 景点id
     * @var integer
     */
    public $sightId;

    /**
     * 视频名称
     * @var string
     */
    public $title;

    /**
     * 链接
     * @var string
     */
    public $url;

    /**
     * 图片
     * @var string
     */
    public $image;

    /**
     * 来源
     * @var string
     */
    public $from;

    /**
     * 长度
     * @var string
     */
    public $len;

    /**
     * 类型
     * @var integer
     */
    public $type;

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

    /**
     * 视频唯一标识码
     * @var string
     */
    public $guid;

}
