<?php
/**
 * 搜索热词列表
 * @author huwei
 *
 */
class WordAction extends Yaf_Action_Abstract {
    public function execute() { 
        $statusArray=Search_Type_Word::$names;
        $statusArray=array_reverse($statusArray,true);
    	$this->getView()->assign('statusArray', $statusArray); 
    }
}
