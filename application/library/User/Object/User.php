<?php
/**
 * 用户信息表
 * @author huwei
 */
class User_Object_User extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'user';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'User_Object_User';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'device_id', 'nick_name', 'city', 'image', 'sex', 'accept_pic', 'accept_msg', 'create_time', 'update_time', 'email', 'passwd');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'device_id'   => 'deviceId',
        'nick_name'   => 'nickName',
        'city'        => 'city',
        'image'       => 'image',
        'sex'         => 'sex',
        'accept_pic'  => 'acceptPic',
        'accept_msg'  => 'acceptMsg',
        'create_time' => 'createTime',
        'update_time' => 'updateTime',
        'email'       => 'email',
        'passwd'      => 'passwd',
    );

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'sex'         => 1,
        'accept_pic'  => 1,
        'accept_msg'  => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * @param array $data
     * @return User_Object_User
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
     * 设备ID
     * @var string
     */
    public $deviceId;

    /**
     * 用户昵称
     * @var string
     */
    public $nickName;

    /**
     * 城市
     * @var string
     */
    public $city;

    /**
     * 用户图像
     * @var string
     */
    public $image;

    /**
     * 性别，0男性,1:女性,2表示还不确定
     * @var integer
     */
    public $sex;

    /**
     * 是否图模式
     * @var integer
     */
    public $acceptPic;

    /**
     * 是否接口消息
     * @var integer
     */
    public $acceptMsg;

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
     * 用户邮箱
     * @var string
     */
    public $email;

    /**
     * 用户密码
     * @var string
     */
    public $passwd;

}
