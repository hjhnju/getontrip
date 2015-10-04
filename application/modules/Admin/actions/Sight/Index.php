<?php
/**
 * 景点管理
 * @author fyy
 *
 */
class IndexAction extends Yaf_Action_Abstract {
    
    public function execute() {
        $statusTypeArray=Sight_Type_Status::$names;
        $statusTypeArray=array_reverse($statusTypeArray,true);
    	$this->getView()->assign('statusTypeArray', $statusTypeArray); 

    	//处理传递过来的城市
        $city_id  = isset($_REQUEST['city_id'])?intval($_REQUEST['city_id']):'';
        if($city_id!=''){ 
           $city=City_Api::getCityById($city_id);  
           $this->getView()->assign('city', $city);
        }
    }
}
