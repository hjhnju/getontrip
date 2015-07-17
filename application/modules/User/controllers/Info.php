<?php
/**
 * 用户信息查询编辑
 * @author huwei
 *
 */
class InfoController extends Base_Controller_Api {
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();        
    }
    
    /**
     * 接口1：/user/info
     * 用户信息获取接口
     * @param integer deviceId
     * @return json
     */
    public function indexAction() {
        $deviceId      = $_POST['deviceId'];
        $logic      = new User_Logic_Info();
        $ret        = $logic->getUserInfo($deviceId);
        $this->ajax($ret);
    }   
    
    /**
     * 接口2：/user/info/edit
     * 用户信息修改接口
     * @param integer deviceId
     * @param array 
     */
    public function editAction(){
        $deviceId      = $_POST['deviceId'];
        $deviceId      = $_POST['deviceId'];
        $logic      = new User_Logic_Info();
        $ret        = $logic->getUserInfo($deviceId);
        $this->ajax($ret);
    }
}
