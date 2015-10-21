<?php
/**
 * 用户信息表 列表类
 * @author huwei
 */
class User_List_User extends Base_List {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'user';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'type';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'device_id', 'type', 'nick_name', 'city', 'image', 'sex', 'accept_pic', 'accept_msg', 'create_time', 'update_time');

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'type'        => 1,
        'sex'         => 1,
        'accept_pic'  => 1,
        'accept_msg'  => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * 获取数据的对象数组
     * @return array|User_Object_User[]
     * 返回的是一个数组，每个元素是一个Loan_Object_Attach对象
     */
    public function getObjects() {
        return parent::getObjects('User_Object_User');
    }

}