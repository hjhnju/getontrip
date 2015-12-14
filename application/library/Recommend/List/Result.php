<?php
/**
 * 推荐结果表 列表类
 * @author huwei
 */
class Recommend_List_Result extends Base_List {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'recommend_result';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'obj_id', 'label_id', 'label_type', 'rate', 'reason', 'status', 'create_time', 'update_time');

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'obj_id'      => 1,
        'label_id'    => 1,
        'label_type'  => 1,
        'status'      => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * 获取数据的对象数组
     * @return array|Recommend_Object_Result[]
     * 返回的是一个数组，每个元素是一个Loan_Object_Attach对象
     */
    public function getObjects() {
        return parent::getObjects('Recommend_Object_Result');
    }

}