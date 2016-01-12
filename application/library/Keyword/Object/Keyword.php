<?php
/**
 * 景点的相关词条信息表
 * @author huwei
 */
class Keyword_Object_Keyword extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'keyword';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Keyword_Object_Keyword';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'sight_id', 'name', 'url', 'create_time', 'update_time', 'status', 'create_user', 'update_user', 'weight', 'x', 'y', 'content', 'image', 'audio', 'alias', 'level', 'type', 'audio_len');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'sight_id'    => 'sightId',
        'name'        => 'name',
        'url'         => 'url',
        'create_time' => 'createTime',
        'update_time' => 'updateTime',
        'status'      => 'status',
        'create_user' => 'createUser',
        'update_user' => 'updateUser',
        'weight'      => 'weight',
        'x'           => 'x',
        'y'           => 'y',
        'content'     => 'content',
        'image'       => 'image',
        'audio'       => 'audio',
        'alias'       => 'alias',
        'level'       => 'level',
        'type'        => 'type',
        'audio_len'   => 'audioLen',
    );

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'sight_id'    => 1,
        'create_time' => 1,
        'update_time' => 1,
        'status'      => 1,
        'create_user' => 1,
        'update_user' => 1,
        'weight'      => 1,
        'level'       => 1,
        'type'        => 1,
    );

    /**
     * @param array $data
     * @return Keyword_Object_Keyword
     */
    public static function init($data) {
        return parent::initObject(self::CLASSNAME, $data);
    }

    /**
     * id
     * @var integer
     */
    public $id;

    /**
     * 来源名称
     * @var integer
     */
    public $sightId;

    /**
     * 词条名
     * @var string
     */
    public $name;

    /**
     * 词条的网址
     * @var string
     */
    public $url;

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
     * 状态 1未确认 2已确认
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
     * 权重值，权重为1的是主词条
     * @var integer
     */
    public $weight;

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
     * 百科内容
     * @var string
     */
    public $content;

    /**
     * 百科中的图片
     * @var string
     */
    public $image;

    /**
     * 景观的音频
     * @var string
     */
    public $audio;

    /**
     * 景观别名
     * @var string
     */
    public $alias;

    /**
     * 级别 1:一级景观，2:二级景观,二级景观时,sight_id为一级景观id
     * @var integer
     */
    public $level;

    /**
     * 1:必玩
     * @var integer
     */
    public $type;

    /**
     * 音频的时长
     * @var string
     */
    public $audioLen;

}
