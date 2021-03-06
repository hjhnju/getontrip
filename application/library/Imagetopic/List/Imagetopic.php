<?php
/**
 * 图文信息表 列表类
 * @author huwei
 */
class Imagetopic_List_Imagetopic extends Base_List {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'imagetopic';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'title', 'content', 'image', 'status', 'owner', 'create_time', 'update_time');

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'status'      => 1,
        'owner'       => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * 获取数据的对象数组
     * @return array|Imagetopic_Object_Imagetopic[]
     * 返回的是一个数组，每个元素是一个Loan_Object_Attach对象
     */
    public function getObjects() {
        return parent::getObjects('Imagetopic_Object_Imagetopic');
    }

}