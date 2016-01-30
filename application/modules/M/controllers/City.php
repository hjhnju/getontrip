<?php
/**
 * 附近景点
 * @author fyy
 */
class CityController extends Base_Controller_Page {
    
    public function init() {
        $this->setNeedLogin(false);

        parent::init();
    }

   /**
     *  城市维度的 列表页面
    */
    public function indexAction() {   
        
        $cityId   = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;

        $cityInfo = City_Api::getCityById($cityId); 

        //当前城市下的所有标签
        $logic      = new Destination_Logic_Tag();
        $tags   = $logic->getTagsByCity($cityId,'1.1'); 
        $tagId   = !empty($_REQUEST['tagId'])?$_REQUEST['tagId']:$tags[0]['id'];
        //$tagId = empty($_COOKIE['tagId'])?$tagId:$_COOKIE['tagId']; 
         
        //$tagId   = 'landscape'; 
        
         
        $this->getView()->assign('title', $cityInfo['name']); 
        $this->getView()->assign('city', $cityInfo); 
        $this->getView()->assign('tagId', $tagId);   
        $this->getView()->assign('tags', $tags);    
    }
 
}
