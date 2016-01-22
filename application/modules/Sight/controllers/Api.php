<?php
/**
 * 景点页接口
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
     * 接口1：/api/sight
     * 景点接口
     * @param integer sightId
     * 获取景点的标签，名称，图像信息。
     * @return json
     */
    public function indexAction() {
        $sightId    = isset($_REQUEST['sightId'])?intval($_REQUEST['sightId']):'';
        if(empty($sightId)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $sight      = Sight_Api::getSightById($sightId);
        $logic      = new Sight_Logic_Tag();
        $arrRet['id']     = strval($sight['id']);
        $arrRet['cityid'] = strval($sight['city_id']);
        $arrRet['name']   = $sight['name'];
        $arrRet['image']  = Base_Image::getUrlByName($sight['image']);
        $logicCollect     = new Collect_Logic_Collect();
        $ret              = $logicCollect->checkCollect(Collect_Type::SIGHT, $sightId);
        $arrRet['isfav']  = $ret?"1":"0";
        $arrRet['tags']   = $logic->getTagsBySight($sightId,$this->getVersion());
        $this->ajax($arrRet);
    }
    
    /**
     * 接口2：/api/sight/topic
     * 景点话题接口
     * @param integer sightId
     * @param integer page
     * @param integer pageSize
     * @param string tags:逗号隔开的id串，如："1,2"。
     * 获取景点的标签下的话题
     * @return json
     */
    public function topicAction() {
        $page       = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize   = isset($_REQUEST['pageSize'])?intval($_REQUEST['pageSize']):self::PAGESIZE;
        $sightId    = isset($_REQUEST['sightId'])?intval($_REQUEST['sightId']):'';
        $strTags    = isset($_REQUEST['tags'])?trim($_REQUEST['tags']):'';
        $intOrder   = isset($_REQUEST['order'])?intval($_REQUEST['order']):1;
        if((empty($sightId) || empty($strTags))){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
    
        //增加访问统计
        $logicVisit = new Tongji_Logic_Visit();
        $logicVisit->addVisit(Tongji_Type_Visit::SIGHT, $sightId);
    
        $logic      = new Sight_Logic_Sight();
        $ret        = $logic->getSightDetail($sightId,$page,$pageSize,$intOrder,$strTags);
        $this->ajaxDecode($ret);
    }
    
    /**
     * 接口3：/api/sight/video
     * 视频列表接口
     * @param integer page
     * @param integer pageSize
     * @param integer sightId,景点ID
     * @return json
     */
    public function videoAction() {
        $page       = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize   = isset($_REQUEST['pageSize'])?intval($_REQUEST['pageSize']):self::PAGESIZE;
        $sightId    = isset($_REQUEST['sightId'])?intval($_REQUEST['sightId']):'';
        if(empty($sightId)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logic      = new Video_Logic_Video();
        $ret        = $logic->getVideoList($sightId,$page,$pageSize,array('status' => Video_Type_Status::PUBLISHED));
        $this->ajax($ret);
    }
    
    /**
     * 接口4：/api/sight/book
     * 书籍列表接口
     * @param integer page
     * @param integer pageSize
     * @param integer sightId,景点ID
     * @return json
     */
    public function bookAction() {
        $page       = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize   = isset($_REQUEST['pageSize'])?intval($_REQUEST['pageSize']):self::PAGESIZE;
        $sightId    = isset($_REQUEST['sightId'])?intval($_REQUEST['sightId']):'';
        if(empty($sightId)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logic      = new Book_Logic_Book();
        $ret        = $logic->getBookList($sightId,$page,$pageSize,array('status' => Book_Type_Status::PUBLISHED));
        $this->ajaxDecode($ret);
    }
    
    /**
     * 接口5：/api/sight/landscape
     * 景观列表接口
     * @param integer page
     * @param integer pageSize
     * @param integer sightId,景点ID
     * @return json
     */
    public function landscapeAction() {
        $page       = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize   = isset($_REQUEST['pageSize'])?intval($_REQUEST['pageSize']):self::PAGESIZE;
        $sightId    = isset($_REQUEST['sightId'])?intval($_REQUEST['sightId']):'';
        $x          = isset($_REQUEST['x'])?doubleval($_REQUEST['x']):'';
        $y          = isset($_REQUEST['y'])?doubleval($_REQUEST['y']):'';
        if(empty($sightId)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logic      = new Keyword_Logic_Keyword();
        $ret        = $logic->getKeywordList($sightId,$x,$y,$page,$pageSize,array('status' => Keyword_Type_Status::PUBLISHED));
        $this->ajax($ret);
    }
    
    /**
     * 接口6:/api/1.0/sight/food
     * 美食列表接口
     * @param integer page
     * @param integer pageSize
     * @param integer sightId,景点ID
     * @return json
     */
    public function foodAction(){
        $page       = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize   = isset($_REQUEST['pageSize'])?intval($_REQUEST['pageSize']):self::PAGESIZE;
        $sightId    = isset($_REQUEST['sightId'])?intval($_REQUEST['sightId']):'';
        if(empty($sightId)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logic      = new Food_Logic_Food();
        $ret        = $logic->getFoodList($sightId,Destination_Type_Type::SIGHT,$page,$pageSize,array('status' => Food_Type_Status::PUBLISHED));
        $this->ajax($ret);
    }
    
    /**
     * 接口7:/api/1.0/sight/specialty
     * 特产列表接口
     * @param integer page
     * @param integer pageSize
     * @param integer sightId,景点ID
     * @return json
     */
    public function specialtyAction(){
        $page       = isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $pageSize   = isset($_REQUEST['pageSize'])?intval($_REQUEST['pageSize']):self::PAGESIZE;
        $sightId    = isset($_REQUEST['sightId'])?intval($_REQUEST['sightId']):'';
        if(empty($sightId)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logic      = new Specialty_Logic_Specialty();
        $ret        = $logic->getSpecialtyList($sightId,Destination_Type_Type::SIGHT, $page,$pageSize,array('status' => Specialty_Type_Status::PUBLISHED));
        $this->ajax($ret);
    }
}
