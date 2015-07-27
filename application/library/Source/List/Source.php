<?php
/**
 * 话题来源关系表 列表类
 * @author huwei
 */
class Source_List_Source extends Base_List {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'source';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'name', 'url', 'create_time', 'update_time', 'type');

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'create_time' => 1,
        'update_time' => 1,
        'type'        => 1,
    );

    /**
     * 获取数据的对象数组
     * @return array|Source_Object_Source[]
     * 返回的是一个数组，每个元素是一个Loan_Object_Attach对象
     */
    public function getObjects() {
        return parent::getObjects('Source_Object_Source');
    }

}