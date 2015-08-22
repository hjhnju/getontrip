<?php
/**
 * 景观管理
 * @author fyy
 *
 */
class ListAction extends Yaf_Action_Abstract {
    
    public function execute() {
        $statusTypeArray=Sight_Type_Status::$names;
        $statusTypeArray=array_reverse($statusTypeArray,true);
    	$this->getView()->assign('statusTypeArray', $statusTypeArray); 
    }
}
