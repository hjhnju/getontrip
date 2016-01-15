<?php
/**
 * 商店信息表 列表类
 * @author huwei
 */
class Food_List_Shop extends Base_List {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'food_shop';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'food_id', 'title', 'image', 'addr', 'phone', 'url', 'status', 'score', 'type', 'x', 'y', 'create_user', 'update_user', 'create_time', 'update_time', 'price');

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'food_id'     => 1,
        'status'      => 1,
        'type'        => 1,
        'create_user' => 1,
        'update_user' => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * 获取数据的对象数组
     * @return array|Food_Object_Shop[]
     * 返回的是一个数组，每个元素是一个Loan_Object_Attach对象
     */
    public function getObjects() {
        return parent::getObjects('Food_Object_Shop');
    }

}