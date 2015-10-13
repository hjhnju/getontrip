<?php
/**
 * 用户登录及信息修改相关操作
 */
class ApiController extends Base_Controller_Page{
    
    protected $logicLogin;
    
    protected $logicUser;
    
    public function init(){
        //未登录不跳转
        $this->setNeedLogin(false);

        parent::init();
        
        $this->logicLogin = new User_Logic_Third();
        
        $this->logicUser  = new User_Logic_User();
    } 
    
    /**
     * 接口1：/api/user/login
     * 登录接口
     * @param integer openId
     * @param integer type,第三方登录类型，1:qq,2:weixin,3:weibo
     * @return json
     * 设置用户的登录态
     */
    public function loginAction(){
        $openId   = isset($_REQUEST['openId'])?intval($_REQUEST['openId']):'';
        $type     = isset($_REQUEST['type'])?intval($_REQUEST['type']):'';
        if(empty($openId) || empty($type)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $ret = $this->logicLogin->setLogin($openId,$type);
        if($ret){
            return $this->ajax($ret);
        }
        return $this->ajaxError();
    }

    /**
     * 接口2：/api/user/signOut
     * 退出登录接口
     * @return json
     */
    public function signOutAction(){
        $ret = $this->logicLogin->signOut();
        if($ret){
            return $this->ajax();
        }
        return $this->ajaxError();
    }      
    
    /**
     * 接口3：/api/user/getinfo
     * 用户信息获取接口
     * @param integer type,第三方登录类型，1:qq,2:weixin,3:weibo
     * @return json
     */
    public function getinfoAction() {
        $type     = isset($_REQUEST['type'])?intval($_REQUEST['type']):'';
        if(empty($type)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $ret      = $this->logicUser->getUserInfo($type);
        $this->ajax($ret);
    }
    
    /**
     * 接口4：/api/user/addinfo
     * 用户信息添加接口
     * @param integer userid，用户ID
     * @param integer type,第三方登录类型，1:qq,2:weixin,3:weibo
     * @param string  param,eg: param=nick_name:aa,type:jpg,image:bb,sex:1
     * @return json
     */
    public function addinfoAction() {
        $type       = isset($_REQUEST['type'])?intval($_REQUEST['type']):'';
        $strParam   = isset($_REQUEST['param'])?trim($_REQUEST['param']):'';
        if(empty($strParam) || empty($type)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $ret        = $this->logicUser->addUserInfo($type, $strParam);
        if($ret){
            return $this->ajax();
        }
        return $this->ajaxError();
    }
    
    /**
     * 接口5：/api/user/editinfo
     * 用户信息修改接口
     * @param integer type,第三方登录类型，1:qq,2:weixin,3:weibo
     * @param string  param,eg: param=nick_name:aa,sex:1
     * @param file  file,上传的图像
     * @return json
     */
    public function editinfoAction(){
        $type       = isset($_REQUEST['type'])?intval($_REQUEST['type']):'';
        $strParam   = isset($_REQUEST['param'])?trim($_REQUEST['param']):'';
        $file       = isset($_FILES['file'])?$_FILES['file']:'';
        if(empty($type)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $ret        = $this->logicUser->editUserInfo($type, $strParam, $file);
        if($ret){
            return $this->ajax();
        }
        return $this->ajaxError();
    }
}
