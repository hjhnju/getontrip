<?php
/**
 * 景点美食关系表 列表类
 * @author huwei
 */
class Sight_List_Food extends Base_List {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'sight_food';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'sight_id', 'food_id', 'weight', 'create_time', 'update_time');

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'sight_id'    => 1,
        'food_id'     => 1,
        'weight'      => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * 获取数据的对象数组
     * @return array|Sight_Object_Food[]
     * 返回的是一个数组，每个元素是一个Loan_Object_Attach对象
     */
    public function getObjects() {
        return parent::getObjects('Sight_Object_Food');
    }

}