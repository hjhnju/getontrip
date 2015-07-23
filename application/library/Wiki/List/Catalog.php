<?php
/**
 * 百科词条的目录信息 列表类
 * @author huwei
 */
class Wiki_List_Catalog extends Base_List {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'wiki_catalog';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'wiki_id', 'catalog', 'url', 'create_time', 'update_time');

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'wiki_id'     => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * 获取数据的对象数组
     * @return array|Wiki_Object_Catalog[]
     * 返回的是一个数组，每个元素是一个Loan_Object_Attach对象
     */
    public function getObjects() {
        return parent::getObjects('Wiki_Object_Catalog');
    }

}