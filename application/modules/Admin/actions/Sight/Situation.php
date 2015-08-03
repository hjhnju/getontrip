<?php
/**
 * 景点编辑情况
 * @author fanyy
 *
 */
class SituationAction extends Yaf_Action_Abstract {
    public function execute() { 
    	$statusTypeArray=Topic_Type_Status::$names;
    	$this->getView()->assign('statusTypeArray', $statusTypeArray);
    }
}
