<?php
/**
 * 附近景点
 * @author fyy
 */
class SightController extends Base_Controller_Page {
    
     
   
    public function init() {
        $this->setNeedLogin(false);
        parent::init();
    }
    
    /**
     *  附近景点
     */
    public function nearbyAction(){
 
      $this->getView()->assign('title', '附近景点'); 
    }
    
     /**
     *  景观地图页面
     */
    public function mapAction() {             
       $postid = isset($_REQUEST['id'])? intval($_REQUEST['id']) : 0;
       $deviceId   = isset($_REQUEST['deviceId'])?trim($_REQUEST['deviceId']):'';
       $sight_id   = isset($_REQUEST['sightId'])?trim($_REQUEST['sightId']):7; 
       $arrInfo = array('status'=>Keyword_Type_Status::PUBLISHED);
       $arrInfo['sight_id'] = $sight_id;

       $sightInfo = Sight_Api::getSightById($sight_id);
         
       $List =Keyword_Api::queryKeywords(1,PHP_INT_MAX,$arrInfo);

       
       
       $this->getView()->assign('list', json_encode($List['list']));
       $this->getView()->assign('sight', $sightInfo); 
    }

    /**
     *  语音导游页面
    */
    public function guideAction() {   

        $sight_id   = isset($_REQUEST['id'])?intval($_REQUEST['id']):0; 
        $sightInfo = Sight_Api::getSightById($sight_id); 

         
        $this->getView()->assign('title', $sightInfo['name']); 
        $this->getView()->assign('sight', $sightInfo); 
    }

    
    /**
     *  景观详情页面
    */
    public function landscapeAction() {   

        $id   = isset($_REQUEST['id'])?intval($_REQUEST['id']):0; 
        $landscape = Keyword_Api::queryById($id); 
       
         
        $this->getView()->assign('landscape', $landscape); 
        $this->getView()->assign('title', $landscape['name']); 
    }
}
