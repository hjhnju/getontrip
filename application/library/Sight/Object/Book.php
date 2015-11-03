<?php
/**
 * 书籍信息表
 * @author huwei
 */
class Sight_Object_Book extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'sight_book';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Sight_Object_Book';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'sight_id', 'book_id', 'create_time', 'update_time', 'weight');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'sight_id'    => 'sightId',
        'book_id'     => 'bookId',
        'create_time' => 'createTime',
        'update_time' => 'updateTime',
        'weight'      => 'weight',
    );

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'sight_id'    => 1,
        'book_id'     => 1,
        'create_time' => 1,
        'update_time' => 1,
        'weight'      => 1,
    );

    /**
     * @param array $data
     * @return Sight_Object_Book
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
     * 书籍id
     * @var integer
     */
    public $bookId;

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
     * 权重值
     * @var integer
     */
    public $weight;

}
