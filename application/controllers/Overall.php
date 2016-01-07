<?php
/**
 * 首页
 */
class OverallController extends Base_Controller_Page {
	//初始化
    public function init(){
        $this->setNeedLogin(false);
        parent::init();
    }
    public function indexAction() { 
        if (Base_Util_Mobile::isMobile()) {
          $this->redirect('/index');
        }
    }
}
