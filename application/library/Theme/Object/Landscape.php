<?php
/**
 * 主题景观关系表
 * @author huwei
 */
class Theme_Object_Landscape extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'theme_landscape';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Theme_Object_Landscape';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'theme_id', 'landscape_id', 'create_time', 'update_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'theme_id'    => 'themeId',
        'landscape_id'=> 'landscapeId',
        'create_time' => 'createTime',
        'update_time' => 'updateTime',
    );

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'theme_id'    => 1,
        'landscape_id'=> 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * @param array $data
     * @return Theme_Object_Landscape
     */
    public static function init($data) {
        return parent::initObject(self::CLASSNAME, $data);
    }

    /**
     * 自增id
     * @var integer
     */
    public $id;

    /**
     * 主题id
     * @var integer
     */
    public $themeId;

    /**
     * 景观id
     * @var integer
     */
    public $landscapeId;

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

}
