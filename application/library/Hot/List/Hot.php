<?php
/**
 * 热度信息表 列表类
 * @author huwei
 */
class Hot_List_Hot extends Base_List {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'hot';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'obj_id', 'obj_type', 'type', 'hot', 'create_time', 'update_time');

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'obj_id'      => 1,
        'obj_type'    => 1,
        'type'        => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * 获取数据的对象数组
     * @return array|Hot_Object_Hot[]
     * 返回的是一个数组，每个元素是一个Loan_Object_Attach对象
     */
    public function getObjects() {
        return parent::getObjects('Hot_Object_Hot');
    }

}