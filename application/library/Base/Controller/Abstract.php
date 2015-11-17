<?php
/**
 * 所有controller的基类
 * 命名不以controller结尾防止Yaf识别
 * @author hejunhua@baidu.com
 * @since 2014-07 重构
 * @author jiangsongfang
 * @since 2014-12 output
 */
class Base_Controller_Abstract extends Yaf_Controller_Abstract
{
    protected $webroot;

    //controller是否需要登录，true则checklogin自动跳转至登录页
    protected $needLogin   = true;

    protected $ajax        = false;
    
    protected $outputView  = 'output.phtml';

    protected $userid      = null;

    //User_Object实例
    protected $objUser     = null;
    
    //子类增加的base日志字段
    protected $addBaseLogs = array();
    
    protected $loginUrl = null;
    
    public function init(){

        $this->webroot = Base_Config::getConfig('web')->root;
        $this->objUser = new stdClass();       
        
        $this->baselog();
        //未登录自动跳转
        $logicUser = new User_Logic_Third();
        $this->userid = $logicUser->checkLogin();
        $arrRequest   = explode("/",$_SERVER['REQUEST_URI']);
        //后台不在这里进行未登录处理,而是在Base_Controller_Admin中处理
        if(isset($arrRequest[1]) && ($arrRequest[1] == 'admin')){
            return ;
        }
        if($this->needLogin && empty($this->userid)){
            header("Content-Type: application/json; charset=UTF-8");
            
            $arrRtInfo = array();
            $arrRtInfo['status'] = Base_RetCode::SESSION_NOT_LOGIN;
            $arrRtInfo['statusInfo'] = '未登录';
            $arrRtInfo['data']= array();
            
            $output = json_encode($arrRtInfo);
            $output = str_replace("<","&lt;",$output);
            $output = str_replace(">","&gt;",$output);
            echo  $output;
            die();
        }        
    }
    
    /**
     * 设置在浏览器端的缓存时间
     * @param number $lifetime
     */
    public function setBrowserCache($md5 = null, $lifetime = 3600) {
        $ts = gmdate("D, d M Y H:i:s", time() + $lifetime) . " GMT";
        header("Expires: $ts");
        header("Pragma: cache");
        header("Cache-Control: max-age=$lifetime");
        if(!is_null($md5)){
            header("ETag: $md5");
        }
        return true;
    }
    
    /**
     * 检测token
     * @param string $token
     * @return boolean
     */
    protected function checkToken($token){
        if(empty($token)){
            return false;
        }
        $len       = strlen($token);
        $secretVal = substr($token,0,64);
        $time      = substr($token,64,$len);
        $secretKey = Base_Config::getConfig('app')->secret;
        if(time() - $time >= 60){
            return false;
        }elseif(hash("sha256",$secretKey.$time) == $secretVal){
            return true;
        }
        return false;
    }
    
    /**
     * 获取整数参数 可以设置一个默认值
     * @param string $key
     * @param number $default
     * @return integer
     */
    public function getInt($key, $default = 0) {
        $num = @$_REQUEST[$key];
        if (empty($num)) {
            $num = $this->_request->getParam($key);
        }
        $num = intval($num);
        if (empty($num)) {
            $num = $default;
        }
        return $num;
    }

    private function baselog(){
        $userid    = 0;
        $logParams = array(
            'module'     => $this->getRequest()->getModuleName(),
            'controller' => $this->getRequest()->getControllerName(),
            'action'     => $this->getRequest()->getActionName(),
            'userid'     => $userid,
        );
        $logParams = array_merge($logParams, $this->addBaseLogs);
        Base_Log::notice($logParams);
    }

    /**
     * 未登录是否跳转
     * @param boolean
     */
    protected function setNeedLogin($needLogin) {
    	
        $this->needLogin = $needLogin;
    }

    /**
     * 添加base日志里的字段
     * 继承类可以在parent::init前调用以增加日志字段
     * @param $arrParams
     * @return  true
     */
    protected function addBaseLogs($arrParams){
        if(!empty($arrParams)){
            $this->addBaseLogs = array_merge($this->addBaseLogs, $arrParams);
        }
    }
    
    protected function checkParam($param, $data) {
        foreach ($param as $key => $msg) {
            if (empty($data[$key])) {
                $this->outputError(Base_RetCode::PARAM_ERROR, $msg);
                return false;
            }
        }
        return true;
    }
    
    protected function isAjax() {
        if ($this->ajax == true) {
            return true;
        }
        if (!empty($_REQUEST['ajax'])) {
            return true;
        }
        return false;
    }
    
    public function output($arrData = array(), $errorMsg = '', $status = 0){
        if ($this->isAjax()) {
            $this->ajax($arrData, $errorMsg, $status);
        } else {
            $this->_view->assign('output', 1);
            $arrRtInfo = array();
            $arrRtInfo['status'] = $status;
            $arrRtInfo['statusInfo'] = $errorMsg;
            $arrRtInfo['data']= $arrData;
            $this->_view->assign('result', $arrRtInfo);
            
            Yaf_Dispatcher::getInstance()->disableView();
            //$this->_view->render($this->outputView);
            $this->_response->setBody($this->_view->render($this->outputView));
            
        }
    }
    
    public function outputError($errorCode = Base_RetCode::UNKNOWN_ERROR, $errorMsg = '', $arrData = array()) {
        if ($this->isAjax()) {
            $this->ajaxError($errorCode, $errorMsg, $arrData);
        } else {
            $this->_view->assign('output', 1);
            $arrRtInfo = array();
            $arrRtInfo['status'] = $errorCode;
            $arrRtInfo['statusInfo'] = $errorMsg;
            $arrRtInfo['data']= $arrData;
            $this->_view->assign('result', $arrRtInfo);
            
            Yaf_Dispatcher::getInstance()->disableView();
            //$this->_view->render($this->outputView);
            $this->_response->setBody($this->_view->render($this->outputView));
        }
    }
    
    public function ajax($arrData = array(), $errorMsg = '', $status = 0){
        header("Content-Type: application/json; charset=UTF-8");
        $arrRtInfo = array();
        $arrRtInfo['status'] = $status;
        $arrRtInfo['statusInfo'] = $errorMsg;
        $arrRtInfo['data']= $arrData;
        
        $output = json_encode($arrRtInfo);
        $output = str_replace("<","&lt;",$output);
        $output = str_replace(">","&gt;",$output);
        echo  $output;//htmlspecialchars($objJsonFormat->getResult(),ENT_NOQUOTES);
        die();
    }
    
	//将转义后的字符进行decode处理，输出未转义原样
	public function ajaxDecode($arrData = array(), $errorMsg = '', $status = 0){
        header("Content-Type: application/json; charset=UTF-8");

        $arrRtInfo = array();
        $arrRtInfo['status'] = $status;
        $arrRtInfo['statusInfo'] = $errorMsg;
        $arrRtInfo['data']= $arrData;
        
        $output = json_encode($arrRtInfo);
        $output = str_replace("&lt;","<",$output);
        $output = str_replace("&gt;",">",$output);
        echo  $output;
        die();
    }

    public function ajaxRaw($arrData){
        header("Content-Type: application/json; charset=UTF-8");
        $output = json_encode($arrData);
        $output = str_replace("<","&lt;",$output);
        $output = str_replace(">","&gt;",$output);
        echo  $output;
        die();
    }
    
    /**
     * ajax输出 支持HTML格式
     * @param array $arrData
     */
    public function ajaxHTML($arrData){
        header("Content-Type: application/json; charset=UTF-8");

        $arrRtInfo = array();
        $arrRtInfo['status'] = 0;
        $arrRtInfo['statusInfo'] = '';
        $arrRtInfo['data']= $arrData;
        
        $output = json_encode($arrRtInfo);
        echo $output;
        die();
    }

    public function jsonp($callback = '', $arrData = array(), $errorMsg = '', $status = 0){

        header("Content-Type: application/javascript; charset=UTF-8");
        $arrRtInfo = array();
        $arrRtInfo['status'] = $status;
        $arrRtInfo['statusInfo'] = $errorMsg;
        $arrRtInfo['data']= $arrData;
        
        $strJsonRet = json_encode($arrRtInfo);
        $strJsonRet = str_replace("<","&lt;",$strJsonRet);
        $strJsonRet = str_replace(">","&gt;",$strJsonRet);
        if($callback){
            echo $callback.'('.$strJsonRet.');';
        }else{
            echo $strJsonRet;
        }
        die();
    }
   
    public function ajaxError($errorCode = Base_RetCode::UNKNOWN_ERROR, $errorMsg = '', $arrData = array()) {

        header("Content-Type: application/json; charset=UTF-8");
        $arrRtInfo = array();
        $arrRtInfo['status'] = $errorCode;
        $arrRtInfo['statusInfo'] = empty($errorMsg) ? Base_RetCode::getMsg($errorCode) : $errorMsg;
        $arrRtInfo['data']= $arrData;

        $output = json_encode($arrRtInfo);
        echo $output;
        die();
    }

    public function jsonpError($callback = '', $errorCode = Base_RetCode::UNKNOWN_ERROR, $errorMsg = '', $arrData = array()) {

        header("Content-Type: application/javascript; charset=UTF-8");
        $arrRtInfo = array();
        $arrRtInfo['status'] = $errorCode;
        $arrRtInfo['statusInfo'] = empty($errorMsg) ? Base_RetCode::getMsg($errorCode) : $errorMsg;
        $arrRtInfo['data']= $arrData;
        $strJsonRet = json_encode($arrRtInfo);
        if($callback){
            echo $callback.'('.$strJsonRet.');';
        }else{
            echo $strJsonRet;
        }
        die();
    }

    public function redirect($url){
        parent::redirect($url);
        exit;
    }

    /** 
     * 前端跳转
     * @param   $url jump url
     */
    public function ajaxJump($url){
        header("Content-Type: application/json; charset=UTF-8"); 
        $arrRtInfo               = array();
        $arrRtInfo['status']     = Base_RetCode::NEED_REDIRECT;
        $arrRtInfo['statusInfo'] = Base_RetCode::getMsg(Base_RetCode::NEED_REDIRECT);
        $arrRtInfo['data']       = array('url' => $url);
        
        $output = json_encode($arrRtInfo);
        echo $output;
        die();
    }
}
