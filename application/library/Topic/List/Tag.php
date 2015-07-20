<?php
/**
 * 标签信息表 列表类
 * @author huwei
 */
class Topic_List_Tag extends Base_List {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'topic_tag';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'topic_id', 'tag_id', 'create_time', 'update_time');

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'topic_id'    => 1,
        'tag_id'      => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * 获取数据的对象数组
     * @return array|Topic_Object_Tag[]
     * 返回的是一个数组，每个元素是一个Loan_Object_Attach对象
     */
    public function getObjects() {
        return parent::getObjects('Topic_Object_Tag');
    }

}