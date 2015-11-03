<?php
/**
 * 书籍信息表 列表类
 * @author huwei
 */
class Sight_List_Book extends Base_List {
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
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'sight_id', 'book_id', 'create_time', 'update_time', 'weight');

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
     * 获取数据的对象数组
     * @return array|Sight_Object_Book[]
     * 返回的是一个数组，每个元素是一个Loan_Object_Attach对象
     */
    public function getObjects() {
        return parent::getObjects('Sight_Object_Book');
    }

}