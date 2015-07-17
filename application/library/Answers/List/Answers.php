<?php
/**
 * 答案信息表 列表类
 * @author huwei
 */
class Answers_List_Answers extends Base_List {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'answers';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'topic_id', 'content', 'user_id', 'from', 'status', 'anonymous', 'create_time', 'update_time');

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'topic_id'    => 1,
        'user_id'     => 1,
        'status'      => 1,
        'anonymous'   => 1,
        'create_time' => 1,
        'update_time' => 1,
    );

    /**
     * 获取数据的对象数组
     * @return array|Answers_Object_Answers[]
     * 返回的是一个数组，每个元素是一个Loan_Object_Attach对象
     */
    public function getObjects() {
        return parent::getObjects('Answers_Object_Answers');
    }

}