<?php
/**
 * 百科信息
 * @author huwei
 */
class Wiki_Object_Wiki extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'wiki';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Wiki_Object_Wiki';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'sight_id', 'title', 'content', 'image', 'status', 'create_time', 'update_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'sight_id'    => 'sightId',
        'title'       => 'title',
        'content'     => 'content',
        'image'       => 'image',
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
        'sight_id'    => 1,
        'status'      => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * @param array $data
     * @return Wiki_Object_Wiki
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
     * 百科内容
     * @var string
     */
    public $content;

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
