<?php
/**
 * 第三方登录信息表 列表类
 * @author huwei
 */
class User_List_Third extends Base_List {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'user_third';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'user_id', 'auth_type', 'open_id', 'create_time', 'update_time', 'login_time');

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'user_id'     => 1,
        'auth_type'   => 1,
        'create_time' => 1,
        'update_time' => 1,
        'login_time'  => 1,
    );

    /**
     * 获取数据的对象数组
     * @return array|User_Object_Third[]
     * 返回的是一个数组，每个元素是一个Loan_Object_Attach对象
     */
    public function getObjects() {
        return parent::getObjects('User_Object_Third');
    }

}