<?php
/**
 * 评论信息表
 * @author huwei
 */
class Comment_Object_Comment extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'comment';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Comment_Object_Comment';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'obj_id', 'from_user_id', 'to_user_id', 'status', 'content', 'create_time', 'update_time', 'up_id', 'type');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'obj_id'      => 'objId',
        'from_user_id'=> 'fromUserId',
        'to_user_id'  => 'toUserId',
        'status'      => 'status',
        'content'     => 'content',
        'create_time' => 'createTime',
        'update_time' => 'updateTime',
        'up_id'       => 'upId',
        'type'        => 'type',
    );

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'obj_id'      => 1,
        'from_user_id'=> 1,
        'to_user_id'  => 1,
        'status'      => 1,
        'create_time' => 1,
        'update_time' => 1,
        'up_id'       => 1,
        'type'        => 1,
    );

    /**
     * @param array $data
     * @return Comment_Object_Comment
     */
    public static function init($data) {
        return parent::initObject(self::CLASSNAME, $data);
    }

    /**
     * 评论id
     * @var integer
     */
    public $id;

    /**
     * 话题id
     * @var integer
     */
    public $objId;

    /**
     * from的用户ID
     * @var integer
     */
    public $fromUserId;

    /**
     * to的用户ID
     * @var integer
     */
    public $toUserId;

    /**
     * 评论状态
     * @var integer
     */
    public $status;

    /**
     * 评论内容
     * @var string
     */
    public $content;

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
     * 上级评论id
     * @var integer
     */
    public $upId;

    /**
     * 评论类型
     * @var integer
     */
    public $type;

}
