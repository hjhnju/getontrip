<?php
/**
 * 搜索标签关系信息表
 * @author huwei
 */
class Search_Object_Label extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'search_label';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Search_Object_Label';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'obj_id', 'label_id', 'type', 'create_user', 'update_user', 'create_time', 'update_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'obj_id'      => 'objId',
        'label_id'    => 'labelId',
        'type'        => 'type',
        'create_user' => 'createUser',
        'update_user' => 'updateUser',
        'create_time' => 'createTime',
        'update_time' => 'updateTime',
    );

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'obj_id'      => 1,
        'label_id'    => 1,
        'type'        => 1,
        'create_user' => 1,
        'update_user' => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * @param array $data
     * @return Search_Object_Label
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
     * 城市ID或景点ID
     * @var integer
     */
    public $objId;

    /**
     * 标签ID
     * @var integer
     */
    public $labelId;

    /**
     * 状态 1景点 2城市
     * @var integer
     */
    public $type;

    /**
     * 标签创建人ID
     * @var integer
     */
    public $createUser;

    /**
     * 标签修改人ID
     * @var integer
     */
    public $updateUser;

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
