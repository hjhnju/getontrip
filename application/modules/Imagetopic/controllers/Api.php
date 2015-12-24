<?php
/**
 * 图文页接口
 * @author huwei
 *
 */
class ApiController extends Base_Controller_Api {
    
    const DETAULT_ORDER = 1;
    
    const PAGESIZE     = 6;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();        
    }
    
    /**
     * 接口1：/api/1.0/imagetopic
     * 图文详情页接口
     * @param integer id，图文ID
     * @return json
     */
    public function indexAction() {
        $imagetopicId    = isset($_REQUEST['id'])?intval($_REQUEST['id']):'';
        if(empty($imagetopicId)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }   
        $logic = new Imagetopic_Logic_Imagetopic();
        $ret   = $logic->detail($imagetopicId);     
        $this->ajaxDecode($ret);
    }  
    
    /**
     * 接口2：/api/1.0/imagetopic/add
     * 添加图文接口
     * @param integer sightId, 景点ID
     * @param string  title, 图文标题
     * @param string  content,图文内容
     * @param file    image,  图文图片
     * @return json
     */
    public function addAction() {
        $sightId    = isset($_REQUEST['sightId'])?intval($_REQUEST['sightId']):'';
        $title      = isset($_REQUEST['title'])?trim($_REQUEST['title']):'';
        $content    = isset($_REQUEST['content'])?trim($_REQUEST['content']):'';
        $image      = isset($_FILES['image'])?$_FILES['image']:'';
        if(empty($sight) || empty($image)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logic = new Imagetopic_Logic_Imagetopic();
        $ret   = $logic->add($sightId, $title, $content, $image);
        if($ret){
            return $this->ajaxError($ret,Base_RetCode::getMsg($ret));
        }
        return $this->ajax($ret);
    }
    
    /**
     * 接口3：/api/1.0/imagetopic/list
     * 图文列表页接口:包括最新和热门
     * @param integer sightId,景点ID
     * @param integer order,类型,1:最新,2:热门
     * @return json
     */
    public function listAction() {
        $sightId    = isset($_REQUEST['sightId'])?intval($_REQUEST['sightId']):'';
        $order      = isset($_REQUEST['order'])?intval($_REQUEST['order']):self::DETAULT_ORDER;
        $page       = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize   = isset($_REQUEST['pageSize'])?intval($_REQUEST['pageSize']):self::PAGESIZE;
        if(empty($sightId)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logic      = new Imagetopic_Logic_Imagetopic();
        $ret        = $logic->getList($sightId, $order, $page, $pageSize);
        $this->ajaxDecode($ret);
    }
}