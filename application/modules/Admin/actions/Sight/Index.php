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
    	
    	//处理传递过来的景点
    	$city_id  = isset($_REQUEST['id'])?intval($_REQUEST['id']):'';
    	if($city_id!=''){;
    	    $arrCity = City_Api::getCityById($city_id);
    	    if(empty($arrCity)){
    	        $this->getView()->assign('city', '');
    	    }
    	    $this->getView()->assign('city', $arrCity);
    	}
    }
}
