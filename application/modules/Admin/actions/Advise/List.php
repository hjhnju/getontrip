<?php
/**
 * 列表
 * @author fanyy
 *
 */
class ListAction extends Yaf_Action_Abstract {
    public function execute() {   
    	$statusArray=Advise_Type_Status::$names; 
        //$statusArray=array_reverse($statusArray,true); 

    	$typeArray=Advise_Type_Type::$names;
        $typeArray=array_reverse($typeArray,true); 

    	$this->getView()->assign('statusArray', $statusArray);
    	$this->getView()->assign('typeArray', $typeArray);
 
    }
}
