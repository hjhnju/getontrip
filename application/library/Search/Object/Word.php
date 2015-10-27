<?php
/**
 * 热门搜索词表
 * @author huwei
 */
class Search_Object_Word extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'search_word';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Search_Object_Word';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'word', 'deviceid', 'create_time', 'update_time', 'userid', 'status');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'word'        => 'word',
        'deviceid'    => 'deviceid',
        'create_time' => 'createTime',
        'update_time' => 'updateTime',
        'userid'      => 'userid',
        'status'      => 'status',
    );

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'create_time' => 1,
        'update_time' => 1,
        'userid'      => 1,
        'status'      => 1,
    );

    /**
     * @param array $data
     * @return Search_Object_Word
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
     * 搜索词
     * @var string
     */
    public $word;

    /**
     * 设备ID
     * @var string
     */
    public $deviceid;

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
     * 用户id
     * @var integer
     */
    public $userid;

    /**
     * 状态1:未审核,2:审核通过,3:审核通过
     * @var integer
     */
    public $status;

}
