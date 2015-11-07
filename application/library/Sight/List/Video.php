<?php
/**
 * 景点视频关系表 列表类
 * @author huwei
 */
class Sight_List_Video extends Base_List {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'sight_video';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'sight_id', 'video_id', 'weight', 'create_time', 'update_time');

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'sight_id'    => 1,
        'video_id'    => 1,
        'weight'      => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * 获取数据的对象数组
     * @return array|Sight_Object_Video[]
     * 返回的是一个数组，每个元素是一个Loan_Object_Attach对象
     */
    public function getObjects() {
        return parent::getObjects('Sight_Object_Video');
    }

}