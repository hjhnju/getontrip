<?php
/**
 * 书籍信息表
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
    protected $fields = array('id', 'title', 'author', 'press', 'content_desc', 'catalog', 'url', 'image', 'isbn', 'price_jd', 'price_mart', 'pages', 'status', 'create_time', 'update_time', 'create_user', 'update_user');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'title'       => 'title',
        'author'      => 'author',
        'press'       => 'press',
        'content_desc'=> 'contentDesc',
        'catalog'     => 'catalog',
        'url'         => 'url',
        'image'       => 'image',
        'isbn'        => 'isbn',
        'price_jd'    => 'priceJd',
        'price_mart'  => 'priceMart',
        'pages'       => 'pages',
        'status'      => 'status',
        'create_time' => 'createTime',
        'update_time' => 'updateTime',
        'create_user' => 'createUser',
        'update_user' => 'updateUser',
    );

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'price_jd'    => 1,
        'price_mart'  => 1,
        'pages'       => 1,
        'status'      => 1,
        'create_time' => 1,
        'update_time' => 1,
        'create_user' => 1,
        'update_user' => 1,
    );

    /**
     * @param array $data
     * @return Book_Object_Book
     */
    public static function init($data) {
        return parent::initObject(self::CLASSNAME, $data);
    }

    /**
     * 书籍id
     * @var integer
     */
    public $id;

    /**
     * 书名
     * @var string
     */
    public $title;

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
     * 内容摘要
     * @var string
     */
    public $contentDesc;

    /**
     * 目录
     * @var string
     */
    public $catalog;

    /**
     * 链接
     * @var string
     */
    public $url;

    /**
     * 图片
     * @var string
     */
    public $image;

    /**
     * ISBN
     * @var string
     */
    public $isbn;

    /**
     * 创建时间
     * @var integer
     */
    public $priceJd;

    /**
     * 创建时间
     * @var integer
     */
    public $priceMart;

    /**
     * 页码
     * @var integer
     */
    public $pages;

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

    /**
     * 创建人
     * @var integer
     */
    public $createUser;

    /**
     * 更新人
     * @var integer
     */
    public $updateUser;

}
