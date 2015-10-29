<?php
/**
 * 书籍信息表 列表类
 * @author huwei
 */
class Book_List_Book extends Base_List {
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
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'title', 'author', 'press', 'content_desc', 'catalog', 'url', 'image', 'isbn', 'price_jd', 'price_mart', 'pages', 'status', 'create_time', 'update_time', 'create_user', 'update_user', 'publish_time', 'weight');

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
        'weight'      => 1,
    );

    /**
     * 获取数据的对象数组
     * @return array|Book_Object_Book[]
     * 返回的是一个数组，每个元素是一个Loan_Object_Attach对象
     */
    public function getObjects() {
        return parent::getObjects('Book_Object_Book');
    }

}