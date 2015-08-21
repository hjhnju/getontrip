<?php
/**
 * 话题列表
 * @author fanyy
 *
 */
class ListAction extends Yaf_Action_Abstract {
    public function execute() { 
    	$statusTypeArray=Theme_Type_Status::$names;
    	$this->getView()->assign('statusTypeArray', $statusTypeArray);
    }
}
