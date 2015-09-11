<?php
/**
 * 用户列表
 * @author fanyy
 *
 */
class ListAction extends Yaf_Action_Abstract {
    public function execute() {
        $sexArray=array(1 => '男',2 => '女' );
    	$this->getView()->assign('sexArray', $sexArray);

    }
}