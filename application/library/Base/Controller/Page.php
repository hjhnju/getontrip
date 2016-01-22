<?php
/**
 * 用户页面controller基础类
 * @author hejunhua
 */
class Base_Controller_Page extends Base_Controller_Abstract {

    public function init(){
        //增加日志字段
        $this->addBaseLogs(array('type'=>'page'));

        parent::init();

        $this->getView()->assign('webroot', $this->webroot);
        $this->getView()->assign('stroot',Base_Config::getConfig('web')->stroot);
        $feversion = Base_Config::getConfig('web')->version;
        $this->getView()->assign('feroot', Base_Config::getConfig('web')->stroot . '/v1/'. $feversion . '/asset');
        $this->getView()->assign('tongji', Base_Config::getConfig('web')->tongji);

        //set csrf token
        $secretKey = Base_Config::getConfig('app')->secret;
        $time      = time();
        $token     = hash("sha256",$secretKey.$time).$time;
        $this->getView()->assign('token', $token);
        
        $isMobile = Base_Util_Mobile::isMobile();
        $this->getView()->assign('isMobile', $isMobile);
    }

    protected function isAjax(){
        return false;
    }

    public function redirect($url){
        parent::redirect($url);
        exit;
    }
}
