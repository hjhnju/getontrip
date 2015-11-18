<?php
/**
 * 首页
 */
class IndexController extends Base_Controller_Page {
	//初始化
    public function init(){
        $this->setNeedLogin(false);
        parent::init();
    }
    public function indexAction() {
    	 //判断是否来自移动端
       $isMobile = Base_Util_Mobile::isMobile(); 
       $this->getView()->assign('isMobile', $isMobile); 
    }
}
