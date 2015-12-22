<?php
/**
 * 城市信息 列表类
 * @author huwei
 */
class City_List_City extends Base_List {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'city';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'name', 'status', 'x', 'y', 'create_time', 'update_time', 'image', 'create_user', 'update_user', 'is_china', 'continent');

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'status'      => 1,
        'create_time' => 1,
        'update_time' => 1,
        'create_user' => 1,
        'update_user' => 1,
        'is_china'    => 1,
    );

    /**
     * 获取数据的对象数组
     * @return array|City_Object_City[]
     * 返回的是一个数组，每个元素是一个Loan_Object_Attach对象
     */
    public function getObjects() {
        return parent::getObjects('City_Object_City');
    }

}