<?php
/**
 * 标签关系表
 * @author huwei
 */
class Tag_Object_Relation extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'tag_relation';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Tag_Object_Relation';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'toptag_id', 'classifytag_id', 'create_user', 'update_user', 'create_time', 'update_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'              => 'id',
        'toptag_id'       => 'toptagId',
        'classifytag_id'  => 'classifytagId',
        'create_user'     => 'createUser',
        'update_user'     => 'updateUser',
        'create_time'     => 'createTime',
        'update_time'     => 'updateTime',
    );

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'              => 1,
        'toptag_id'       => 1,
        'classifytag_id'  => 1,
        'create_user'     => 1,
        'update_user'     => 1,
        'create_time'     => 1,
        'update_time'     => 1,
    );

    /**
     * @param array $data
     * @return Tag_Object_Relation
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
     * 一级分类标签id
     * @var integer
     */
    public $toptagId;

    /**
     * 普通分类标签id
     * @var integer
     */
    public $classifytagId;

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
