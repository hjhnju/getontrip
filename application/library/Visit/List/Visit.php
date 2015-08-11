<?php
/**
 * 访问信息表 列表类
 * @author huwei
 */
class Visit_List_Visit extends Base_List {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'visit';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'device_id', 'type', 'obj_id', 'create_time', 'update_time');

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'device_id'   => 1,
        'type'        => 1,
        'obj_id'      => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * 获取数据的对象数组
     * @return array|Visit_Object_Visit[]
     * 返回的是一个数组，每个元素是一个Loan_Object_Attach对象
     */
    public function getObjects() {
        return parent::getObjects('Visit_Object_Visit');
    }

}