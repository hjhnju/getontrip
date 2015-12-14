<?php
/**
 * 文章信息表 列表类
 * @author huwei
 */
class Recommend_List_Article extends Base_List {
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
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'title', 'subtitle', 'keywords', 'content', 'source_id', 'author', 'url', 'issue', 'source', 'create_time', 'update_time');

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
     * 获取数据的对象数组
     * @return array|Recommend_Object_Article[]
     * 返回的是一个数组，每个元素是一个Loan_Object_Attach对象
     */
    public function getObjects() {
        return parent::getObjects('Recommend_Object_Article');
    }

}