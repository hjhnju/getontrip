<?php
/**
 * 列表
 * @author fanyy
 *
 */
class ListAction extends Yaf_Action_Abstract {
    public function execute() { 
    	$statusTypeArray=Comment_Type_Status::$names;
      $statusTypeArray=array_reverse($statusTypeArray,true);
    	$this->getView()->assign('statusTypeArray', $statusTypeArray);
 
    }
}
