<?php 
/**
* 
*/
class Admin_Type_Action  extends Base_Type
{
     /**
     * 新建
     * @var string
     */
    const ACTION_ADD = 'add';
    /**
     * 新建
     * @var string
     */
    const ACTION_EDIT = 'edit';
      /**
     * 新建
     * @var string
     */
    const ACTION_VIEW = 'view'; 
    
    protected static $names  = array(
        self::ACTION_ADD => '新建',
        self::ACTION_EDIT => '编辑',
        self::ACTION_VIEW => '查看', 
    );
}