<?php
/**
 * 视频信息
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
    protected $fields = array('id', 'sight_id', 'title', 'from', 'url', 'image', 'status', 'type', 'create_time', 'update_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'sight_id'    => 'sightId',
        'title'       => 'title',
        'from'        => 'from',
        'url'         => 'url',
        'image'       => 'image',
        'status'      => 'status',
        'type'        => 'type',
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
        'type'        => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * @param array $data
     * @return Video_Object_Video
     */
    public static function init($data) {
        return parent::initObject(self::CLASSNAME, $data);
    }

    /**
     *  ID
     * @var integer
     */
    public $id;

    /**
     * 景点ID
     * @var integer
     */
    public $sightId;

    /**
     * 标题
     * @var string
     */
    public $title;

    /**
     * 来源
     * @var string
     */
    public $from;

    /**
     * 链接
     * @var string
     */
    public $url;

    /**
     * 背景图
     * @var string
     */
    public $image;

    /**
     * 状态 
     * @var integer
     */
    public $status;

    /**
     * 类型1:专辑，2:单视频 
     * @var integer
     */
    public $type;

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
