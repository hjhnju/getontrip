<?php
/**
 * 主题景观关系表 列表类
 * @author huwei
 */
class Theme_List_Landscape extends Base_List {
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
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'theme_id', 'landscape_id', 'create_time', 'update_time');

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
     * 获取数据的对象数组
     * @return array|Theme_Object_Landscape[]
     * 返回的是一个数组，每个元素是一个Loan_Object_Attach对象
     */
    public function getObjects() {
        return parent::getObjects('Theme_Object_Landscape');
    }

}