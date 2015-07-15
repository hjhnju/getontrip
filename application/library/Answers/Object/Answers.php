<?php
/**
 * 答案信息表
 * @author huwei
 */
class Answers_Object_Answers extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'answers';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Answers_Object_Answers';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'topic_id', 'content', 'user_id', 'from', 'status', 'create_time', 'update_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'topic_id'    => 'topicId',
        'content'     => 'content',
        'user_id'     => 'userId',
        'from'        => 'from',
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
        'topic_id'    => 1,
        'user_id'     => 1,
        'status'      => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * @param array $data
     * @return Answers_Object_Answers
     */
    public static function init($data) {
        return parent::initObject(self::CLASSNAME, $data);
    }

    /**
     * 答案id
     * @var integer
     */
    public $id;

    /**
     * 话题id
     * @var integer
     */
    public $topicId;

    /**
     * 答案内容
     * @var string
     */
    public $content;

    /**
     * 回答用户ID
     * @var integer
     */
    public $userId;

    /**
     * 内部系统回答答案来源
     * @var string
     */
    public $from;

    /**
     * 答案状态
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
