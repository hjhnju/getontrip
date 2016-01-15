<?php
/**
 * 特产商品表 列表类
 * @author huwei
 */
class Specialty_List_Product extends Base_List {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'specialty_product';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'specialty_id', 'title', 'content', 'image', 'price', 'url', 'status', 'create_user', 'update_user', 'create_time', 'update_time');

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'specialty_id'=> 1,
        'status'      => 1,
        'create_user' => 1,
        'update_user' => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * 获取数据的对象数组
     * @return array|Specialty_Object_Product[]
     * 返回的是一个数组，每个元素是一个Loan_Object_Attach对象
     */
    public function getObjects() {
        return parent::getObjects('Specialty_Object_Product');
    }

}