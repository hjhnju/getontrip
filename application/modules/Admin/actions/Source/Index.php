<?php
/**
 * 话题来源管理
 * @author fyy
 *
 */
class IndexAction extends Yaf_Action_Abstract {
    
    public function execute() { 
        $list = Source_Api::listType(1,PHP_INT_MAX,array());
        $this->getView()->assign('groupList', $list['list']);

        $typeArray=Source_Type_Type::$names;
        $typeArray=array_reverse($typeArray,true);
        $this->getView()->assign('typeArray', $typeArray); 
   
    }
}
