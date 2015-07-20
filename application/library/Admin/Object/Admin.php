<?php
/**
 * 管理员信息表
 * @author huwei
 */
class Admin_Object_Admin extends Base_Object {
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
     * 类名
     * @var string
     */
    const CLASSNAME = 'Admin_Object_Admin';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'name', 'passwd', 'role', 'create_time', 'update_time', 'login_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'name'        => 'name',
        'passwd'      => 'passwd',
        'role'        => 'role',
        'create_time' => 'createTime',
        'update_time' => 'updateTime',
        'login_time'  => 'loginTime',
    );

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
     * @param array $data
     * @return Admin_Object_Admin
     */
    public static function init($data) {
        return parent::initObject(self::CLASSNAME, $data);
    }

    /**
     * 用户ID
     * @var integer
     */
    public $id;

    /**
     * 用户名
     * @var string
     */
    public $name;

    /**
     * 密码
     * @var string
     */
    public $passwd;

    /**
     * 用户身份类型
     * @var integer
     */
    public $role;

    /**
     * 创建时间
     * @var integer
     */
    public $createTime;

    /**
     * 更新时间
     * @var integer
     */
    public $updateTime;

    /**
     * 最近登录时间
     * @var integer
     */
    public $loginTime;

}
