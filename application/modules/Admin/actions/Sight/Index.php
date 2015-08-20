<?php
/**
 * 景点管理
 * @author fyy
 *
 */
class IndexAction extends Yaf_Action_Abstract {
    
    public function execute() {
        $statusTypeArray=Sight_Type_Status::$names;
        $statusTypeArray=array_reverse($statusTypeArray,true);
    	$this->getView()->assign('statusTypeArray', $statusTypeArray); 
    }
}
