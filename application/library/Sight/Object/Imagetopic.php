<?php
/**
 * 景点图文关系表
 * @author huwei
 */
class Sight_Object_Imagetopic extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'sight_imagetopic';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Sight_Object_Imagetopic';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'sight_id', 'imagetopic_id', 'create_time', 'update_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'              => 'id',
        'sight_id'        => 'sightId',
        'imagetopic_id'   => 'imagetopicId',
        'create_time'     => 'createTime',
        'update_time'     => 'updateTime',
    );

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'              => 1,
        'sight_id'        => 1,
        'imagetopic_id'   => 1,
        'create_time'     => 1,
        'update_time'     => 1,
    );

    /**
     * @param array $data
     * @return Sight_Object_Imagetopic
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
     * 图文id
     * @var integer
     */
    public $imagetopicId;

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
