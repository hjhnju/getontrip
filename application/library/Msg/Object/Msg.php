<?php
/**
 * 消息
 * @author huwei
 */
class Msg_Object_Msg extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'msg';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'mid';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Msg_Object_Msg';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('mid', 'sender', 'receiver', 'title', 'type', 'content', 'attach', 'image', 'status', 'create_time', 'update_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'mid'         => 'mid',
        'sender'      => 'sender',
        'receiver'    => 'receiver',
        'title'       => 'title',
        'type'        => 'type',
        'content'     => 'content',
        'attach'      => 'attach',
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
        'mid'         => 1,
        'sender'      => 1,
        'receiver'    => 1,
        'type'        => 1,
        'status'      => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * @param array $data
     * @return Msg_Object_Msg
     */
    public static function init($data) {
        return parent::initObject(self::CLASSNAME, $data);
    }

    /**
     * 消息ID
     * @var integer
     */
    public $mid;

    /**
     * 发送人
     * @var integer
     */
    public $sender;

    /**
     * 接收人
     * @var integer
     */
    public $receiver;

    /**
     * 消息标题
     * @var string
     */
    public $title;

    /**
     * 消息类型
     * @var integer
     */
    public $type;

    /**
     * 消息内容
     * @var string
     */
    public $content;

    /**
     * 消息的附加信息
     * @var string
     */
    public $attach;

    /**
     * 消息图片
     * @var string
     */
    public $image;

    /**
     * 状态 1已读 2未读 3删除
     * @var integer
     */
    public $status;

    /**
     * 发送时间
     * @var integer
     */
    public $createTime;

    /**
     * 更新时间
     * @var integer
     */
    public $updateTime;

}
