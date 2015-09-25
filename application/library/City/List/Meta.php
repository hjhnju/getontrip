<?php
/**
 * 城市元数据信息 列表类
 * @author huwei
 */
class City_List_Meta extends Base_List {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'city_meta';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'name', 'pinyin', 'pid', 'provinceid', 'cityid', 'create_user', 'update_user', 'create_time', 'update_time');

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'pid'         => 1,
        'provinceid'  => 1,
        'cityid'      => 1,
        'create_user' => 1,
        'update_user' => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * 获取数据的对象数组
     * @return array|City_Object_Meta[]
     * 返回的是一个数组，每个元素是一个Loan_Object_Attach对象
     */
    public function getObjects() {
        return parent::getObjects('City_Object_Meta');
    }

}