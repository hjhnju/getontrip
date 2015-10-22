<?php
/**
 * 用户列表
 * @author fanyy
 *
 */
class ListAction extends Yaf_Action_Abstract {
    public function execute() {
        $sexArray=array(0 => '男',1 => '女' );
    	$this->getView()->assign('sexArray', $sexArray);

    }
}