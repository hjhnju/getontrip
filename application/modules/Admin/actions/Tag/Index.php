<?php
/**
 * 标签管理
 * @author fyy
 *
 */
class IndexAction extends Yaf_Action_Abstract {
    
    public function execute() {
        $typeArray=Tag_Type_Tag::$names;
        unset($typeArray[Tag_Type_Tag::NORMAL]);
        //$typeArray=array_reverse($typeArray,true);
        $this->getView()->assign('typeArray', $typeArray);   
   
    }
}
