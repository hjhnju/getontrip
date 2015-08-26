<?php
/**
 * 后台登录
 * @author jiangsongfang
 *
 */
class IndexAction extends Base_Controller_Action {
    public function execute() { 
         $redirectUri   = isset($_REQUEST['u']) ? trim($_REQUEST['u']) : '';
         $this->_view->assign('redirectUri',$redirectUri);
       
    }
}