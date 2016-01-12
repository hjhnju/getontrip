<?php
/**
 * 词条列表
 * @author fanyy
 *
 */
class RecommendAction extends Yaf_Action_Abstract {
    public function execute() { 
    	$statusTypeArray = array(
    	    0 => '未处理',
    	    1 => '已接受',
    	    2 => '已拒绝',
    	    3 => '全部状态',
    	);
    	$this->getView()->assign('statusTypeArray', $statusTypeArray);
    }
}
