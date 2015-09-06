<?php
/**
 * 列表
 * @author fanyy
 *
 */
class ListAction extends Yaf_Action_Abstract {
    public function execute() {  
    	$statusArray=Msg_Type_Status::$names;
        $statusArray=array_reverse($statusArray,true);
        
        $typeArray=Msg_Type_Type::$names;
        $typeArray=array_reverse($typeArray,true);
        


    	$this->getView()->assign('statusArray', $statusArray);
    	$this->getView()->assign('typeArray', $typeArray);
 
    }
}
