<?php
/**
 * 收藏信息表
 * @author huwei
 */
class Collect_Object_Collect extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'collect';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Collect_Object_Collect';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'user_id', 'type', 'obj_id', 'create_time', 'update_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'user_id'     => 'userId',
        'type'        => 'type',
        'obj_id'      => 'objId',
        'create_time' => 'createTime',
        'update_time' => 'updateTime',
    );

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'user_id'     => 1,
        'type'        => 1,
        'obj_id'      => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * @param array $data
     * @return Collect_Object_Collect
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
     * 用户ID
     * @var integer
     */
    public $userId;

    /**
     * 收藏类型
     * @var integer
     */
    public $type;

    /**
     * 收藏对象ID
     * @var integer
     */
    public $objId;

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
