<?php
/**
 * 景点详情接口
 * @author huwei
 *
 */
class ApiController extends Base_Controller_Api {
    
    const PAGESIZE = 6;
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();        
    }
    
    /**
     * 接口1：/api/sight/detail
     * 景点详情接口
     * @param integer sightId
     * @param integer page
     * @param integer pageSize
     * @param string tags:逗号隔开的id串，如："1,2"。
     * 对于用户点击通用标签的时候，不要传景点ID，只传通用标签ID及页码信息。
     * 对于用户点击书籍标签，视频标签，景观标签，分别调用书籍模块，景观模块，视频模块的接口。
     * @return json
     */
    public function detailAction() {
        $page       = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize   = isset($_REQUEST['pageSize'])?intval($_REQUEST['pageSize']):self::PAGESIZE;
        $sightId    = isset($_REQUEST['sightId'])?intval($_REQUEST['sightId']):'';
        $strTags    = isset($_REQUEST['tags'])?trim($_REQUEST['tags']):'';
        $intOrder   = isset($_REQUEST['order'])?intval($_REQUEST['order']):2;
        if(empty($sightId) && empty($strTags)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logic      = new Sight_Logic_Sight();
        $ret        = $logic->getSightDetail($sightId,$page,$pageSize,$intOrder,$strTags);
        $this->ajax($ret);
    }
    
    /**
     * 接口2：获取景点列表 /api/sight/list
     * @param integer $page
     * @param integer $pageSize
     * @param integer $cityId
     * @return array
     */
    public function listAction() {
        $page     = isset($_REQUEST['page'])?$_REQUEST['page']:1;
        $pageSize = isset($_REQUEST['pageSize'])?$_REQUEST['pageSize']:self::PAGESIZE;
        $cityId   = isset($_REQUEST['cityId'])?$_REQUEST['cityId']:'';
        if(!empty($cityId)){
            $ret  =  Sight_Api::getSightByCity($cityId,$page,$pageSize);
        }else{           
            $ret  =  Sight_Api::getSightList($page, $pageSize, Sight_Type_Status::PUBLISHED);
        }   
        $this->ajax($ret['list']);
    }
}
