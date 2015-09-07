<?php
/**
 * 意见反馈 列表类
 * @author huwei
 */
class Advise_List_Advise extends Base_List {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'advise';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'userid', 'content', 'status', 'create_time', 'update_time', 'deal_time', 'type');

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'userid'      => 1,
        'status'      => 1,
        'create_time' => 1,
        'update_time' => 1,
        'deal_time'   => 1,
        'type'        => 1,
    );

    /**
     * 获取数据的对象数组
     * @return array|Advise_Object_Advise[]
     * 返回的是一个数组，每个元素是一个Loan_Object_Attach对象
     */
    public function getObjects() {
        return parent::getObjects('Advise_Object_Advise');
    }

}