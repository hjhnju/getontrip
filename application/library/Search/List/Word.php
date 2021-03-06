<?php
/**
 * 热门搜索词表 列表类
 * @author huwei
 */
class Search_List_Word extends Base_List {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'search_word';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'word', 'deviceid', 'create_time', 'update_time', 'userid', 'status');

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'create_time' => 1,
        'update_time' => 1,
        'userid'      => 1,
        'status'      => 1,
    );

    /**
     * 获取数据的对象数组
     * @return array|Search_Object_Word[]
     * 返回的是一个数组，每个元素是一个Loan_Object_Attach对象
     */
    public function getObjects() {
        return parent::getObjects('Search_Object_Word');
    }

}