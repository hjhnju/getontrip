<?php
/**
 * 后台管理页面controller基础类
 * @author huwei
 */
class Base_Controller_Admin extends Base_Controller_Page {
    
    protected $loginUrl  = '/admin/login';

    public function init() {
        parent::init();

        //未登录自动跳转
        $name           = '';
        $logicUser      = new User_Logic_Third();
        $this->userid   = $logicUser->checkLogin();
        if(!empty($this->userid)){
            $logicAdmin = new Admin_Logic_Admin;
            $name       = $logicAdmin->getUserName($this->userid);
        }
        
        
        if($this->needLogin && empty($this->userid)){
            //$u        = isset($_REQUEST['HTTP_REFERER']) ? $_REQUEST['HTTP_REFERER'] : null;
            $u        = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
            $loginUrl = $this->loginUrl ? $this->loginUrl : Base_Config::getConfig('web')->loginurl;
            if(!empty($u)){
                $loginUrl = $loginUrl . '?' . http_build_query(array('u'=>$u));
            }
            if($this->isAjax()){
                return $this->ajaxJump($loginUrl);
            }else{
                return $this->redirect($loginUrl);
            }
        }
        
        //为页面统一assign用户信息
        $this->getView()->assign("username",$name);
        $this->getView()->assign("userid",$this->userid);
        
        // 定义的默认的action
        $controller = $this->_request->controller;
        $action     = $this->_request->action;
        $filename   = 'modules/' . MODULE . '/actions/' . $controller . '/' . ucfirst($action) . '.php';
        
        $this->actions = array(
            $action => $filename,
        );
        
        $uri = $this->_request->getRequestUri();
        $this->_view->assign('uri', $uri);

        //覆盖用户端的feroot
        $this->getView()->assign('feroot', $this->webroot . '/asset');
    }
}
