<?php
/**
 * 百科词条的目录信息
 * @author huwei
 */
class Wiki_Object_Catalog extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'wiki_catalog';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Wiki_Object_Catalog';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'wiki_id', 'catalog', 'url', 'create_time', 'update_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'wiki_id'     => 'wikiId',
        'catalog'     => 'catalog',
        'url'         => 'url',
        'create_time' => 'createTime',
        'update_time' => 'updateTime',
    );

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'wiki_id'     => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * @param array $data
     * @return Wiki_Object_Catalog
     */
    public static function init($data) {
        return parent::initObject(self::CLASSNAME, $data);
    }

    /**
     *  ID
     * @var integer
     */
    public $id;

    /**
     * 百科ID
     * @var integer
     */
    public $wikiId;

    /**
     * 目录名
     * @var string
     */
    public $catalog;

    /**
     * 链接
     * @var string
     */
    public $url;

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
