<?php
/**
 * 文章信息表
 * @author huwei
 */
class Recommend_Object_Article extends Base_Object {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'recommend_article';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 类名
     * @var string
     */
    const CLASSNAME = 'Recommend_Object_Article';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'title', 'subtitle', 'keywords', 'content', 'source_id', 'author', 'url', 'issue', 'source', 'create_time', 'update_time');

    /**
     * 字段与属性隐射关系
     * @var array
     */
    public $properties = array(
        'id'          => 'id',
        'title'       => 'title',
        'subtitle'    => 'subtitle',
        'keywords'    => 'keywords',
        'content'     => 'content',
        'source_id'   => 'sourceId',
        'author'      => 'author',
        'url'         => 'url',
        'issue'       => 'issue',
        'source'      => 'source',
        'create_time' => 'createTime',
        'update_time' => 'updateTime',
    );

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'source_id'   => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * @param array $data
     * @return Recommend_Object_Article
     */
    public static function init($data) {
        return parent::initObject(self::CLASSNAME, $data);
    }

    /**
     * 文章id
     * @var integer
     */
    public $id;

    /**
     * 标题
     * @var string
     */
    public $title;

    /**
     * 附标题
     * @var string
     */
    public $subtitle;

    /**
     * 关键词
     * @var string
     */
    public $keywords;

    /**
     * 文章内容
     * @var string
     */
    public $content;

    /**
     * 杂志来源ID
     * @var integer
     */
    public $sourceId;

    /**
     * 作者
     * @var string
     */
    public $author;

    /**
     * 原链接
     * @var string
     */
    public $url;

    /**
     * 期次
     * @var string
     */
    public $issue;

    /**
     * 杂志名称
     * @var string
     */
    public $source;

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
