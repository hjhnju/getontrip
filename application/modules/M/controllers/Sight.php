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
     *  语音导游列表页面
    */
    public function guideAction() {   
        
        $sight_id   = isset($_REQUEST['id'])?intval($_REQUEST['id']):0; 
        $tagId   = 'landscape';  
        $sightInfo = Sight_Api::getSightById($sight_id); 

         
        $this->getView()->assign('title', $sightInfo['name']); 
        $this->getView()->assign('sight', $sightInfo);   
        $this->getView()->assign('tagId', $tagId);  
    }

    /**
     *  美食列表页面
    */
    public function foodlistAction() {   
        
        $sight_id   = isset($_REQUEST['id'])?intval($_REQUEST['id']):0; 
        $tagId   = 'food';  
        $sightInfo = Sight_Api::getSightById($sight_id); 

         
        $this->getView()->assign('title', $sightInfo['name']); 
        $this->getView()->assign('sight', $sightInfo);  
        $this->getView()->assign('tagId', $tagId);   
    }

     /**
     *  特产列表页面
    */
    public function specialtylistAction() {   
        
        $sight_id   = isset($_REQUEST['id'])?intval($_REQUEST['id']):0; 
        $tagId   = 'specialty';  
        $sightInfo = Sight_Api::getSightById($sight_id); 

         
        $this->getView()->assign('title', $sightInfo['name']); 
        $this->getView()->assign('sight', $sightInfo);  
        $this->getView()->assign('tagId', $tagId);   
    }

    /**
     *  话题列表页面
    */
    public function topiclistAction() {   
        
        $sight_id   = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;
        $tagId   = !empty($_REQUEST['tagId'])?$_REQUEST['tagId']:0;  
        $sightInfo = Sight_Api::getSightById($sight_id); 

         
        $this->getView()->assign('title', $sightInfo['name']); 
        $this->getView()->assign('sight', $sightInfo); 
        $this->getView()->assign('tagId', $tagId);   
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

    /**
     *  美食详情页面
    */
    public function foodAction() {  
        $id = isset($_REQUEST['id'])? intval($_REQUEST['id']) : 0;
        if(empty($id)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $pageSize = 4;
        $logicFood    = new Food_Logic_Food();
        $food         = $logicFood->getFoodInfo($id, 1, $pageSize);

        //遍历
        for ($i=0; $i < count($food['shops']) ; $i++) { 
             $shop = $food['shops'][$i];
             $floor  = floor ($shop['score']);
             $star_all= array();
             if ($floor==$shop['score']) { 
                $shop['star_half'] = 0;
             }else{ 
                $shop['star_half'] = 1;
             }
             for ($j=0; $j < $floor; $j++) { 
                array_push($star_all,$j);
             }
             $shop['star_all'] = $star_all;
             $food['shops'][$i] = $shop;
        }
 
        $this->getView()->assign('title', $food['title']); 
        $this->getView()->assign('post', $food);
        $this->getView()->assign('pageSize', $pageSize);   

    }

    /**
     *  特产详情页面
    */
    public function specialtyAction() {   
        $id = isset($_REQUEST['id'])? intval($_REQUEST['id']) : 0;
        if(empty($id)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $pageSize = 4;
        $logicSpecialty    = new Specialty_Logic_Specialty();
        $specialty          = $logicSpecialty->getSpecialtyInfo($id, 1, $pageSize); 
        
        $this->getView()->assign('title', $specialty['title']); 
        $this->getView()->assign('post', $specialty); 
        $this->getView()->assign('pageSize', $pageSize);  
    }
}
