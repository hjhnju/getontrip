<?php
class UtilsController  extends Base_Controller_Admin {
	 protected $needLogin = false;
  
     //初始化
     public function init(){
        $this->setNeedLogin(false);
        parent::init(); 
        //$this->getView()->assign('data', $sidebar);
     }
}
