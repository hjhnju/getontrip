<?php
/**
 * 景点元数据
 * @author fyy
 *
 */
class MetalistAction extends Yaf_Action_Abstract {
    
    public function execute() {
       $statusArray=Sight_Type_Meta::$names;
       $this->getView()->assign('statusArray', $statusArray); 
   
    }
}
