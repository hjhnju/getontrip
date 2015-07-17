<?php
/**
 * 点赞数据接口
 * @author huwei
 *
 */
class IndexController extends Base_Controller_Api {

    protected $_logicPraise;
    
    public function init() {
        $this->_logicPraise = new Praise_Logic_Praise();
        $this->setNeedLogin(false);
        parent::init();        
    }
    
    /**
     * 接口1：/praise
     */
    public function indexAction() {
       $type     = isset($_POST['type'])?intval($_POST['type']):Praise_Type::ANSWER;
       $deviceId = isset($_POST['deviceId'])?intval($_POST['deviceId']):0;
       $objId    = isset($_POST['objId'])?intval($_POST['objId']):0;
       if(empty($deviceId) || empty($objId)){
           return $this->ajaxError();
       }
       $ret = $this->_logicPraise->addPraise($deviceId,$objId,$type);
       if($ret){
           return $this->ajax();
       }
       return $this->ajaxError();
    }          
}