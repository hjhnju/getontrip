<?php
/**
 * 管理员信息表 列表类
 * @author huwei
 */
class Admin_List_Admin extends Base_List {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'admin';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'name', 'passwd', 'role', 'create_time', 'update_time', 'login_time');

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'role'        => 1,
        'create_time' => 1,
        'update_time' => 1,
        'login_time'  => 1,
    );

    /**
     * 获取数据的对象数组
     * @return array|Admin_Object_Admin[]
     * 返回的是一个数组，每个元素是一个Loan_Object_Attach对象
     */
    public function getObjects() {
        return parent::getObjects('Admin_Object_Admin');
    }

}