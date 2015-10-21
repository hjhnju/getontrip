<?php
/**
 * 第三方登录信息表
 * @author huwei
 */
class User_Object_Third extends Base_Object {
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
     * 类名
     * @var string
     */
    const CLASSNAME = 'User_Object_Third';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'user_id', 'auth_type', 'open_id', 'create_time', 'update_time', 'login_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'user_id'     => 'userId',
        'auth_type'   => 'authType',
        'open_id'     => 'openId',
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
        'user_id'     => 1,
        'auth_type'   => 1,
        'create_time' => 1,
        'update_time' => 1,
        'login_time'  => 1,
    );

    /**
     * @param array $data
     * @return User_Object_Third
     */
    public static function init($data) {
        return parent::initObject(self::CLASSNAME, $data);
    }

    /**
     * 自增ID
     * @var integer
     */
    public $id;

    /**
     * 用户ID
     * @var integer
     */
    public $userId;

    /**
     * 第三方openid类型
     * @var integer
     */
    public $authType;

    /**
     * openid
     * @var string
     */
    public $openId;

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
