<?php
/**
 * 话题列表
 * @author fanyy
 *
 */
class ListAction extends Yaf_Action_Abstract {
    public function execute() {
    	$page 		= $_REQUEST['page']? $_REQUEST['page'] : 1;
    	$pagesize 	= 30;
        /*$data = Apply_Api::getApplyList($page, $pagesize);
        $this->_view->assign('data', $data);*/
    }
}
