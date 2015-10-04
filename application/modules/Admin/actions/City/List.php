<?php
/**
 * 准备发布的城市列表 
 * @author fyy
 *
 */  
class ListAction extends Yaf_Action_Abstract {
    public function execute() { 
       $statusArray=City_Type_Status::$names;
       $this->getView()->assign('statusArray', $statusArray);
    }
}
