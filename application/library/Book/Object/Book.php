<?php
/**
 * 书籍信息
 * @author huwei
 */
class Book_Object_Book extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'book';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Book_Object_Book';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'sight_id', 'title', 'price_before', 'price_now', 'author', 'press', 'isbn', 'url', 'image', 'status', 'create_time', 'update_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'sight_id'    => 'sightId',
        'title'       => 'title',
        'price_before'=> 'priceBefore',
        'price_now'   => 'priceNow',
        'author'      => 'author',
        'press'       => 'press',
        'isbn'        => 'isbn',
        'url'         => 'url',
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
        'id'          => 1,
        'sight_id'    => 1,
        'status'      => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * @param array $data
     * @return Book_Object_Book
     */
    public static function init($data) {
        return parent::initObject(self::CLASSNAME, $data);
    }

    /**
     *  ID
     * @var integer
     */
    public $id;

    /**
     * 景点ID
     * @var integer
     */
    public $sightId;

    /**
     * 标题
     * @var string
     */
    public $title;

    /**
     * 原价
     * @var 
     */
    public $priceBefore;

    /**
     * 现价
     * @var 
     */
    public $priceNow;

    /**
     * 作者
     * @var string
     */
    public $author;

    /**
     * 出版社
     * @var string
     */
    public $press;

    /**
     * ISBN
     * @var string
     */
    public $isbn;

    /**
     * 链接
     * @var string
     */
    public $url;

    /**
     * 背景图
     * @var string
     */
    public $image;

    /**
     * 状态 
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
