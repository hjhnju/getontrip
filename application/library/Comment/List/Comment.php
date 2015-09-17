<?php
/**
 * 评论信息表 列表类
 * @author huwei
 */
class Comment_List_Comment extends Base_List {
    /**
     * 数据表名
     * @var string
     */
    protected $table = 'comment';

    /**
     * 主键
     * @var string
     */
    protected $prikey = 'id';

    /**
     * 对象包含的所有字段
     * @var array
     */
    protected $fields = array('id', 'obj_id', 'from_user_id', 'to_user_id', 'status', 'content', 'create_time', 'update_time', 'up_id', 'type');

    /**
     * 整数类型的字段
     * @var array
     */
    protected $intProps = array(
        'id'          => 1,
        'obj_id'      => 1,
        'from_user_id'=> 1,
        'to_user_id'  => 1,
        'status'      => 1,
        'create_time' => 1,
        'update_time' => 1,
        'up_id'       => 1,
        'type'        => 1,
    );

    /**
     * 获取数据的对象数组
     * @return array|Comment_Object_Comment[]
     * 返回的是一个数组，每个元素是一个Loan_Object_Attach对象
     */
    public function getObjects() {
        return parent::getObjects('Comment_Object_Comment');
    }

}