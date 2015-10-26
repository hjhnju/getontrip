<?php
/**
 * 标签管理
 * @author fyy
 *
 */
class EditAction extends Yaf_Action_Abstract {
    
    public function execute() {
        $typeArray=Tag_Type_Tag::$names;
        //$typeArray=array_reverse($typeArray,true);
        $this->getView()->assign('typeArray', $typeArray);   
   
    }
}
