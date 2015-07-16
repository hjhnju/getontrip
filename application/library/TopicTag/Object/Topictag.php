<?php
/**
 * 标签信息表
 * @author huwei
 */
class Topictag_Object_Topictag extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'topic_tag';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Topictag_Object_Topictag';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'topic_id', 'tag_id', 'create_time', 'update_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'topic_id'    => 'topicId',
        'tag_id'      => 'tagId',
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
        'tag_id'      => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * @param array $data
     * @return Topic_tag_Object_Topic_tag
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
     * 话题id
     * @var integer
     */
    public $topicId;

    /**
     * 标签id
     * @var integer
     */
    public $tagId;

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
