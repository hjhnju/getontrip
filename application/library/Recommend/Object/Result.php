<?php
/**
 * 推荐结果表
 * @author huwei
 */
class Recommend_Object_Result extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'recommend_result';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Recommend_Object_Result';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'obj_id', 'label_id', 'label_type', 'rate', 'reason', 'status', 'create_time', 'update_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'obj_id'      => 'objId',
        'label_id'    => 'labelId',
        'label_type'  => 'labelType',
        'rate'        => 'rate',
        'reason'      => 'reason',
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
        'obj_id'      => 1,
        'label_id'    => 1,
        'label_type'  => 1,
        'status'      => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * @param array $data
     * @return Recommend_Object_Result
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
     * 文章ID
     * @var integer
     */
    public $objId;

    /**
     * 类型ID，可能是景点或通用标签
     * @var integer
     */
    public $labelId;

    /**
     * label类型：1景点，2通用标签 
     * @var integer
     */
    public $labelType;

    /**
     * 推荐的评分
     * @var 
     */
    public $rate;

    /**
     * 推荐的理由
     * @var string
     */
    public $reason;

    /**
     * 状态:1未处理 2已确认3拒绝
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
