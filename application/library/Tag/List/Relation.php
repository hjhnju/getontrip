<?php
/**
 * 标签关系表 列表类
 * @author huwei
 */
class Tag_List_Relation extends Base_List {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'tag_relation';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'toptag_id', 'classifytag_id', 'create_user', 'update_user', 'create_time', 'update_time');

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'              => 1,
        'toptag_id'       => 1,
        'classifytag_id'  => 1,
        'create_user'     => 1,
        'update_user'     => 1,
        'create_time'     => 1,
        'update_time'     => 1,
    );

    /**
     * 获取数据的对象数组
     * @return array|Tag_Object_Relation[]
     * 返回的是一个数组，每个元素是一个Loan_Object_Attach对象
     */
    public function getObjects() {
        return parent::getObjects('Tag_Object_Relation');
    }

}