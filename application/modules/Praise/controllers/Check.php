<?php
/**
 * 检验是否点赞过接口
 * @author huwei
 *
 */
class CheckController extends Base_Controller_Api {

    const HAS_PRAISED = 0;
    
    const NOT_PRAISED = 1;
    
    protected $_logicPraise;
    
    public function init() {
        $this->_logicPraise = new Praise_Logic_Praise();
        $this->setNeedLogin(false);
        parent::init();        
    }
    
    /**
     * 接口1：/praise/check
     * 检验是否点赞过
     * @param integer type，点赞内容的类型，默认是答案 ，可以不传参
     * @param integer deviceId，设备ID
     * @param integer objId，对象ID
     * @return json,data 0：已经点过赞，1:还未点过赞
     */
    public function indexAction() {
       $type     = isset($_POST['type'])?intval($_POST['type']):Praise_Type::ANSWER;
       $deviceId = isset($_POST['deviceId'])?intval($_POST['deviceId']):0;
       $objId    = isset($_POST['objId'])?intval($_POST['objId']):0;
       if(empty($deviceId) || empty($objId)){
           return $this->ajaxError();
       }
       $ret = $this->_logicPraise->checkPraise($deviceId,$objId,$type);
       if($ret){
           return $this->ajax(self::HAS_PRAISED);
       }
       return $this->ajax(self::NOT_PRAISED);
    }          
}