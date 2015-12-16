<?php
/**
 * 首页
 */
class DownloadController extends Base_Controller_Page {
	//初始化
    public function init(){
        $this->setNeedLogin(false);
        parent::init();
    }
    public function indexAction() {
    	 //判断是否来自微信
       $isWeixin = $this->isWeixin();
       $this->getView()->assign('isWeixin', $isWeixin);
       if (!$isWeixin) { 
         return $this->redirect('https://itunes.apple.com/app/id1059746773');
       }
    }

    function isWeixin() {  
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) { 
            return true; 
        }  
        return false; 
    }
}
